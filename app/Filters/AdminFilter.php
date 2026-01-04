<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * Check if user is logged in and has admin role
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        $user = session()->get('user');

        if (!$user) {
            // Not logged in, redirect to login
            session()->setFlashdata('error', 'Silakan login terlebih dahulu');
            return redirect()->to('/auth/login');
        }

        // Check if user has admin role
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            // Not admin, redirect to home with error
            session()->setFlashdata('error', 'Akses ditolak. Halaman ini hanya untuk Admin.');
            return redirect()->to('/');
        }

        // User is admin, allow access
        return;
    }

    /**
     * After method (not used)
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
