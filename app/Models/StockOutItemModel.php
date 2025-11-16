<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOutItemModel extends Model
{
    protected $table = 'tb_stock_out_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['stock_out_id', 'product_id', 'jumlah'];

    // No timestamps for items table
    protected $useTimestamps = false;
}
