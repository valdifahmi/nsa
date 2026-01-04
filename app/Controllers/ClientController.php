<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\LogModel;

class ClientController extends BaseController
{
    protected $clientModel;
    protected $logModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->logModel = new LogModel();
    }

    /**
     * Display client list page
     */
    public function index()
    {
        $data = [
            'title' => 'Data Client'
        ];
        return view('Client/index', $data);
    }

    /**
     * Fetch all clients (AJAX)
     */
    public function fetchClients()
    {
        $clients = $this->clientModel->orderBy('id', 'DESC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $clients
        ]);
    }

    /**
     * Get client by ID (AJAX)
     */
    public function getClient($id)
    {
        $client = $this->clientModel->find($id);

        if ($client) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $client
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Client tidak ditemukan'
        ]);
    }

    /**
     * Create new client (AJAX)
     */
    public function create()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_klien' => 'required|min_length[3]|max_length[255]',
            'kontak' => 'permit_empty|max_length[100]',
            'alamat' => 'permit_empty'
        ], [
            'nama_klien' => [
                'required' => 'Nama client harus diisi',
                'min_length' => 'Nama client minimal 3 karakter',
                'max_length' => 'Nama client maksimal 255 karakter'
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

        $namaKlien = $this->request->getPost('nama_klien');

        // Check if name already exists
        if ($this->clientModel->isNameExists($namaKlien)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama client sudah ada'
            ]);
        }

        $data = [
            'nama_klien' => $namaKlien,
            'kontak' => $this->request->getPost('kontak'),
            'alamat' => $this->request->getPost('alamat')
        ];

        if ($this->clientModel->insert($data)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'CREATE',
                'module' => 'Client',
                'log_message' => 'Menambah client: ' . $namaKlien
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Client berhasil ditambahkan',
                'data' => [
                    'id' => $this->clientModel->getInsertID(),
                    'nama_klien' => $namaKlien
                ]
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menambahkan client'
        ]);
    }

    /**
     * Update client (AJAX)
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'nama_klien' => 'required|min_length[3]|max_length[255]',
            'kontak' => 'permit_empty|max_length[100]',
            'alamat' => 'permit_empty'
        ], [
            'nama_klien' => [
                'required' => 'Nama client harus diisi',
                'min_length' => 'Nama client minimal 3 karakter',
                'max_length' => 'Nama client maksimal 255 karakter'
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

        $client = $this->clientModel->find($id);
        if (!$client) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Client tidak ditemukan'
            ]);
        }

        $namaKlien = $this->request->getPost('nama_klien');

        // Check if name already exists (exclude current client)
        if ($this->clientModel->isNameExists($namaKlien, $id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nama client sudah ada'
            ]);
        }

        $data = [
            'nama_klien' => $namaKlien,
            'kontak' => $this->request->getPost('kontak'),
            'alamat' => $this->request->getPost('alamat')
        ];

        if ($this->clientModel->update($id, $data)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'UPDATE',
                'module' => 'Client',
                'log_message' => 'Mengubah client: ' . $namaKlien
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Client berhasil diupdate'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal mengupdate client'
        ]);
    }

    /**
     * Delete client (AJAX)
     */
    public function delete($id)
    {
        $client = $this->clientModel->find($id);

        if (!$client) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Client tidak ditemukan'
            ]);
        }

        // Check if client is used in transactions
        $db = \Config\Database::connect();
        $usageCount = $db->table('tb_stock_out')
            ->where('client_id', $id)
            ->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Client tidak dapat dihapus karena sudah digunakan dalam transaksi'
            ]);
        }

        if ($this->clientModel->delete($id)) {
            // Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'DELETE',
                'module' => 'Client',
                'log_message' => 'Menghapus client: ' . $client['nama_klien']
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Client berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menghapus client'
        ]);
    }

    /**
     * Get clients for dropdown (AJAX)
     */
    public function getForDropdown()
    {
        $clients = $this->clientModel->getForDropdown();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $clients
        ]);
    }
}
