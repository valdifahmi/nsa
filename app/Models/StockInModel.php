<?php

namespace App\Models;

use CodeIgniter\Model;

class StockInModel extends Model
{
    protected $table = 'tb_stock_in';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nomor_transaksi', 'user_id', 'tanggal_masuk', 'supplier', 'catatan'];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
