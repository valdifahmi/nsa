<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'tb_products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_id', 'brand_id', 'kode_barang', 'nama_barang', 'deskripsi', 'image', 'satuan', 'stok_saat_ini', 'min_stok', 'created_at', 'updated_at'];
}
