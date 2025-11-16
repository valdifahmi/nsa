<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        helper(['form', 'url']);
    }

    /**
     * Display the category management page
     */
    public function index()
    {
        return view('Category/index');
    }

    /**
     * Fetch all categories (AJAX)
     */
    public function fetchAll()
    {
        $categories = $this->categoryModel->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Store new category (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'permit_empty|max_length[1000]',
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
            $imageFile->move(FCPATH . 'uploads/categories', $imageName);
        }

        $data = [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'image' => $imageName
        ];

        if ($this->categoryModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Category created successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to create category'
        ]);
    }

    /**
     * Get single category for editing (AJAX)
     */
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Category not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $category
        ]);
    }

    /**
     * Update category (AJAX)
     */
    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Category not found'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $imageName = $category['image'];
        $imageFile = $this->request->getFile('image');

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image if not default
            if ($category['image'] !== 'default.png') {
                $oldImagePath = FCPATH . 'uploads/categories/' . $category['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/categories', $imageName);
        }

        $data = [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'image' => $imageName
        ];

        if ($this->categoryModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Category updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update category'
        ]);
    }

    /**
     * Delete category (AJAX)
     */
    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Category not found'
            ]);
        }

        // Delete image file if not default
        if ($category['image'] !== 'default.png') {
            $imagePath = FCPATH . 'uploads/categories/' . $category['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->categoryModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Category deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete category'
        ]);
    }
}
