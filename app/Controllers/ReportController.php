<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockInModel;
use App\Models\StockInItemModel;
use App\Models\StockOutModel;
use App\Models\StockOutItemModel;
use App\Models\ProductModel;

class ReportController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Purchase Report (Header) - Load View
     */
    public function purchaseReport()
    {
        return view('Report/purchase');
    }

    /**
     * Fetch Purchase Report Data (AJAX)
     * JOIN tb_stock_in + tb_users
     */
    public function fetchPurchaseReport()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $builder = $this->db->table('tb_stock_in si');
        $builder->select('si.id, si.nomor_transaksi, si.tanggal_masuk, si.supplier, si.catatan, u.nama_lengkap as user_name');
        $builder->join('tb_users u', 'si.user_id = u.id', 'left');

        if ($startDate && $endDate) {
            $builder->where('DATE(si.tanggal_masuk) >=', $startDate);
            $builder->where('DATE(si.tanggal_masuk) <=', $endDate);
        }

        $builder->orderBy('si.tanggal_masuk', 'DESC');
        $query = $builder->get();
        $data = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Purchase Item Report (Detail) - Load View
     */
    public function purchaseItemReport()
    {
        return view('Report/purchase_item');
    }

    /**
     * Fetch Purchase Item Report Data (AJAX)
     * JOIN tb_stock_in_items + tb_stock_in + tb_products
     */
    public function fetchPurchaseItemReport()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $builder = $this->db->table('tb_stock_in_items sii');
        $builder->select('sii.id, si.nomor_transaksi, si.tanggal_masuk, p.kode_barang, p.nama_barang, sii.jumlah, p.satuan');
        $builder->join('tb_stock_in si', 'sii.stock_in_id = si.id', 'left');
        $builder->join('tb_products p', 'sii.product_id = p.id', 'left');

        if ($startDate && $endDate) {
            $builder->where('DATE(si.tanggal_masuk) >=', $startDate);
            $builder->where('DATE(si.tanggal_masuk) <=', $endDate);
        }

        $builder->orderBy('si.tanggal_masuk', 'DESC');
        $query = $builder->get();
        $data = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Sale Report (Header) - Load View
     */
    public function saleReport()
    {
        return view('Report/sale');
    }

    /**
     * Fetch Sale Report Data (AJAX)
     * JOIN tb_stock_out + tb_users
     */
    public function fetchSaleReport()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $builder = $this->db->table('tb_stock_out so');
        $builder->select('so.id, so.nomor_transaksi, so.tanggal_keluar, so.penerima, so.catatan, u.nama_lengkap as user_name');
        $builder->join('tb_users u', 'so.user_id = u.id', 'left');

        if ($startDate && $endDate) {
            $builder->where('DATE(so.tanggal_keluar) >=', $startDate);
            $builder->where('DATE(so.tanggal_keluar) <=', $endDate);
        }

        $builder->orderBy('so.tanggal_keluar', 'DESC');
        $query = $builder->get();
        $data = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Sale Item Report (Detail) - Load View
     */
    public function saleItemReport()
    {
        return view('Report/sale_item');
    }

    /**
     * Fetch Sale Item Report Data (AJAX)
     * JOIN tb_stock_out_items + tb_stock_out + tb_products
     */
    public function fetchSaleItemReport()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');

        $builder = $this->db->table('tb_stock_out_items soi');
        $builder->select('soi.id, so.nomor_transaksi, so.tanggal_keluar, p.kode_barang, p.nama_barang, soi.jumlah, p.satuan');
        $builder->join('tb_stock_out so', 'soi.stock_out_id = so.id', 'left');
        $builder->join('tb_products p', 'soi.product_id = p.id', 'left');

        if ($startDate && $endDate) {
            $builder->where('DATE(so.tanggal_keluar) >=', $startDate);
            $builder->where('DATE(so.tanggal_keluar) <=', $endDate);
        }

        $builder->orderBy('so.tanggal_keluar', 'DESC');
        $query = $builder->get();
        $data = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Stock Report - Load View
     */
    public function stockReport()
    {
        return view('Report/stock');
    }

    /**
     * Fetch Stock Report Data (AJAX)
     * JOIN tb_products + tb_categories + tb_brands
     * No date filter needed
     */
    public function fetchStockReport()
    {
        $builder = $this->db->table('tb_products p');
        $builder->select('p.id, p.kode_barang, p.nama_barang, c.nama_kategori, b.nama_brand, p.satuan, p.stok_saat_ini, p.min_stok');
        $builder->join('tb_categories c', 'p.category_id = c.id', 'left');
        $builder->join('tb_brands b', 'p.brand_id = b.id', 'left');
        $builder->orderBy('p.nama_barang', 'ASC');

        $query = $builder->get();
        $data = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
