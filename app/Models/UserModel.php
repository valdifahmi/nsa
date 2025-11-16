<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tb_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'nama_lengkap', 'role', 'created_at', 'updated_at'];
}
