<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockInModel;
use App\Models\StockInItemModel;
use App\Models\ProductModel;

class PurchaseController extends BaseController
{
    protected $stockInModel;
    protected $stockInItemModel;
    protected $productModel;
    protected $db;

    public function __construct()
    {
        $this->stockInModel = new StockInModel();
        $this->stockInItemModel = new StockInItemModel();
        $this->productModel = new ProductModel();
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
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'tanggal_masuk' => 'required',
            'supplier' => 'permit_empty|max_length[255]',
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

        // Validate tanggal_masuk format
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');
        if (!$tanggal_masuk) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tanggal masuk is required'
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
            // Insert header to tb_stock_in
            $headerData = [
                'nomor_transaksi' => $nomor_transaksi,
                'user_id' => $userId,
                'tanggal_masuk' => $tanggal_masuk . ' ' . date('H:i:s'),
                'supplier' => $this->request->getPost('supplier') ?: null,
                'catatan' => $this->request->getPost('catatan') ?: null
            ];

            $insertResult = $this->stockInModel->insert($headerData);

            // Check if insert failed
            if ($insertResult === false) {
                $errors = $this->stockInModel->errors();
                throw new \Exception('Failed to insert stock in header: ' . json_encode($errors));
            }

            $stock_in_id = $this->stockInModel->getInsertID();

            if (!$stock_in_id || $stock_in_id == 0) {
                throw new \Exception('Failed to get stock_in_id after insert. Insert result: ' . var_export($insertResult, true));
            }

            // Insert items and update stock
            foreach ($items as $item) {
                // Validate item data
                if (!isset($item['product_id']) || !isset($item['jumlah'])) {
                    throw new \Exception('Invalid item data');
                }

                // Insert to tb_stock_in_items
                $itemData = [
                    'stock_in_id' => $stock_in_id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah']
                ];

                $this->stockInItemModel->insert($itemData);

                // Update stock in tb_products
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $newStock = $product['stok_saat_ini'] + $item['jumlah'];
                    $this->productModel->update($item['product_id'], [
                        'stok_saat_ini' => $newStock
                    ]);
                }
            }

            // Commit transaction
            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Purchase transaction saved successfully',
                'nomor_transaksi' => $nomor_transaksi
            ]);
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();

            // Log error for debugging
            log_message('error', 'Purchase store error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
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
