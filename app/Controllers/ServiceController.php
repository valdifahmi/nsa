<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ServiceModel;

class ServiceController extends BaseController
{
    protected $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
        helper(['form', 'url']);
    }

    /**
     * Display the service management page
     */
    public function index()
    {
        return view('Service/index');
    }

    /**
     * Fetch all services (AJAX)
     */
    public function fetchAll()
    {
        $services = $this->serviceModel->orderBy('id', 'DESC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $services
        ]);
    }

    /**
     * Store new service (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nama_jasa' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'harga_standar' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'nama_jasa' => $this->request->getPost('nama_jasa'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga_standar' => $this->request->getPost('harga_standar')
        ];

        if ($this->serviceModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Service created successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to create service'
        ]);
    }

    /**
     * Get single service for editing (AJAX)
     */
    public function edit($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Service not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $service
        ]);
    }

    /**
     * Update service (AJAX)
     */
    public function update($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Service not found'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'nama_jasa' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'harga_standar' => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'nama_jasa' => $this->request->getPost('nama_jasa'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'harga_standar' => $this->request->getPost('harga_standar')
        ];

        if ($this->serviceModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Service updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update service'
        ]);
    }

    /**
     * Delete service (AJAX)
     */
    public function delete($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Service not found'
            ]);
        }

        if ($this->serviceModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Service deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete service'
        ]);
    }
}
