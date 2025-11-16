<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOutModel extends Model
{
    protected $table = 'tb_stock_out';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nomor_transaksi', 'user_id', 'tanggal_keluar', 'penerima', 'catatan'];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
