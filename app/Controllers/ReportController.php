<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockInModel;
use App\Models\StockInItemModel;
use App\Models\StockOutModel;
use App\Models\StockOutItemModel;
use App\Models\ProductModel;
use App\Models\SupplierModel; // Added

class ReportController extends BaseController
{
    protected $db;
    protected $supplierModel; // Added
    protected $stockInModel; // Added
    protected $stockInItemModel; // Added

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->supplierModel = new SupplierModel(); // Added
        $this->stockInModel = new StockInModel(); // Added
        $this->stockInItemModel = new StockInItemModel(); // Added
        helper(['form', 'url']);
    }

    /**
     * Purchase Report - Load View
     */
    public function purchaseReport()
    {
        $data['suppliers'] = $this->supplierModel->orderBy('nama_supplier', 'ASC')->findAll();
        return view('Report/purchase', $data);
    }

    /**
     * Fetch Purchase Report Data (AJAX for DataTable)
     */
    public function fetchPurchaseReport()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $order = $request->getPost('order')[0] ?? [];

        // Custom filters
        $startDate = $request->getPost('startDate');
        $endDate = $request->getPost('endDate');
        $supplierId = $request->getPost('supplierId');

        $builder = $this->db->table('tb_stock_in si');
        $builder->select('
            si.id, 
            si.nomor_transaksi, 
            si.tanggal_masuk, 
            s.nama_supplier, 
            si.catatan,
            (SELECT COUNT(sii_sub.id) FROM tb_stock_in_items sii_sub WHERE sii_sub.stock_in_id = si.id) as jenis,
            (SELECT SUM(sii_sub.jumlah) FROM tb_stock_in_items sii_sub WHERE sii_sub.stock_in_id = si.id) as jumlah_qty,
            (SELECT SUM(sii_sub.jumlah * sii_sub.harga_beli_satuan) FROM tb_stock_in_items sii_sub WHERE sii_sub.stock_in_id = si.id) as total
        ');
        $builder->join('tb_suppliers s', 'si.supplier_id = s.id', 'left');

        // --- Filtering ---
        if ($searchValue) {
            $builder->groupStart()
                ->like('si.nomor_transaksi', $searchValue)
                ->orLike('s.nama_supplier', $searchValue)
                ->orLike('si.catatan', $searchValue)
                ->groupEnd();
        }
        if ($startDate && $endDate) {
            $builder->where('si.tanggal_masuk >=', $startDate);
            $builder->where('si.tanggal_masuk <=', $endDate . ' 23:59:59');
        }
        if ($supplierId) {
            $builder->where('si.supplier_id', $supplierId);
        }

        // --- Counting ---
        $totalRecordsBuilder = clone $builder;
        $totalRecords = $totalRecordsBuilder->countAllResults();

        // --- Ordering ---
        $columnMap = [
            1 => 'si.nomor_transaksi',
            2 => 'si.tanggal_masuk',
            3 => 's.nama_supplier',
            4 => 'jenis',
            5 => 'jumlah_qty',
            6 => 'total'
        ];
        if (isset($order['column']) && isset($columnMap[$order['column']])) {
            $builder->orderBy($columnMap[$order['column']], $order['dir']);
        } else {
            $builder->orderBy('si.tanggal_masuk', 'DESC');
            $builder->orderBy('si.id', 'DESC');
        }

        // --- Pagination ---
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $query = $builder->get();
        $data = $query->getResultArray();

        $output = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // For simplicity, using totalRecords. For complex filters, this needs a separate count.
            'data' => $data,
        ];

        return $this->response->setJSON($output);
    }

    /**
     * Get Purchase Detail for Modal (AJAX)
     */
    public function getPurchaseDetail($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid ID.'])->setStatusCode(400);
        }

        $stockIn = $this->stockInModel
            ->select('tb_stock_in.*, s.nama_supplier, u.nama_lengkap as nama_user')
            ->join('tb_suppliers s', 's.id = tb_stock_in.supplier_id', 'left')
            ->join('tb_users u', 'u.id = tb_stock_in.user_id', 'left')
            ->find($id);

        if (!$stockIn) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction not found.'])->setStatusCode(404);
        }

        $items = $this->stockInItemModel
            ->select('tb_stock_in_items.*, p.nama_barang as nama_produk, (tb_stock_in_items.jumlah * tb_stock_in_items.harga_beli_satuan) as harga_total')
            ->join('tb_products p', 'p.id = tb_stock_in_items.product_id')
            ->where('tb_stock_in_items.stock_in_id', $id)
            ->findAll();

        // Calculate totals for the main transaction record
        $subtotal = array_sum(array_column($items, 'harga_total'));
        // Assuming diskon and total_akhir are stored in tb_stock_in, if not they would be 0
        $stockIn['subtotal'] = $subtotal;
        $stockIn['diskon'] = $stockIn['diskon'] ?? 0;
        $stockIn['total_akhir'] = $subtotal - $stockIn['diskon'];


        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'stock_in' => $stockIn,
                'items' => $items,
            ]
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
