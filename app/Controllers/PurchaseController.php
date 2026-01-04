<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockInModel;
use App\Models\StockInItemModel;
use App\Models\ProductModel;
use App\Models\SupplierModel;
use App\Models\LogModel;

class PurchaseController extends BaseController
{
    protected $stockInModel;
    protected $stockInItemModel;
    protected $productModel;
    protected $supplierModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->stockInModel = new StockInModel();
        $this->stockInItemModel = new StockInItemModel();
        $this->productModel = new ProductModel();
        $this->supplierModel = new SupplierModel();
        $this->logModel = new LogModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Display the purchase (stock in) page
     */
    public function index()
    {
        return view('Purchase/index');
    }

    /**
     * Store new purchase transaction (AJAX)
     * NEW LOGIC: Hidden pricing + auto-capture from product
     */
    public function store()
    {
        // Get JSON input
        $input = $this->request->getJSON();

        // Validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'supplier_id' => 'required|integer',
            'tanggal_masuk' => 'required',
            'items' => 'required'
        ]);

        if (!$validation->run((array)$input)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Check if items array is empty
        if (empty($input->items)) {
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

        // Verify supplier exists
        $supplier = $this->supplierModel->find($input->supplier_id);
        if (!$supplier) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier not found'
            ]);
        }

        // Start database transaction
        $this->db->transStart();

        try {
            // Generate transaction number
            $nomor_transaksi = $this->generateTransactionNumber();

            // 1. Insert header to tb_stock_in
            $headerData = [
                'nomor_transaksi' => $nomor_transaksi,
                'supplier_id' => $input->supplier_id,
                'user_id' => $userId,
                'tanggal_masuk' => $input->tanggal_masuk,
                'catatan' => $input->catatan ?? null
            ];

            $this->stockInModel->insert($headerData);
            $stock_in_id = $this->stockInModel->getInsertID();

            if (!$stock_in_id) {
                throw new \Exception('Failed to create stock in header');
            }

            // 2. Process each item
            foreach ($input->items as $item) {
                // Validate item data
                if (!isset($item->product_id) || !isset($item->jumlah)) {
                    throw new \Exception('Invalid item data');
                }

                // Get product data (including current price)
                $product = $this->productModel->find($item->product_id);
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item->product_id);
                }

                // 3. Insert to tb_stock_in_items with HISTORICAL PRICE
                $itemData = [
                    'stock_in_id' => $stock_in_id,
                    'product_id' => $item->product_id,
                    'jumlah' => $item->jumlah,
                    'harga_beli_satuan' => $product['harga_beli_saat_ini'] ?? 0 // AUTO from product
                ];

                $this->stockInItemModel->insert($itemData);

                // 4. Update stock in tb_products
                $newStock = $product['stok_saat_ini'] + $item->jumlah;
                $this->productModel->update($item->product_id, [
                    'stok_saat_ini' => $newStock
                ]);

                // 5. Log activity
                $this->logModel->insert([
                    'user_id' => $userId,
                    'action' => 'CREATE',
                    'module' => 'Stock In',
                    'log_message' => "Barang masuk: {$product['nama_barang']} ({$item->jumlah} {$product['satuan']}) - {$nomor_transaksi}"
                ]);
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log main transaction
            $this->logModel->insert([
                'user_id' => $userId,
                'action' => 'CREATE',
                'module' => 'Stock In',
                'log_message' => "Transaksi barang masuk berhasil: {$nomor_transaksi} - Supplier: {$supplier['nama_supplier']}"
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Transaksi barang masuk berhasil disimpan',
                'nomor_transaksi' => $nomor_transaksi
            ]);
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            // Log error
            log_message('error', 'Purchase store error: ' . $e->getMessage());
            $this->logModel->insert([
                'user_id' => $userId,
                'action' => 'ERROR',
                'module' => 'Stock In',
                'log_message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ]);

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Find product by barcode/code (AJAX)
     */
    public function findProductByCode()
    {
        $code = $this->request->getGet('code');

        if (!$code) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Code is required'
            ]);
        }

        $product = $this->productModel->findByCode($code);

        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $product
        ]);
    }

    /**
     * Search products for autocomplete (AJAX)
     * Returns products with image field
     */
    public function searchProducts()
    {
        $keyword = $this->request->getGet('q');

        if (!$keyword) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => []
            ]);
        }

        // Get products with image field
        $products = $this->productModel
            ->select('id, kode_barang, nama_barang, satuan, stok_saat_ini, min_stok, image')
            ->groupStart()
            ->like('kode_barang', $keyword)
            ->orLike('nama_barang', $keyword)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Generate transaction number
     * Format: SI-YYYYMMDD-XXXX
     */
    private function generateTransactionNumber()
    {
        $date = date('Ymd');
        $prefix = 'SI-' . $date . '-';

        // Get last transaction number for today
        $lastTransaction = $this->stockInModel
            ->like('nomor_transaksi', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastTransaction) {
            // Extract the sequence number
            $lastNumber = (int) substr($lastTransaction['nomor_transaksi'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format with leading zeros
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
