<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('Auth/login');
    }

    /**
     * Detect if a stored password string looks like a password_hash()-generated hash.
     */
    private function isHash(string $value): bool
    {
        return $value !== '' && str_starts_with($value, '$');
    }

    public function processLogin()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username' => ['required' => 'Username wajib diisi'],
            'password' => ['required' => 'Password wajib diisi'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid username or password');
        }

        $stored = (string) ($user['password'] ?? '');
        $authenticated = false;

        if ($this->isHash($stored)) {
            // Standard flow: verify hashed password
            $authenticated = password_verify($password, $stored);
        } else {
            // Fallback: plaintext password found (e.g., data diisi manual)
            // Compare securely, then upgrade to hashed password transparently
            $authenticated = hash_equals($stored, $password);
            if ($authenticated) {
                try {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $userModel->update($user['id'], ['password' => $newHash]);
                    $user['password'] = $newHash;
                } catch (\Throwable $e) {
                    // Abaikan error upgrade hash, izinkan login tetap berjalan
                }
            }
        }

        if ($authenticated) {
            // Simpan payload minimal ke sesi
            session()->set('user', [
                'id'           => $user['id'],
                'username'     => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'] ?? '',
                'role'         => $user['role'] ?? 'staff',
            ]);
            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}
