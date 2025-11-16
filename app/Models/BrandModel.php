<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table = 'tb_brands';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_brand', 'created_at'];
}
