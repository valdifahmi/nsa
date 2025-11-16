<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'tb_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kategori', 'deskripsi', 'image', 'created_at', 'updated_at'];
}
