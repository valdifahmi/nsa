<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jangan redirect jika sudah di halaman login
        $uri = $request->getUri();
        $path = $uri->getPath();

        if (strpos($path, 'auth/login') !== false || strpos($path, 'auth/processLogin') !== false) {
            return;
        }

        if (!session()->has('user')) {
            return redirect()->to(base_url('auth/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
