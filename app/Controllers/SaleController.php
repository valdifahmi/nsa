<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockOutModel;
use App\Models\StockOutItemModel;
use App\Models\StockOutServiceModel;
use App\Models\ServiceModel;
use App\Models\ProductModel;
use App\Models\ClientModel;
use App\Models\ActivityLogModel;

class SaleController extends BaseController
{
    protected $stockOutModel;
    protected $stockOutItemModel;
    protected $stockOutServiceModel;
    protected $serviceModel;
    protected $productModel;
    protected $clientModel;
    protected $activityLogModel;
    protected $db;

    public function __construct()
    {
        $this->stockOutModel = new StockOutModel();
        $this->stockOutItemModel = new StockOutItemModel();
        $this->stockOutServiceModel = new StockOutServiceModel();
        $this->serviceModel = new ServiceModel();
        $this->productModel = new ProductModel();
        $this->clientModel = new ClientModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Display the sale (stock out) page
     */
    public function index()
    {
        return view('Sale/index');
    }

    /**
     * Display list of all transactions
     */
    public function listTransactions()
    {
        return view('Sale/list');
    }

    /**
     * Store new sale transaction with HIDDEN PRICING (AJAX)
     * Updated to support Work Order (tipe_transaksi)
     */
    public function store()
    {
        // Get JSON input
        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid JSON data'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'client_id' => 'required|integer',
            'tanggal_keluar' => 'required',
            'penerima' => 'permit_empty|max_length[255]',
            'catatan' => 'permit_empty|max_length[1000]',
            'items' => 'required',
            'tipe_transaksi' => 'permit_empty|in_list[Beli Putus,Workshop]'
        ];

        $validation->setRules($rules);

        if (!$validation->run($json)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Get items
        $items = $json['items'];

        if (empty($items)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cart is empty. Please add at least one item.'
            ]);
        }

