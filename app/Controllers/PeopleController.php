<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class PeopleController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * Display the people (users) page
     */
    public function index()
    {
        return view('People/index');
    }

    /**
     * Fetch all users (AJAX) - exclude password
     */
    public function fetchAll()
    {
        $users = $this->userModel->select('id, username, nama_lengkap, role, created_at, updated_at')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Store new user (AJAX)
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'username' => 'required|is_unique[tb_users.username]|max_length[100]',
            'nama_lengkap' => 'required|max_length[255]',
            'role' => 'required|in_list[admin,staff]',
            'password' => 'required|min_length[6]'
        ];

        $messages = [
            'username' => [
                'required' => 'Username wajib diisi',
                'is_unique' => 'Username sudah digunakan',
                'max_length' => 'Username maksimal 100 karakter'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap wajib diisi',
                'max_length' => 'Nama lengkap maksimal 255 karakter'
            ],
            'role' => [
                'required' => 'Role wajib dipilih',
                'in_list' => 'Role harus admin atau staff'
            ],
            'password' => [
                'required' => 'Password wajib diisi',
                'min_length' => 'Password minimal 6 karakter'
            ]
        ];

        $validation->setRules($rules, $messages);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        // Hash password
        $password = $this->request->getPost('password');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'username' => $this->request->getPost('username'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role'),
            'password' => $hashedPassword
        ];

        if ($this->userModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan user',
                'errors' => $this->userModel->errors()
            ]);
        }
    }

    /**
     * Get user data for edit (AJAX) - exclude password
     */
    public function edit($id)
    {
        $user = $this->userModel->select('id, username, nama_lengkap, role')->find($id);

        if ($user) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $user
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }
    }

    /**
     * Update user (AJAX)
     * Password is optional - only update if filled
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();

        // Get current user to check username uniqueness
        $currentUser = $this->userModel->find($id);

        if (!$currentUser) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }

        $rules = [
            'username' => "required|max_length[100]|is_unique[tb_users.username,id,{$id}]",
            'nama_lengkap' => 'required|max_length[255]',
            'role' => 'required|in_list[admin,staff]',
            'password' => 'permit_empty|min_length[6]'
        ];

        $messages = [
            'username' => [
                'required' => 'Username wajib diisi',
                'is_unique' => 'Username sudah digunakan',
                'max_length' => 'Username maksimal 100 karakter'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap wajib diisi',
                'max_length' => 'Nama lengkap maksimal 255 karakter'
            ],
            'role' => [
                'required' => 'Role wajib dipilih',
                'in_list' => 'Role harus admin atau staff'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter'
            ]
        ];

        $validation->setRules($rules, $messages);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role')
        ];

        // Only update password if filled
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil diupdate'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate user',
                'errors' => $this->userModel->errors()
            ]);
        }
    }

    /**
     * Delete user (AJAX)
     */
    public function delete($id)
    {
        // Optional: Prevent deleting self
        $currentUser = session()->get('user');
        if ($currentUser && $currentUser['id'] == $id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ]);
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus user'
            ]);
        }
    }
}
