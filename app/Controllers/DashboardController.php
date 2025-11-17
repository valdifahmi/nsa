<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\CategoryModel;

class DashboardController extends BaseController
{
    protected $db;
    protected $productModel;
    protected $brandModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->db            = \Config\Database::connect();
        $this->productModel  = new ProductModel();
        $this->brandModel    = new BrandModel();
        $this->categoryModel = new CategoryModel();
        helper(['date']);
    }

    /**
     * Load dashboard shell and initial filter datasets
     */
    public function index()
    {
        $data = [
            'products'   => $this->productModel->orderBy('nama_barang', 'ASC')->findAll(),
            'brands'     => $this->brandModel->orderBy('nama_brand', 'ASC')->findAll(),
            'categories' => $this->categoryModel->orderBy('nama_kategori', 'ASC')->findAll(),
        ];

        return view('Dashboard/index', $data);
    }

    /**
     * Central AJAX endpoint to fetch all dashboard datasets in one call
     * POST params: startDate (Y-m-d), endDate (Y-m-d), productId, brandId, categoryId
     */
    public function fetchData()
    {
        $startDate  = (string) $this->request->getPost('startDate');
        $endDate    = (string) $this->request->getPost('endDate');
        $productId  = (string) $this->request->getPost('productId');
        $brandId    = (string) $this->request->getPost('brandId');
        $categoryId = (string) $this->request->getPost('categoryId');

        // Default last 30 days if not provided
        if (empty($startDate) || empty($endDate)) {
            $endDate   = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime('-29 days'));
        }

        $startDT = $startDate . ' 00:00:00';
        $endDT   = $endDate . ' 23:59:59';

        // ------------- Helper closures for dynamic filters -------------
        $applyProductFilter = function ($builder, string $productAlias, bool $joinProductIfNeeded = false) use ($productId, $brandId, $categoryId) {
            if (!empty($productId)) {
                $builder->where("$productAlias.id", (int) $productId);
            }
            if (!empty($brandId)) {
                if ($joinProductIfNeeded) {
                    // assume we already joined as alias $productAlias
                    $builder->where("$productAlias.brand_id", (int) $brandId);
                } else {
                    $builder->where("$productAlias.brand_id", (int) $brandId);
                }
            }
            if (!empty($categoryId)) {
                if ($joinProductIfNeeded) {
                    $builder->where("$productAlias.category_id", (int) $categoryId);
                } else {
                    $builder->where("$productAlias.category_id", (int) $categoryId);
                }
            }
        };

        // ------------- Cards (totals) -------------
        // Total Masuk
        $bIn = $this->db->table('tb_stock_in_items sii')
            ->join('tb_stock_in si', 'si.id = sii.stock_in_id', 'inner');

        // join products if we need brand/category filter or specific product
        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bIn->join('tb_products p', 'p.id = sii.product_id', 'inner');
            $applyProductFilter($bIn, 'p', true);
        }

        $bIn->where('si.tanggal_masuk >=', $startDT)
            ->where('si.tanggal_masuk <=', $endDT)
            ->selectSum('sii.jumlah', 'total_masuk');

        $rowIn = $bIn->get()->getRowArray();
        $totalMasuk = (int) ($rowIn['total_masuk'] ?? 0);

        // Total Keluar
        $bOut = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner');

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bOut->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bOut, 'p', true);
        }

        $bOut->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->selectSum('soi.jumlah', 'total_keluar');

        $rowOut = $bOut->get()->getRowArray();
        $totalKeluar = (int) ($rowOut['total_keluar'] ?? 0);

        // Total Stok (apply product/brand/category only)
        $bStock = $this->db->table('tb_products p')->selectSum('p.stok_saat_ini', 'total_stok');

        if (!empty($productId)) {
            $bStock->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bStock->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bStock->where('p.category_id', (int) $categoryId);
        }

        $rowStock = $bStock->get()->getRowArray();
        $totalStok = (int) ($rowStock['total_stok'] ?? 0);

        $cards = [
            'total_masuk'  => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'total_stok'   => $totalStok,
        ];

        // ------------- Overview Chart (group by date) -------------
        $bInDaily = $this->db->table('tb_stock_in si')
            ->join('tb_stock_in_items sii', 'sii.stock_in_id = si.id', 'inner')
            ->select("DATE(si.tanggal_masuk) AS tgl", false)
            ->select("SUM(sii.jumlah) AS total", false)
            ->where('si.tanggal_masuk >=', $startDT)
            ->where('si.tanggal_masuk <=', $endDT)
            ->groupBy('DATE(si.tanggal_masuk)');

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bInDaily->join('tb_products p', 'p.id = sii.product_id', 'inner');
            $applyProductFilter($bInDaily, 'p', true);
        }

        $inDailyRows = $bInDaily->get()->getResultArray();
        $inDaily = [];
        foreach ($inDailyRows as $r) {
            $inDaily[$r['tgl']] = (int) $r['total'];
        }

        $bOutDaily = $this->db->table('tb_stock_out so')
            ->join('tb_stock_out_items soi', 'soi.stock_out_id = so.id', 'inner')
            ->select("DATE(so.tanggal_keluar) AS tgl", false)
            ->select("SUM(soi.jumlah) AS total", false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->groupBy('DATE(so.tanggal_keluar)');

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bOutDaily->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bOutDaily, 'p', true);
        }

        $outDailyRows = $bOutDaily->get()->getResultArray();
        $outDaily = [];
        foreach ($outDailyRows as $r) {
            $outDaily[$r['tgl']] = (int) $r['total'];
        }

        // Merge into continuous date series
        $overview = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );
        foreach ($period as $dt) {
            $d = $dt->format('Y-m-d');
            $overview[] = [
                'waktu'  => $d,
                'masuk'  => $inDaily[$d]  ?? 0,
                'keluar' => $outDaily[$d] ?? 0,
            ];
        }

        // ------------- Top Products (Stock Out) -------------
        $bTop = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->select('p.nama_barang AS produk')
            ->select('SUM(soi.jumlah) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bTop->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bTop->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bTop->where('p.category_id', (int) $categoryId);
        }

        $topProducts = $bTop->groupBy('p.id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // ------------- Low Stock Products -------------
        $bLow = $this->db->table('tb_products p')
            ->select('p.id, p.nama_barang, p.image, p.stok_saat_ini, p.min_stok')
            ->where('p.stok_saat_ini <= p.min_stok');

        if (!empty($productId)) {
            $bLow->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bLow->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bLow->where('p.category_id', (int) $categoryId);
        }

        $lowStock = $bLow->orderBy('p.stok_saat_ini', 'ASC')
            ->limit(10)
            ->get()->getResultArray();

        // ------------- Donut Charts (Proportion of Stock) -------------
        // by product
        $bDonutProduct = $this->db->table('tb_products p')
            ->select('p.nama_barang AS label')
            ->select('SUM(p.stok_saat_ini) AS value', false);

        if (!empty($productId)) {
            $bDonutProduct->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bDonutProduct->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bDonutProduct->where('p.category_id', (int) $categoryId);
        }

        $donutByProduct = $bDonutProduct->groupBy('p.id')
            ->orderBy('value', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        // by brand
        $bDonutBrand = $this->db->table('tb_products p')
            ->join('tb_brands b', 'b.id = p.brand_id', 'left')
            ->select("COALESCE(b.nama_brand, 'No Brand') AS label", false)
            ->select('SUM(p.stok_saat_ini) AS value', false);

        if (!empty($productId)) {
            $bDonutBrand->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bDonutBrand->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bDonutBrand->where('p.category_id', (int) $categoryId);
        }

        $donutByBrand = $bDonutBrand->groupBy('p.brand_id')
            ->orderBy('value', 'DESC')
            ->get()->getResultArray();

        // by category
        $bDonutCategory = $this->db->table('tb_products p')
            ->join('tb_categories c', 'c.id = p.category_id', 'left')
            ->select("COALESCE(c.nama_kategori, 'No Category') AS label", false)
            ->select('SUM(p.stok_saat_ini) AS value', false);

        if (!empty($productId)) {
            $bDonutCategory->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bDonutCategory->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bDonutCategory->where('p.category_id', (int) $categoryId);
        }

        $donutByCategory = $bDonutCategory->groupBy('p.category_id')
            ->orderBy('value', 'DESC')
            ->get()->getResultArray();

        $payload = [
            'cards'              => $cards,
            'overview_chart'     => $overview,
            'top_products_chart' => $topProducts,
            'low_stock_products' => $lowStock,
            'donut_charts'       => [
                'product'  => $donutByProduct,
                'brand'    => $donutByBrand,
                'category' => $donutByCategory,
            ],
        ];

        return $this->response->setJSON($payload);
    }
}
