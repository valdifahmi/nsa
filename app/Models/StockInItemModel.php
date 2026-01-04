<?php

namespace App\Models;

use CodeIgniter\Model;

class StockInItemModel extends Model
{
    protected $table = 'tb_stock_in_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['stock_in_id', 'product_id', 'jumlah', 'harga_beli_satuan'];

    // No timestamps for this table
    protected $useTimestamps = false;
}
