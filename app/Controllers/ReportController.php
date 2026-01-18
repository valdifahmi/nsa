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

    /**
     * Product Log Report - Load View
     */
    /**
     * Product Log Report - Load View
     */
    public function productLog()
    {
        return view('Report/product_log');
    }

    /**
     * Fetch Product Log Data (AJAX)
     */
    public function getLogBarang()
    {
        $startDate = $this->request->getVar('startDate');
        $endDate = $this->request->getVar('endDate');

        if (!$startDate || !$endDate) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please select a date range.'])->setStatusCode(400);
        }

        // Adjust endDate to include the whole day
        $endDateWithTime = $endDate . ' 23:59:59';

        $sql = "
            SELECT * FROM (
                SELECT
                    si.tanggal_masuk as tanggal,
                    p.kode_barang,
                    p.nama_barang,
                    si.nomor_transaksi as referensi,
                    sii.jumlah as qty,
                    'Stock-IN' as action
                FROM tb_stock_in_items sii
                JOIN tb_stock_in si ON sii.stock_in_id = si.id
                JOIN tb_products p ON sii.product_id = p.id
                WHERE si.tanggal_masuk >= ? AND si.tanggal_masuk <= ?

                UNION ALL

                SELECT
                    so.tanggal_keluar as tanggal,
                    p.kode_barang,
                    p.nama_barang,
                    so.nomor_transaksi as referensi,
                    soi.jumlah as qty,
                    'Stock-OUT' as action
                FROM tb_stock_out_items soi
                JOIN tb_stock_out so ON soi.stock_out_id = so.id
                JOIN tb_products p ON soi.product_id = p.id
                WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ?
            ) as log
            ORDER BY tanggal DESC
        ";

        $bindings = [$startDate, $endDateWithTime, $startDate, $endDateWithTime];
        $query = $this->db->query($sql, $bindings);
        $logData = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $logData
        ]);
    }
}
