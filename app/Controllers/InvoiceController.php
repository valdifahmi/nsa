<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockOutModel;
use App\Models\StockOutItemModel;
use App\Models\StockOutServiceModel;
use App\Models\ClientModel;
use App\Models\ProductModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceController extends BaseController
{
    protected $stockOutModel;
    protected $stockOutItemModel;
    protected $stockOutServiceModel;
    protected $clientModel;
    protected $productModel;

    public function __construct()
    {
        $this->stockOutModel = new StockOutModel();
        $this->stockOutItemModel = new StockOutItemModel();
        $this->stockOutServiceModel = new StockOutServiceModel();
        $this->clientModel = new ClientModel();
        $this->productModel = new ProductModel();
        helper(['form', 'url']);
    }

    /**
     * Display invoice list page
     */
    public function index()
    {
        $data = [
            'title' => 'Daftar Invoice'
        ];

        return view('Invoice/index', $data);
    }

    /**
     * Fetch all invoices for DataTables (AJAX)
     */
    public function fetchInvoices()
    {
        try {
            // Get all stock out transactions with client info
            $builder = $this->stockOutModel->builder();
            $invoices = $builder
                ->select('tb_stock_out.*, tb_clients.nama_klien as nama_client')
                ->join('tb_clients', 'tb_clients.id = tb_stock_out.client_id', 'left')
                ->orderBy('tb_stock_out.id', 'DESC')
                ->get()
                ->getResultArray();

            log_message('info', 'Invoices found: ' . count($invoices));

            // Calculate total for each invoice
            foreach ($invoices as &$invoice) {
                // Get items for this invoice
                $items = $this->stockOutItemModel
                    ->where('stock_out_id', $invoice['id'])
                    ->findAll();

                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += ($item['jumlah'] * $item['harga_jual_satuan']);
                }

                // Calculate tax and grand total
                $ppn_persen = $invoice['ppn_persen'] ?? 11;
                $total_pajak = $subtotal * ($ppn_persen / 100);
                $grand_total = $subtotal + $total_pajak;

                $invoice['subtotal'] = $subtotal;
                $invoice['total_pajak'] = $total_pajak;
                $invoice['grand_total'] = $grand_total;
            }

            log_message('info', 'Returning ' . count($invoices) . ' invoices');

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            log_message('error', 'fetchInvoices error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error fetching invoices: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get invoice detail (AJAX) - Updated to include services
     */
    public function getDetail($id)
    {
        try {
            // Get header
            $builder = $this->stockOutModel->builder();
            $invoice = $builder
                ->select('tb_stock_out.*, tb_clients.nama_klien as nama_client, tb_clients.alamat as alamat_client')
                ->join('tb_clients', 'tb_clients.id = tb_stock_out.client_id', 'left')
                ->where('tb_stock_out.id', $id)
                ->get()
                ->getRowArray();

            if (!$invoice) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invoice not found'
                ]);
            }

            // Get items
            $items = $this->stockOutItemModel
                ->select('tb_stock_out_items.*, tb_products.nama_barang, tb_products.kode_barang')
                ->join('tb_products', 'tb_products.id = tb_stock_out_items.product_id')
                ->where('stock_out_id', $id)
                ->findAll();

            // Get services (if Workshop) - with JOIN to get nama_jasa
            $db = \Config\Database::connect();
            $services = $db->table('tb_stock_out_services as sos')
                ->select('sos.id, sos.stock_out_id, sos.service_id, sos.jumlah, sos.biaya_jasa, sos.pph_persen, s.nama_jasa')
                ->join('tb_services as s', 's.id = sos.service_id', 'left')
                ->where('sos.stock_out_id', $id)
                ->get()
                ->getResultArray();

            // Use grand_total from database (already calculated with dynamic tax)
            $grand_total = $invoice['grand_total'];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'invoice' => $invoice,
                    'items' => $items,
                    'services' => $services,
                    'grand_total' => $grand_total
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate PDF invoice - Updated to include services
     */
    public function generatePDF($id)
    {
        try {
            log_message('info', 'Starting PDF generation for ID: ' . $id);

            // Get header
            $builder = $this->stockOutModel->builder();
            $invoice = $builder
                ->select('tb_stock_out.*, tb_clients.nama_klien as nama_client, tb_clients.alamat as alamat_client')
                ->join('tb_clients', 'tb_clients.id = tb_stock_out.client_id', 'left')
                ->where('tb_stock_out.id', $id)
                ->get()
                ->getRowArray();

            log_message('info', 'Invoice data: ' . json_encode($invoice));

            if (!$invoice) {
                log_message('error', 'Invoice not found for ID: ' . $id);
                die('Invoice not found');
            }

            // Get items
            $items = $this->stockOutItemModel
                ->select('tb_stock_out_items.*, tb_products.nama_barang, tb_products.kode_barang, tb_products.satuan')
                ->join('tb_products', 'tb_products.id = tb_stock_out_items.product_id')
                ->where('stock_out_id', $id)
                ->findAll();

            log_message('info', 'Items found: ' . count($items));

            // Get services (if Workshop) - with JOIN to get nama_jasa
            $db = \Config\Database::connect();
            $services = $db->table('tb_stock_out_services as sos')
                ->select('sos.id, sos.stock_out_id, sos.service_id, sos.jumlah, sos.biaya_jasa, sos.pph_persen, s.nama_jasa')
                ->join('tb_services as s', 's.id = sos.service_id', 'left')
                ->where('sos.stock_out_id', $id)
                ->get()
                ->getResultArray();

            log_message('info', 'Services found: ' . count($services));

            // Prepare data for view (use values from database)
            $data = [
                'invoice' => $invoice,
                'items' => $items,
                'services' => $services
            ];

            log_message('info', 'Loading view...');

            // Load view and generate HTML
            $html = view('Invoice/print_pdf', $data);

            log_message('info', 'View loaded, HTML length: ' . strlen($html));

            // Configure Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('chroot', FCPATH);

            log_message('info', 'Dompdf options configured');

            // Instantiate Dompdf
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);

            log_message('info', 'HTML loaded into Dompdf');

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            log_message('info', 'Rendering PDF...');

            // Render PDF
            $dompdf->render();

            log_message('info', 'PDF rendered successfully');

            // Output PDF to browser
            $filename = 'Invoice_' . $invoice['nomor_transaksi'] . '.pdf';

            log_message('info', 'Streaming PDF: ' . $filename);

            $dompdf->stream($filename, ['Attachment' => false]);

            log_message('info', 'PDF generation completed');
        } catch (\Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            die('Error generating PDF: ' . $e->getMessage() . '<br><br>Check logs for details.');
        }
    }
}
