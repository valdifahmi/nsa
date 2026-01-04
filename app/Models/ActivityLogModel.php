<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'tb_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'action',
        'module',
        'log_message'
    ];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
