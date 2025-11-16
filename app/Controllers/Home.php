<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('Product/list_product');
    }
}