        // Check if user is logged in
        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in. Please login first.'
            ]);
        }
        $userId = $user['id'];

        // Validate client exists
        $client = $this->clientModel->find($json['client_id']);
        if (!$client) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Client not found'
            ]);
        }

        // Generate transaction number and invoice number
        $nomor_transaksi = $this->generateTransactionNumber();
        $nomor_invoice = $this->generateInvoiceNumber();

        // Determine status based on tipe_transaksi (default: Beli Putus)
        $tipe_transaksi = $json['tipe_transaksi'] ?? 'Beli Putus';
        $status_work_order = ($tipe_transaksi === 'Workshop') ? 'Proses' : 'Selesai';

        // Start database transaction
        $this->db->transBegin();

        try {
            // VALIDASI STOK - CRITICAL!
            foreach ($items as $item) {
                if (!isset($item['product_id']) || !isset($item['jumlah'])) {
                    throw new \Exception('Invalid item data');
                }

                $product = $this->productModel->find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['product_id']);
                }

                if ($product['stok_saat_ini'] < $item['jumlah']) {
                    throw new \Exception(
                        'Stok tidak cukup untuk ' . $product['nama_barang'] .
                            '. Stok tersedia: ' . $product['stok_saat_ini'] .
                            ', diminta: ' . $item['jumlah']
                    );
                }
            }

            // Insert header to tb_stock_out
            $headerData = [
                'nomor_transaksi' => $nomor_transaksi,
                'nomor_invoice' => $nomor_invoice,
                'user_id' => $userId,
                'client_id' => $json['client_id'],
                'tanggal_keluar' => $json['tanggal_keluar'] . ' ' . date('H:i:s'),
                'penerima' => $json['penerima'] ?? null,
                'catatan' => $json['catatan'] ?? null,
                'tipe_transaksi' => $tipe_transaksi,
                'status_work_order' => $status_work_order,
                'ppn_persen' => 11,
                'pph_persen' => 2,
                'total_barang' => 0,
                'total_jasa' => 0,
                'total_ppn' => 0,
                'total_pph' => 0,
                'grand_total' => 0,
                'status_pembayaran' => 'Belum Lunas'
            ];

            $insertResult = $this->stockOutModel->insert($headerData);

            if ($insertResult === false) {
                $errors = $this->stockOutModel->errors();
                throw new \Exception('Failed to insert stock out header: ' . json_encode($errors));
            }

            $stock_out_id = $this->stockOutModel->getInsertID();

            if (!$stock_out_id || $stock_out_id == 0) {
                throw new \Exception('Failed to get stock_out_id after insert');
            }

            $totalBarang = 0;

            // Insert items and update stock
            foreach ($items as $item) {
                $product = $this->productModel->find($item['product_id']);

                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['product_id']);
                }

                $harga_beli_satuan = $product['harga_beli_saat_ini'];
                $harga_jual_satuan = $product['harga_jual_saat_ini'];

                $itemData = [
                    'stock_out_id' => $stock_out_id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli_satuan' => $harga_beli_satuan,
                    'harga_jual_satuan' => $harga_jual_satuan
                ];

                $this->stockOutItemModel->insert($itemData);

                $totalBarang += ($harga_jual_satuan * $item['jumlah']);

                $newStock = $product['stok_saat_ini'] - $item['jumlah'];

                if ($newStock < 0) {
                    throw new \Exception('Stock calculation error for product: ' . $product['nama_barang']);
                }

                $this->productModel->update($item['product_id'], [
                    'stok_saat_ini' => $newStock
                ]);
            }

            // If Beli Putus, calculate totals immediately
            if ($tipe_transaksi === 'Beli Putus') {
                $ppn = $totalBarang * (11 / 100);
                $grandTotal = $totalBarang + $ppn;

                $this->stockOutModel->update($stock_out_id, [
                    'total_barang' => $totalBarang,
                    'total_ppn' => $ppn,
                    'grand_total' => $grandTotal
                ]);
            } else {
                // Workshop: Only save total_barang
                $this->stockOutModel->update($stock_out_id, [
                    'total_barang' => $totalBarang
                ]);
            }

            // Log activity
            $this->activityLogModel->insert([
                'user_id' => $userId,
                'action' => 'CREATE',
                'module' => 'Stock Out',
                'log_message' => "Created {$tipe_transaksi} transaction {$nomor_transaksi} (Invoice: {$nomor_invoice}) for client: {$client['nama_klien']} with " . count($items) . " items. Status: {$status_work_order}"
            ]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Sale transaction saved successfully',
                'nomor_transaksi' => $nomor_transaksi,
                'nomor_invoice' => $nomor_invoice,
                'stock_out_id' => $stock_out_id,
                'tipe_transaksi' => $tipe_transaksi,
                'status_work_order' => $status_work_order
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Sale store error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================
    // WORK ORDER METHODS
    // ========================================

    /**
     * Display update work order page
     */
    public function updateWO($id)
    {
        $workOrder = $this->stockOutModel->find($id);

        if (!$workOrder) {
            return redirect()->to('/sale')->with('error', 'Work Order not found');
        }

        if ($workOrder['status_work_order'] !== 'Proses') {
            return redirect()->to('/sale')->with('error', 'Work Order sudah selesai, tidak bisa diubah');
        }

        $client = $this->clientModel->find($workOrder['client_id']);

        $items = $this->db->table('tb_stock_out_items as soi')
            ->select('soi.*, p.nama_barang, p.kode_barang, p.satuan')
            ->join('tb_products as p', 'p.id = soi.product_id')
            ->where('soi.stock_out_id', $id)
            ->get()
            ->getResultArray();

        // Get services with JOIN to get nama_jasa from tb_services
        $services = $this->db->table('tb_stock_out_services as sos')
            ->select('sos.id, sos.stock_out_id, sos.service_id, sos.jumlah, sos.biaya_jasa as harga_jasa, s.nama_jasa')
            ->join('tb_services as s', 's.id = sos.service_id', 'left')
            ->where('sos.stock_out_id', $id)
            ->get()
            ->getResultArray();

        $allServices = $this->serviceModel->findAll();

        $data = [
            'workOrder' => $workOrder,
            'client' => $client,
            'items' => $items,
            'services' => $services,
            'allServices' => $allServices
        ];

        return view('Sale/update_wo', $data);
    }

    /**
     * Get work order detail (AJAX)
     */
    public function getWorkOrderDetail($id)
    {
        $workOrder = $this->stockOutModel->find($id);

        if (!$workOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order not found'
            ]);
        }

        $client = $this->clientModel->find($workOrder['client_id']);

        $items = $this->db->table('tb_stock_out_items as soi')
            ->select('soi.*, p.nama_barang, p.kode_barang, p.satuan')
            ->join('tb_products as p', 'p.id = soi.product_id')
            ->where('soi.stock_out_id', $id)
            ->get()
            ->getResultArray();

        $services = $this->stockOutServiceModel->where('stock_out_id', $id)->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'workOrder' => $workOrder,
                'client' => $client,
                'items' => $items,
                'services' => $services
            ]
        ]);
    }

    /**
     * Add item to existing Work Order (AJAX)
     */
    public function addItemToWO()
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid JSON data'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'stock_out_id' => 'required|integer',
            'product_id' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]'
        ];

        $validation->setRules($rules);

        if (!$validation->run($json)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
        }
        $userId = $user['id'];

        $workOrder = $this->stockOutModel->find($json['stock_out_id']);
        if (!$workOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order not found'
            ]);
        }

        if ($workOrder['status_work_order'] !== 'Proses') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order sudah selesai, tidak bisa menambah barang'
            ]);
        }

        $product = $this->productModel->find($json['product_id']);
        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }

        if ($product['stok_saat_ini'] < $json['jumlah']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak cukup. Tersedia: ' . $product['stok_saat_ini']
            ]);
        }

        $this->db->transBegin();

        try {
            $harga_beli_satuan = $product['harga_beli_saat_ini'];
            $harga_jual_satuan = $product['harga_jual_saat_ini'];

            $itemData = [
                'stock_out_id' => $json['stock_out_id'],
                'product_id' => $json['product_id'],
                'jumlah' => $json['jumlah'],
                'harga_beli_satuan' => $harga_beli_satuan,
                'harga_jual_satuan' => $harga_jual_satuan
            ];

            $this->stockOutItemModel->insert($itemData);

            $newStock = $product['stok_saat_ini'] - $json['jumlah'];
            $this->productModel->update($json['product_id'], [
                'stok_saat_ini' => $newStock
            ]);

            $this->activityLogModel->insert([
                'user_id' => $userId,
                'action' => 'UPDATE',
                'module' => 'Work Order',
                'log_message' => "Added item {$product['nama_barang']} (Qty: {$json['jumlah']}) to Work Order {$workOrder['nomor_transaksi']}"
            ]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Item berhasil ditambahkan ke Work Order',
                'data' => [
                    'product_name' => $product['nama_barang'],
                    'jumlah' => $json['jumlah'],
                    'harga_jual_satuan' => $harga_jual_satuan
                ]
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Add item to WO error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add service to Work Order (AJAX)
     */
    public function addServiceToWO()
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid JSON data'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'stock_out_id' => 'required|integer',
            'service_id' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]'
        ];

        $validation->setRules($rules);

        if (!$validation->run($json)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
        }
        $userId = $user['id'];

        $workOrder = $this->stockOutModel->find($json['stock_out_id']);
        if (!$workOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order not found'
            ]);
        }

        if ($workOrder['status_work_order'] !== 'Proses') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order sudah selesai, tidak bisa menambah jasa'
            ]);
        }

        $service = $this->serviceModel->find($json['service_id']);
        if (!$service) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Service not found'
            ]);
        }

        try {
            $serviceData = [
                'stock_out_id' => $json['stock_out_id'],
                'service_id' => $json['service_id'],
                'biaya_jasa' => $service['harga_standar'],
                'jumlah' => $json['jumlah'],
                'pph_persen' => 2 // Default PPh 2%
            ];

            $this->stockOutServiceModel->insert($serviceData);

            $this->activityLogModel->insert([
                'user_id' => $userId,
                'action' => 'UPDATE',
                'module' => 'Work Order',
                'log_message' => "Added service {$service['nama_jasa']} (Qty: {$json['jumlah']}) to Work Order {$workOrder['nomor_transaksi']}"
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Service berhasil ditambahkan',
                'data' => [
                    'service_name' => $service['nama_jasa'],
                    'jumlah' => $json['jumlah'],
                    'harga_jasa' => $service['harga_standar']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Add service to WO error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Finalize Work Order - Calculate totals with DYNAMIC TAX
     */
    public function finalizeWorkOrder()
    {
        $json = $this->request->getJSON(true);

        if (!$json || !isset($json['stock_out_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
        }

        $stock_out_id = $json['stock_out_id'];

        $user = session()->get('user');
        if (!$user || !isset($user['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not logged in'
            ]);
        }
        $userId = $user['id'];

        $workOrder = $this->stockOutModel->find($stock_out_id);
        if (!$workOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order not found'
            ]);
        }

        if ($workOrder['status_work_order'] !== 'Proses') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Work Order sudah selesai'
            ]);
        }

        $this->db->transBegin();

        try {
            // Get DYNAMIC tax rates from request (allow admin to change before finalize)
            $ppn_persen = isset($json['ppn_persen']) ? floatval($json['ppn_persen']) : ($workOrder['ppn_persen'] ?? 11);
            $pph_persen = isset($json['pph_persen']) ? floatval($json['pph_persen']) : ($workOrder['pph_persen'] ?? 2);

            // Calculate total barang
            $items = $this->stockOutItemModel->where('stock_out_id', $stock_out_id)->findAll();
            $total_barang = 0;
            foreach ($items as $item) {
                $total_barang += ($item['jumlah'] * $item['harga_jual_satuan']);
            }

            // Calculate total jasa
            $services = $this->stockOutServiceModel->where('stock_out_id', $stock_out_id)->findAll();
            $total_jasa = 0;
            foreach ($services as $service) {
                $total_jasa += ($service['jumlah'] * $service['biaya_jasa']);
            }

            // Calculate taxes using DYNAMIC rates
            $total_ppn = ($total_barang * $ppn_persen) / 100;
            $total_pph = ($total_jasa * $pph_persen) / 100;

            // Grand Total = (Barang + PPN) + (Jasa - PPh)
            $grand_total = ($total_barang + $total_ppn) + ($total_jasa - $total_pph);

            $updateData = [
                'status_work_order' => 'Selesai',
                'ppn_persen' => $ppn_persen,
                'pph_persen' => $pph_persen,
                'total_barang' => $total_barang,
                'total_jasa' => $total_jasa,
                'total_ppn' => $total_ppn,
                'total_pph' => $total_pph,
                'grand_total' => $grand_total
            ];

            $this->stockOutModel->update($stock_out_id, $updateData);

            $this->activityLogModel->insert([
                'user_id' => $userId,
                'action' => 'FINALIZE',
                'module' => 'Work Order',
                'log_message' => "Finalized Work Order {$workOrder['nomor_transaksi']}. Grand Total: Rp " . number_format($grand_total, 0, ',', '.')
            ]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Work Order berhasil diselesaikan',
                'data' => [
                    'nomor_transaksi' => $workOrder['nomor_transaksi'],
                    'nomor_invoice' => $workOrder['nomor_invoice'],
                    'total_barang' => $total_barang,
                    'total_ppn' => $total_ppn,
                    'total_jasa' => $total_jasa,
                    'total_pph' => $total_pph,
                    'grand_total' => $grand_total,
                    'ppn_persen' => $ppn_persen,
                    'pph_persen' => $pph_persen
                ]
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Finalize WO error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate transaction number
     * Format: SO-YYYYMMDD-XXXX
     */
    private function generateTransactionNumber()
    {
        $date = date('Ymd');
        $prefix = 'SO-' . $date . '-';

        $lastTransaction = $this->stockOutModel
            ->like('nomor_transaksi', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction['nomor_transaksi'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate invoice number
     * Format: INV/NSA/YYYY/mm/number
     */
    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'INV/NSA/' . $year . '/' . $month . '/';

        $lastInvoice = $this->stockOutModel
            ->like('nomor_invoice', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice['nomor_invoice'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
