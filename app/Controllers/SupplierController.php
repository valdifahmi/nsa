<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\LogModel;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $logModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->logModel = new LogModel();
    }

    /**
     * Display supplier list page
     */
    public function index()
    {
        $data = [
            'title' => 'Data Supplier'
        ];
        return view('Supplier/index', $data);
    }

    /**
     * Fetch all suppliers (AJAX)
     */
    public function fetchSuppliers()
    {
        $suppliers = $this->supplierModel->orderBy('id', 'DESC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }

    /**
     * Get supplier by ID (AJAX)
     */
    public function getSupplier($id)
    {
        $supplier = $this->supplierModel->find($id);

        if ($supplier) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $supplier
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Supplier tidak ditemukan'
        ]);
    }

    /**
     * Create new supplier (AJAX)
     */
    public function create()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_supplier' => 'required|min_length[3]|max_length[255]',
            'kontak' => 'permit_empty|max_length[100]',
            'alamat' => 'permit_empty'
        ], [
            'nama_supplier' => [
                'required' => 'Nama supplier harus diisi',
                'min_length' => 'Nama supplier minimal 3 karakter',
                'max_length' => 'Nama supplier maksimal 255 karakter'
            ],
            'kontak' => [
                'max_length' => 'Kontak maksimal 100 karakter'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $namaSupplier = $this->request->getPost('nama_supplier');

        // Check if name already exists
        if ($this->supplierModel->isNameExists($namaSupplier)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama supplier sudah ada'
            ]);
        }

        $data = [
            'nama_supplier' => $namaSupplier,
            'kontak' => $this->request->getPost('kontak'),
            'alamat' => $this->request->getPost('alamat')
        ];

        if ($this->supplierModel->insert($data)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'CREATE',
                'module' => 'Supplier',
                'log_message' => 'Menambah supplier: ' . $namaSupplier
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier berhasil ditambahkan',
                'data' => [
                    'id' => $this->supplierModel->getInsertID(),
                    'nama_supplier' => $namaSupplier
                ]
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menambahkan supplier'
        ]);
    }

    /**
     * Update supplier (AJAX)
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_supplier' => 'required|min_length[3]|max_length[255]',
            'kontak' => 'permit_empty|max_length[100]',
            'alamat' => 'permit_empty'
        ], [
            'nama_supplier' => [
                'required' => 'Nama supplier harus diisi',
                'min_length' => 'Nama supplier minimal 3 karakter',
                'max_length' => 'Nama supplier maksimal 255 karakter'
            ],
            'kontak' => [
                'max_length' => 'Kontak maksimal 100 karakter'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier tidak ditemukan'
            ]);
        }

        $namaSupplier = $this->request->getPost('nama_supplier');

        // Check if name already exists (exclude current supplier)
        if ($this->supplierModel->isNameExists($namaSupplier, $id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama supplier sudah ada'
            ]);
        }

        $data = [
            'nama_supplier' => $namaSupplier,
            'kontak' => $this->request->getPost('kontak'),
            'alamat' => $this->request->getPost('alamat')
        ];

        if ($this->supplierModel->update($id, $data)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'UPDATE',
                'module' => 'Supplier',
                'log_message' => 'Mengubah supplier: ' . $namaSupplier
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier berhasil diupdate'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal mengupdate supplier'
        ]);
    }

    /**
     * Delete supplier (AJAX)
     */
    public function delete($id)
    {
        $supplier = $this->supplierModel->find($id);

        if (!$supplier) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier tidak ditemukan'
            ]);
        }

        // Check if supplier is used in transactions
        $db = \Config\Database::connect();
        $usageCount = $db->table('tb_stock_in')
            ->where('supplier_id', $id)
            ->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Supplier tidak dapat dihapus karena sudah digunakan dalam transaksi'
            ]);
        }

        if ($this->supplierModel->delete($id)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'DELETE',
                'module' => 'Supplier',
                'log_message' => 'Menghapus supplier: ' . $supplier['nama_supplier']
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Supplier berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menghapus supplier'
        ]);
    }

    /**
     * Get suppliers for dropdown (AJAX)
     */
    public function getForDropdown()
    {
        $suppliers = $this->supplierModel->getForDropdown();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }
}
