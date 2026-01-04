<?php

namespace App\Models;

use CodeIgniter\Model;

class StockInModel extends Model
{
    protected $table = 'tb_stock_in';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nomor_transaksi', 'supplier_id', 'user_id', 'tanggal_masuk', 'catatan'];

    // Timestamps
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
