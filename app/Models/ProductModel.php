<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'tb_products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_id', 'brand_id', 'kode_barang', 'nama_barang', 'deskripsi', 'image', 'satuan', 'stok_saat_ini', 'min_stok', 'harga_beli_saat_ini', 'harga_jual_saat_ini', 'created_at', 'updated_at'];

    /**
     * Get product with category and brand info
     */
    public function getProductWithDetails($id)
    {
        return $this->select('tb_products.*, tb_categories.nama_kategori, tb_brands.nama_brand')
            ->join('tb_categories', 'tb_categories.id = tb_products.category_id', 'left')
            ->join('tb_brands', 'tb_brands.id = tb_products.brand_id', 'left')
            ->where('tb_products.id', $id)
            ->first();
    }

    /**
     * Find product by barcode/code
     */
    public function findByCode($code)
    {
        return $this->where('kode_barang', $code)->first();
    }

    /**
     * Search products for autocomplete
     */
    public function searchProducts($keyword)
    {
        return $this->select('id, kode_barang, nama_barang, satuan, stok_saat_ini')
            ->like('kode_barang', $keyword)
            ->orLike('nama_barang', $keyword)
            ->limit(10)
            ->findAll();
    }
}
