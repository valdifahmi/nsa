<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BrandModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        helper(['form', 'url']);
    }

    /**
     * Display the product management page
     */
    public function index()
    {
        return view('Product/index');
    }

    /**
     * Fetch all products with category and brand names (AJAX)
     */
    public function fetchAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_products p');
        $builder->select('p.*, c.nama_kategori, b.nama_brand');
        $builder->join('tb_categories c', 'c.id = p.category_id', 'left');
        $builder->join('tb_brands b', 'b.id = p.brand_id', 'left');
        $builder->orderBy('p.id', 'DESC');

        $products = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Fetch dropdowns data for categories and brands (AJAX)
     */
    public function fetchDropdowns()
    {
        $categories = $this->categoryModel->findAll();
        $brands = $this->brandModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'brands' => $brands
            ]
        ]);
    }

    /**
     * Store new product (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'kode_barang' => 'required|is_unique[tb_products.kode_barang]|max_length[100]',
            'nama_barang' => 'required|min_length[3]|max_length[255]',
            'category_id' => 'required|numeric',
            'brand_id' => 'permit_empty|numeric',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'satuan' => 'required|max_length[50]',
            'stok_saat_ini' => 'required|numeric',
            'min_stok' => 'required|numeric',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $imageName = 'default.png';
        $imageFile = $this->request->getFile('image');

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/products', $imageName);
        }

        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'category_id' => $this->request->getPost('category_id'),
            'brand_id' => $this->request->getPost('brand_id') ?: null,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'satuan' => $this->request->getPost('satuan'),
            'stok_saat_ini' => $this->request->getPost('stok_saat_ini'),
            'min_stok' => $this->request->getPost('min_stok'),
            'image' => $imageName
        ];

        if ($this->productModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product created successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to create product'
        ]);
    }

    /**
     * Get single product for editing (AJAX)
     */
    public function edit($id)
    {
        $product = $this->productModel->find($id);

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
     * Update product (AJAX)
     */
    public function update($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'kode_barang' => "required|max_length[100]|is_unique[tb_products.kode_barang,id,{$id}]",
            'nama_barang' => 'required|min_length[3]|max_length[255]',
            'category_id' => 'required|numeric',
            'brand_id' => 'permit_empty|numeric',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'satuan' => 'required|max_length[50]',
            'stok_saat_ini' => 'required|numeric',
            'min_stok' => 'required|numeric',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $imageName = $product['image'];
        $imageFile = $this->request->getFile('image');

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image if not default
            if ($product['image'] !== 'default.png') {
                $oldImagePath = FCPATH . 'uploads/products/' . $product['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/products', $imageName);
        }

        $data = [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'category_id' => $this->request->getPost('category_id'),
            'brand_id' => $this->request->getPost('brand_id') ?: null,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'satuan' => $this->request->getPost('satuan'),
            'stok_saat_ini' => $this->request->getPost('stok_saat_ini'),
            'min_stok' => $this->request->getPost('min_stok'),
            'image' => $imageName
        ];

        if ($this->productModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update product'
        ]);
    }

    /**
     * Delete product (AJAX)
     */
    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }

        // Store product name before deletion
        $productName = $product['nama_barang'];
        $productCode = $product['kode_barang'];

        // Delete image file if not default
        if ($product['image'] !== 'default.png') {
            $imagePath = FCPATH . 'uploads/products/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->productModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete product'
        ]);
    }

    /**
     * Find product by code (for barcode scanner) (AJAX)
     */
    public function findProductByCode()
    {
        $kode_barang = $this->request->getGet('code');

        if (!$kode_barang) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Product code is required'
            ]);
        }

        $product = $this->productModel->where('kode_barang', $kode_barang)->first();

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
     */
    public function searchAutocomplete()
    {
        $query = $this->request->getGet('q');

        if (!$query || strlen($query) < 2) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => []
            ]);
        }

        $products = $this->productModel
            ->like('kode_barang', $query)
            ->orLike('nama_barang', $query)
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Search products for jQuery UI Autocomplete (AJAX)
     */
    public function searchProducts()
    {
        $term = $this->request->getGet('term');

        if (!$term || strlen($term) < 2) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => []
            ]);
        }

        $products = $this->productModel
            ->groupStart()
            ->like('kode_barang', $term)
            ->orLike('nama_barang', $term)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $products
        ]);
    }
}
