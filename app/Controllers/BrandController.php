<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BrandModel;

class BrandController extends BaseController
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new BrandModel();
        helper(['form', 'url']);
    }

    /**
     * Display Brand List Page
     */
    public function index()
    {
        return view('Brand/index');
    }

    /**
     * Fetch All Brands (AJAX)
     */
    public function fetchAll()
    {
        $brands = $this->brandModel->orderBy('nama_brand', 'ASC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $brands
        ]);
    }

    /**
     * Store New Brand (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_brand' => [
                'rules' => 'required|min_length[2]|max_length[255]|is_unique[tb_brands.nama_brand]',
                'errors' => [
                    'required' => 'Nama brand harus diisi',
                    'min_length' => 'Nama brand minimal 2 karakter',
                    'max_length' => 'Nama brand maksimal 255 karakter',
                    'is_unique' => 'Nama brand sudah ada'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'nama_brand' => $this->request->getPost('nama_brand')
        ];

        if ($this->brandModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Brand berhasil ditambahkan'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan brand'
            ]);
        }
    }

    /**
     * Get Brand by ID for Edit (AJAX)
     */
    public function edit($id)
    {
        $brand = $this->brandModel->find($id);

        if ($brand) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $brand
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Brand tidak ditemukan'
            ]);
        }
    }

    /**
     * Update Brand (AJAX)
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_brand' => [
                'rules' => "required|min_length[2]|max_length[255]|is_unique[tb_brands.nama_brand,id,{$id}]",
                'errors' => [
                    'required' => 'Nama brand harus diisi',
                    'min_length' => 'Nama brand minimal 2 karakter',
                    'max_length' => 'Nama brand maksimal 255 karakter',
                    'is_unique' => 'Nama brand sudah ada'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'nama_brand' => $this->request->getPost('nama_brand')
        ];

        if ($this->brandModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Brand berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate brand'
            ]);
        }
    }

    /**
     * Delete Brand (AJAX)
     */
    public function delete($id)
    {
        // Check if brand is used in products
        $db = \Config\Database::connect();
        $builder = $db->table('tb_products');
        $builder->where('brand_id', $id);
        $count = $builder->countAllResults();

        if ($count > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Brand tidak dapat dihapus karena masih digunakan oleh ' . $count . ' produk'
            ]);
        }

        if ($this->brandModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Brand berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus brand'
            ]);
        }
    }
}
