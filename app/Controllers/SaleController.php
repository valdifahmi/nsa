<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockOutModel;
use App\Models\StockOutItemModel;
use App\Models\ProductModel;

class SaleController extends BaseController
{
    protected $stockOutModel;
    protected $stockOutItemModel;
    protected $productModel;
    protected $db;

    public function __construct()
    {
        $this->stockOutModel = new StockOutModel();
        $this->stockOutItemModel = new StockOutItemModel();
        $this->productModel = new ProductModel();
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
     * Store new sale transaction (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'tanggal_keluar' => 'required',
            'penerima' => 'permit_empty|max_length[255]',
            'catatan' => 'permit_empty|max_length[1000]',
            'items' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Get items from JSON
        $itemsJson = $this->request->getPost('items');
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cart is empty. Please add at least one item.'
            ]);
        }

        // Validate tanggal_keluar format
        $tanggal_keluar = $this->request->getPost('tanggal_keluar');
        if (!$tanggal_keluar) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tanggal keluar is required'
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

        // Generate transaction number
        $nomor_transaksi = $this->generateTransactionNumber();

        // Start database transaction
        $this->db->transBegin();

        try {
            // VALIDASI STOK - CRITICAL!
            // Cek semua item sebelum menyimpan
            foreach ($items as $item) {
                if (!isset($item['product_id']) || !isset($item['jumlah'])) {
                    throw new \Exception('Invalid item data');
                }

                $product = $this->productModel->find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['product_id']);
                }

                // Validasi stok tersedia
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
                'user_id' => $userId,
                'tanggal_keluar' => $tanggal_keluar . ' ' . date('H:i:s'),
                'penerima' => $this->request->getPost('penerima') ?: null,
                'catatan' => $this->request->getPost('catatan') ?: null
            ];

            $insertResult = $this->stockOutModel->insert($headerData);

            // Check if insert failed
            if ($insertResult === false) {
                $errors = $this->stockOutModel->errors();
                throw new \Exception('Failed to insert stock out header: ' . json_encode($errors));
            }

            $stock_out_id = $this->stockOutModel->getInsertID();

            if (!$stock_out_id || $stock_out_id == 0) {
                throw new \Exception('Failed to get stock_out_id after insert');
            }

            // Insert items and update stock
            foreach ($items as $item) {
                // Insert to tb_stock_out_items
                $itemData = [
                    'stock_out_id' => $stock_out_id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah']
                ];

                $this->stockOutItemModel->insert($itemData);

                // Update stock in tb_products (KURANGI STOK)
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $newStock = $product['stok_saat_ini'] - $item['jumlah'];

                    // Pastikan stok tidak negatif
                    if ($newStock < 0) {
                        throw new \Exception('Stock calculation error for product: ' . $product['nama_barang']);
                    }

                    $this->productModel->update($item['product_id'], [
                        'stok_saat_ini' => $newStock
                    ]);
                }
            }

            // Commit transaction
            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Sale transaction saved successfully',
                'nomor_transaksi' => $nomor_transaksi
            ]);
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            // Log error for debugging
            log_message('error', 'Sale store error: ' . $e->getMessage());

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

        // Get last transaction number for today
        $lastTransaction = $this->stockOutModel
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
