<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        $user = session()->get('user');

        if (!$user) {
            // Not logged in, redirect to login
            return redirect()->to('/auth/login')->with('error', 'Please login first');
        }

        // Check if user role is admin
        if (!isset($user['role']) || $user['role'] !== 'admin') {
            // Not admin, redirect to home with error
            return redirect()->to('/')->with('error', 'Access denied. Admin only.');
        }

        // User is admin, allow access
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
