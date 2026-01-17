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
        // Set default dates based on the app's timezone
        $appConfig = new \Config\App();
        $tz        = new \DateTimeZone($appConfig->appTimezone);
        $endDate   = new \DateTime('now', $tz);
        $startDate = new \DateTime('first day of this month', $tz);

        $data = [
            'products'         => $this->productModel->orderBy('nama_barang', 'ASC')->findAll(),
            'brands'           => $this->brandModel->orderBy('nama_brand', 'ASC')->findAll(),
            'categories'       => $this->categoryModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'defaultStartDate' => $startDate->format('Y-m-d'),
            'defaultEndDate'   => $endDate->format('Y-m-d'),
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

        // Total Stok (apply all filters, including date)
        // This is a complex query to calculate historical stock at the `endDate`
        $historicalStockQuery = "
            SELECT
                SUM(
                    p.stok_saat_ini
                    + COALESCE(so_after.total, 0)
                    - COALESCE(si_after.total, 0)
                ) as total_stok_hist
            FROM tb_products p
            LEFT JOIN (
                SELECT
                    sii_sub.product_id,
                    SUM(sii_sub.jumlah) as total
                FROM tb_stock_in_items sii_sub
                JOIN tb_stock_in si_sub ON si_sub.id = sii_sub.stock_in_id
                WHERE si_sub.tanggal_masuk > ?
                GROUP BY sii_sub.product_id
            ) AS si_after ON si_after.product_id = p.id
            LEFT JOIN (
                SELECT
                    soi_sub.product_id,
                    SUM(soi_sub.jumlah) as total
                FROM tb_stock_out_items soi_sub
                JOIN tb_stock_out so_sub ON so_sub.id = soi_sub.stock_out_id
                WHERE so_sub.tanggal_keluar > ?
                GROUP BY soi_sub.product_id
            ) AS so_after ON so_after.product_id = p.id
            WHERE 1=1
        ";
        $queryParams = [$endDT, $endDT];

        if (!empty($productId)) {
            $historicalStockQuery .= " AND p.id = ? ";
            $queryParams[] = (int) $productId;
        }
        if (!empty($brandId)) {
            $historicalStockQuery .= " AND p.brand_id = ? ";
            $queryParams[] = (int) $brandId;
        }
        if (!empty($categoryId)) {
            $historicalStockQuery .= " AND p.category_id = ? ";
            $queryParams[] = (int) $categoryId;
        }

        $rowStock = $this->db->query($historicalStockQuery, $queryParams)->getRowArray();
        $totalStok = (int) ($rowStock['total_stok_hist'] ?? 0);


        // ------------- Card: Avg. Stock Velocity -------------
        $avgVelocityQuery = "
            SELECT AVG(T.avg_days_to_sell) as average_velocity
            FROM (
                SELECT
                    p.id,
                    COALESCE(AVG(DATEDIFF(so.tanggal_keluar, si.tanggal_masuk)), 0) AS avg_days_to_sell
                FROM tb_stock_out_items soi
                INNER JOIN tb_stock_out so ON so.id = soi.stock_out_id
                INNER JOIN tb_products p ON p.id = soi.product_id
                LEFT JOIN tb_stock_in_items sii ON sii.product_id = p.id
                LEFT JOIN tb_stock_in si ON si.id = sii.stock_in_id
                WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ?
        ";
        $avgVelocityParams = [$startDT, $endDT];

        if (!empty($productId)) {
            $avgVelocityQuery .= " AND p.id = ? ";
            $avgVelocityParams[] = (int) $productId;
        }
        if (!empty($brandId)) {
            $avgVelocityQuery .= " AND p.brand_id = ? ";
            $avgVelocityParams[] = (int) $brandId;
        }
        if (!empty($categoryId)) {
            $avgVelocityQuery .= " AND p.category_id = ? ";
            $avgVelocityParams[] = (int) $categoryId;
        }

        $avgVelocityQuery .= "
                GROUP BY p.id
            ) AS T
        ";

        $rowAvgVelocity = $this->db->query($avgVelocityQuery, $avgVelocityParams)->getRowArray();
        $avgStockVelocity = (float) ($rowAvgVelocity['average_velocity'] ?? 0);


        $cards = [
            'total_masuk'        => $totalMasuk,
            'total_keluar'       => $totalKeluar,
            'total_stok'         => $totalStok,
            'avg_stock_velocity' => $avgStockVelocity,
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

        // ------------- FINANCIAL SECTION (New Logic) -------------

        // Calculate Revenue (Ex. PPN), which is total revenue from product sales
        $bRevenueExPPN = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->where('soi.product_id IS NOT NULL'); // Ensure it's a product

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bRevenueExPPN->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bRevenueExPPN, 'p', true);
        }

        $rowRevenueExPPN = $bRevenueExPPN->get()->getRowArray();
        $revenueExPPN = (float) ($rowRevenueExPPN['total'] ?? 0);

        // Calculate Gross Profit (Revenue - Cost of Goods Sold)
        $bProfit = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->select('SUM(soi.jumlah * (soi.harga_jual_satuan - soi.harga_beli_satuan)) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bProfit->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bProfit, 'p', true);
        }

        $rowProfit = $bProfit->get()->getRowArray();
        $grossProfit = (float) ($rowProfit['total'] ?? 0);

        // Calculate Gross Margin %
        $grossMarginPercent = ($revenueExPPN > 0) ? (($grossProfit / $revenueExPPN) * 100) : 0;

        // Calculate PPN (11% of product revenue)
        $ppnAmount = $revenueExPPN * 0.11;

        // Calculate PPh 23 (2% of service revenue)
        $bServiceRevenue = $this->db->table('tb_stock_out_services sos')
            ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
            ->select('SUM(sos.biaya_jasa * sos.jumlah) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->get()->getRowArray();

        $totalServiceRevenue = (float) ($bServiceRevenue['total'] ?? 0);
        $pph23Amount = $totalServiceRevenue * 0.02;

        // --- NEW METRICS: Historical Values as of endDate ---

        // Query to get stock level for each product at the historical date (endDate)
        $historicalStockQuery = "
            SELECT
                p.id,
                p.harga_beli_saat_ini,
                p.harga_jual_saat_ini,
                (
                    p.stok_saat_ini
                    + COALESCE(so_after.total, 0)
                    - COALESCE(si_after.total, 0)
                ) as historical_stock
            FROM tb_products p
            LEFT JOIN (
                SELECT
                    sii_sub.product_id,
                    SUM(sii_sub.jumlah) as total
                FROM tb_stock_in_items sii_sub
                JOIN tb_stock_in si_sub ON si_sub.id = sii_sub.stock_in_id
                WHERE si_sub.tanggal_masuk > ?
                GROUP BY sii_sub.product_id
            ) AS si_after ON si_after.product_id = p.id
            LEFT JOIN (
                SELECT
                    soi_sub.product_id,
                    SUM(soi_sub.jumlah) as total
                FROM tb_stock_out_items soi_sub
                JOIN tb_stock_out so_sub ON so_sub.id = soi_sub.stock_out_id
                WHERE so_sub.tanggal_keluar > ?
                GROUP BY soi_sub.product_id
            ) AS so_after ON so_after.product_id = p.id
            WHERE 1=1
        ";

        $historicalValueParams = [$endDT, $endDT];

        // Apply filters to the historical value query
        if (!empty($productId)) {
            $historicalStockQuery .= " AND p.id = ? ";
            $historicalValueParams[] = (int) $productId;
        }
        if (!empty($brandId)) {
            $historicalStockQuery .= " AND p.brand_id = ? ";
            $historicalValueParams[] = (int) $brandId;
        }
        if (!empty($categoryId)) {
            $historicalStockQuery .= " AND p.category_id = ? ";
            $historicalValueParams[] = (int) $categoryId;
        }

        $historicalProducts = $this->db->query($historicalStockQuery, $historicalValueParams)->getResultArray();

        $inventoryValueHistorical = 0;
        $potentialRevenue = 0;

        foreach ($historicalProducts as $prod) {
            $stock = (float) $prod['historical_stock'];
            if ($stock > 0) {
                // Inventory Value: Historical Stock x Purchase Price
                $inventoryValueHistorical += $stock * (float)$prod['harga_beli_saat_ini'];
                // Potential Revenue: Historical Stock x Selling Price
                $potentialRevenue += $stock * (float)$prod['harga_jual_saat_ini'];
            }
        }

        // Potential Profit: Potential Revenue - Historical Inventory Value
        $potentialProfit = $potentialRevenue - $inventoryValueHistorical;

        // Count unpriced items (products without a selling price)
        $bUnpriced = $this->db->table('tb_products p')
            ->groupStart()
            ->where('p.harga_jual_saat_ini', 0)
            ->orWhere('p.harga_jual_saat_ini IS NULL', null, false)
            ->groupEnd();

        if (!empty($productId)) {
            $bUnpriced->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bUnpriced->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bUnpriced->where('p.category_id', (int) $categoryId);
        }

        $unpricedCount = $bUnpriced->countAllResults();

        // Assemble the 8 financial cards
        $financialCards = [
            'revenue_ex_ppn'             => $revenueExPPN + $totalServiceRevenue,
            'gross_profit'               => $grossProfit,
            'margin_percent'             => $grossMarginPercent,
            'ppn'                        => $ppnAmount,
            'pph_23'                     => $pph23Amount,
            'inventory_value_historical' => $inventoryValueHistorical,
            'potential_revenue'          => $potentialRevenue,
            'potential_profit'           => $potentialProfit,
            'unpriced_items_count'       => $unpricedCount,
        ];

        // ------------- CHART 1: Profit vs Revenue Trend (Daily) -------------
        $bProfitRevenueTrend = $this->db->table('tb_stock_out so')
            ->join('tb_stock_out_items soi', 'soi.stock_out_id = so.id', 'inner')
            ->select("DATE(so.tanggal_keluar) AS tanggal", false)
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS revenue', false)
            ->select('SUM(soi.jumlah * (soi.harga_jual_satuan - soi.harga_beli_satuan)) AS profit', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bProfitRevenueTrend->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bProfitRevenueTrend, 'p', true);
        }

        $profitRevenueTrendRows = $bProfitRevenueTrend->groupBy('DATE(so.tanggal_keluar)')
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        $profitRevenueTrend = [];
        foreach ($profitRevenueTrendRows as $r) {
            $profitRevenueTrend[] = [
                'tanggal' => $r['tanggal'],
                'revenue' => (float) ($r['revenue'] ?? 0),
                'profit'  => (float) ($r['profit'] ?? 0),
            ];
        }

        // ------------- CHART 2: Revenue by Category (Donut) -------------
        $bRevenueByCategory = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->join('tb_categories c', 'c.id = p.category_id', 'left')
            ->select("COALESCE(c.nama_kategori, 'No Category') AS category_name", false)
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS value', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bRevenueByCategory->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bRevenueByCategory->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bRevenueByCategory->where('p.category_id', (int) $categoryId);
        }

        $revenueByCategory = $bRevenueByCategory->groupBy('p.category_id')
            ->orderBy('value', 'DESC')
            ->get()->getResultArray();

        // ------------- CHART 3: Sales Velocity (Average Days to Sell) -------------
        // Calculate average days between stock in and stock out for each product
        $bSalesVelocity = $this->db->query("
            SELECT 
                p.nama_barang AS product_name,
                p.id AS product_id,
                COALESCE(AVG(DATEDIFF(so.tanggal_keluar, si.tanggal_masuk)), 0) AS avg_days_to_sell,
                SUM(soi.jumlah) AS total_sold
            FROM tb_stock_out_items soi
            INNER JOIN tb_stock_out so ON so.id = soi.stock_out_id
            INNER JOIN tb_products p ON p.id = soi.product_id
            LEFT JOIN tb_stock_in_items sii ON sii.product_id = p.id
            LEFT JOIN tb_stock_in si ON si.id = sii.stock_in_id
            WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ?
            " . (!empty($productId) ? " AND p.id = " . (int) $productId : "") . "
            " . (!empty($brandId) ? " AND p.brand_id = " . (int) $brandId : "") . "
            " . (!empty($categoryId) ? " AND p.category_id = " . (int) $categoryId : "") . "
            GROUP BY p.id
            ORDER BY avg_days_to_sell ASC
            LIMIT 10
        ", [$startDT, $endDT]);

        $salesVelocity = $bSalesVelocity->getResultArray();

        // ------------- CHART 4: Top 10 Revenue Products -------------
        $bTopRevenue = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->select('p.nama_barang AS product_name')
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS total_revenue', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bTopRevenue->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bTopRevenue->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bTopRevenue->where('p.category_id', (int) $categoryId);
        }

        $topRevenueProducts = $bTopRevenue->groupBy('p.id')
            ->orderBy('total_revenue', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // ------------- CHART 5: Top Products Cost vs Profit (Stacked Bar) -------------
        $bCostProfitStack = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->select('p.nama_barang AS product_name')
            ->select('SUM(soi.jumlah * soi.harga_beli_satuan) AS total_cost', false)
            ->select('SUM(soi.jumlah * (soi.harga_jual_satuan - soi.harga_beli_satuan)) AS total_profit', false)
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS total_revenue', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bCostProfitStack->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bCostProfitStack->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bCostProfitStack->where('p.category_id', (int) $categoryId);
        }

        $costProfitStack = $bCostProfitStack->groupBy('p.id')
            ->orderBy('total_revenue', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // ------------- CHART 6: Top 10 Clients by Revenue (Stacked Bar) -------------
        $partsFilterSQL = "";
        $partsQueryParams = [$startDT, $endDT];

        if (!empty($productId)) {
            $partsFilterSQL .= " AND p.id = ? ";
            $partsQueryParams[] = (int) $productId;
        }
        if (!empty($brandId)) {
            $partsFilterSQL .= " AND p.brand_id = ? ";
            $partsQueryParams[] = (int) $brandId;
        }
        if (!empty($categoryId)) {
            $partsFilterSQL .= " AND p.category_id = ? ";
            $partsQueryParams[] = (int) $categoryId;
        }

        $topClientsQuery = "
            SELECT
                c.nama_klien,
                COALESCE(parts.total, 0) as revenue_barang,
                COALESCE(services.total, 0) as revenue_jasa,
                (COALESCE(parts.total, 0) + COALESCE(services.total, 0)) as total_revenue
            FROM tb_clients c
            LEFT JOIN (
                SELECT
                    so.client_id,
                    SUM(soi.jumlah * soi.harga_jual_satuan) as total
                FROM tb_stock_out_items soi
                JOIN tb_stock_out so ON so.id = soi.stock_out_id
                JOIN tb_products p ON p.id = soi.product_id
                WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ? AND so.client_id IS NOT NULL
                $partsFilterSQL
                GROUP BY so.client_id
            ) AS parts ON c.id = parts.client_id
            LEFT JOIN (
                SELECT
                    so.client_id,
                    SUM(sos.jumlah * sos.biaya_jasa) as total
                FROM tb_stock_out_services sos
                JOIN tb_stock_out so ON so.id = sos.stock_out_id
                WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ? AND so.client_id IS NOT NULL
                GROUP BY so.client_id
            ) AS services ON c.id = services.client_id
            WHERE COALESCE(parts.total, 0) > 0 OR COALESCE(services.total, 0) > 0
            ORDER BY total_revenue DESC
            LIMIT 10
        ";

        $finalQueryParams = array_merge($partsQueryParams, [$startDT, $endDT]);
        $topClientsRevenue = $this->db->query($topClientsQuery, $finalQueryParams)->getResultArray();

        // ------------- INTERACTIVE REVENUE DONUT CHARTS -------------
        $bRevenueDonutCategory = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->join('tb_categories c', 'c.id = p.category_id', 'left')
            ->select("COALESCE(c.nama_kategori, 'No Category') AS label", false)
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS value', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bRevenueDonutCategory->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bRevenueDonutCategory->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bRevenueDonutCategory->where('p.category_id', (int) $categoryId);
        }

        $allCategoryRevenue = $bRevenueDonutCategory->groupBy('p.category_id')
            ->orderBy('value', 'DESC')
            ->get()->getResultArray();

        // Group top 9 + others
        $revenueDonutCategory = [];
        $othersTotal = 0;
        foreach ($allCategoryRevenue as $index => $item) {
            if ($index < 9) {
                $revenueDonutCategory[] = $item;
            } else {
                $othersTotal += (float) $item['value'];
            }
        }
        if ($othersTotal > 0) {
            $revenueDonutCategory[] = ['label' => 'Lainnya', 'value' => $othersTotal];
        }

        // 2. Revenue by Brand (Top 9 + Others)
        $bRevenueDonutBrand = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->join('tb_products p', 'p.id = soi.product_id', 'inner')
            ->join('tb_brands b', 'b.id = p.brand_id', 'left')
            ->select("COALESCE(b.nama_brand, 'No Brand') AS label", false)
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS value', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId)) {
            $bRevenueDonutBrand->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bRevenueDonutBrand->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bRevenueDonutBrand->where('p.category_id', (int) $categoryId);
        }

        $allBrandRevenue = $bRevenueDonutBrand->groupBy('p.brand_id')
            ->orderBy('value', 'DESC')
            ->get()->getResultArray();

        // Group top 9 + others
        $revenueDonutBrand = [];
        $othersTotal = 0;
        foreach ($allBrandRevenue as $index => $item) {
            if ($index < 9) {
                $revenueDonutBrand[] = $item;
            } else {
                $othersTotal += (float) $item['value'];
            }
        }
        if ($othersTotal > 0) {
            $revenueDonutBrand[] = ['label' => 'Lainnya', 'value' => $othersTotal];
        }

        // 3. Revenue: Barang vs Jasa (Tax-accurate)
        // Parts Revenue (Exclude PPN - already excluded in harga_jual_satuan)
        $bPartsRevenue = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
            ->select('SUM(soi.jumlah * soi.harga_jual_satuan) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bPartsRevenue->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bPartsRevenue, 'p', true);
        }

        $rowPartsRev = $bPartsRevenue->get()->getRowArray();
        $partsRevenue = (float) ($rowPartsRev['total'] ?? 0);

        // Service Revenue (Include PPh 23 - Gross Amount)
        $bServiceRevGross = $this->db->table('tb_stock_out_services sos')
            ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
            ->select('SUM(sos.biaya_jasa * sos.jumlah) AS total', false)
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->get()->getRowArray();

        $serviceRevenueGross = (float) ($bServiceRevGross['total'] ?? 0);

        $revenueDonutType = [
            ['label' => 'Revenue Barang', 'value' => $partsRevenue],
            ['label' => 'Revenue Jasa', 'value' => $serviceRevenueGross],
        ];

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
            // Financial section
            'financial_cards'         => $financialCards,
            'profit_revenue_trend'    => $profitRevenueTrend,
            'revenue_by_category'     => $revenueByCategory,
            'sales_velocity'          => $salesVelocity,
            'top_revenue_products'    => $topRevenueProducts,
            'cost_profit_stack'       => $costProfitStack,
            'top_clients_revenue'     => $topClientsRevenue,
            // Interactive Revenue Donut Charts
            'revenue_donut_charts'    => [
                'category' => $revenueDonutCategory,
                'brand'    => $revenueDonutBrand,
                'type'     => $revenueDonutType,
            ],
        ];

        return $this->response->setJSON($payload);
    }

    /**
     * Fetch Warehouse Data Only
     * Returns warehouse-specific data in JSON format
     * POST params: startDate (Y-m-d), endDate (Y-m-d), productId, brandId, categoryId
     */
    public function fetchWarehouseData()
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

        // ------------- Helper closure for dynamic filters -------------
        $applyProductFilter = function ($builder, string $productAlias, bool $joinProductIfNeeded = false) use ($productId, $brandId, $categoryId) {
            if (!empty($productId)) {
                $builder->where("$productAlias.id", (int) $productId);
            }
            if (!empty($brandId)) {
                if ($joinProductIfNeeded) {
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

        // ------------- 1. Total Stock (sum of all product stock) -------------
        $bTotalStock = $this->db->table('tb_products p')
            ->selectSum('p.stok_saat_ini', 'total_stok');

        if (!empty($productId)) {
            $bTotalStock->where('p.id', (int) $productId);
        }
        if (!empty($brandId)) {
            $bTotalStock->where('p.brand_id', (int) $brandId);
        }
        if (!empty($categoryId)) {
            $bTotalStock->where('p.category_id', (int) $categoryId);
        }

        $rowTotalStock = $bTotalStock->get()->getRowArray();
        $totalStok = (int) ($rowTotalStock['total_stok'] ?? 0);

        // ------------- 2. Stock In Volume (total qty barang masuk) -------------
        $bStockInVolume = $this->db->table('tb_stock_in_items sii')
            ->join('tb_stock_in si', 'si.id = sii.stock_in_id', 'inner');

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bStockInVolume->join('tb_products p', 'p.id = sii.product_id', 'inner');
            $applyProductFilter($bStockInVolume, 'p', true);
        }

        $bStockInVolume->where('si.tanggal_masuk >=', $startDT)
            ->where('si.tanggal_masuk <=', $endDT)
            ->selectSum('sii.jumlah', 'stock_in_volume');

        $rowStockIn = $bStockInVolume->get()->getRowArray();
        $stockInVolume = (int) ($rowStockIn['stock_in_volume'] ?? 0);

        // ------------- 3. Stock Out Volume (total qty barang keluar) -------------
        $bStockOutVolume = $this->db->table('tb_stock_out_items soi')
            ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner');

        if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
            $bStockOutVolume->join('tb_products p', 'p.id = soi.product_id', 'inner');
            $applyProductFilter($bStockOutVolume, 'p', true);
        }

        $bStockOutVolume->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT)
            ->selectSum('soi.jumlah', 'stock_out_volume');

        $rowStockOut = $bStockOutVolume->get()->getRowArray();
        $stockOutVolume = (int) ($rowStockOut['stock_out_volume'] ?? 0);

        // ------------- 4. Active Work Order Count (status_work_order = 'Proses') -------------
        $bActiveWO = $this->db->table('tb_stock_out so')
            ->where('so.status_work_order', 'Proses')
            ->where('so.tanggal_keluar >=', $startDT)
            ->where('so.tanggal_keluar <=', $endDT);

        // Note: Active WO count typically doesn't filter by product/brand/category
        // But if needed, uncomment below:
        // if (!empty($productId) || !empty($brandId) || !empty($categoryId)) {
        //     $bActiveWO->join('tb_stock_out_items soi', 'soi.stock_out_id = so.id', 'inner')
        //               ->join('tb_products p', 'p.id = soi.product_id', 'inner');
        //     $applyProductFilter($bActiveWO, 'p', true);
        // }

        $activeWOCount = $bActiveWO->countAllResults();

        // ------------- 5. Sales Velocity Data (Top 10 Products) -------------
        $salesVelocityQuery = "
            SELECT 
                p.nama_barang AS product_name,
                p.id AS product_id,
                COALESCE(AVG(DATEDIFF(so.tanggal_keluar, si.tanggal_masuk)), 0) AS avg_days_to_sell,
                SUM(soi.jumlah) AS total_sold
            FROM tb_stock_out_items soi
            INNER JOIN tb_stock_out so ON so.id = soi.stock_out_id
            INNER JOIN tb_products p ON p.id = soi.product_id
            LEFT JOIN tb_stock_in_items sii ON sii.product_id = p.id
            LEFT JOIN tb_stock_in si ON si.id = sii.stock_in_id
            WHERE so.tanggal_keluar >= ? AND so.tanggal_keluar <= ?
            " . (!empty($productId) ? " AND p.id = " . (int) $productId : "") . "
            " . (!empty($brandId) ? " AND p.brand_id = " . (int) $brandId : "") . "
            " . (!empty($categoryId) ? " AND p.category_id = " . (int) $categoryId : "") . "
            GROUP BY p.id
            ORDER BY avg_days_to_sell ASC
            LIMIT 10
        ";

        $bSalesVelocity = $this->db->query($salesVelocityQuery, [$startDT, $endDT]);
        $salesVelocityData = $bSalesVelocity->getResultArray();

        // ------------- Prepare Warehouse Response -------------
        $warehouseResponse = [
            'total_stok'          => $totalStok,
            'stock_in_volume'     => $stockInVolume,
            'stock_out_volume'    => $stockOutVolume,
            'active_wo_count'     => $activeWOCount,
            'sales_velocity_data' => $salesVelocityData,
        ];

        return $this->response->setJSON([
            'success'   => true,
            'warehouse' => $warehouseResponse,
        ]);
    }

    /**
     * Fetch Workshop Data Only
     * Returns workshop-specific data in JSON format
     * POST params: startDate (Y-m-d), endDate (Y-m-d), productId, brandId, categoryId
     */
    public function fetchWorkshopData()
    {
        try {
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

            // --- Subquery for filtering by product/brand/category ---
            $productFilterActive = !empty($productId) || !empty($brandId) || !empty($categoryId);
            $subQuery = null;
            if ($productFilterActive) {
                $subQuery = $this->db->table('tb_stock_out_items soi')
                    ->select('soi.stock_out_id')
                    ->join('tb_products p', 'p.id = soi.product_id', 'inner');

                if (!empty($productId)) $subQuery->where('p.id', (int)$productId);
                if (!empty($brandId)) $subQuery->where('p.brand_id', (int)$brandId);
                if (!empty($categoryId)) $subQuery->where('p.category_id', (int)$categoryId);
            }

            // ------------- 1. Active Work Order Count (status_work_order = 'Proses') -------------
            $bActiveWO = $this->db->table('tb_stock_out so')
                ->where('so.status_work_order', 'Proses')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                $bActiveWO->whereIn('so.id', $subQuery);
            }
            $activeWOCount = $bActiveWO->countAllResults();


            // ------------- 2. Total Service Done (tipe 'Workshop' + status 'Selesai') -------------
            $bServiceDone = $this->db->table('tb_stock_out so')
                ->where('so.tipe_transaksi', 'Workshop')
                ->where('so.status_work_order', 'Selesai')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                $bServiceDone->whereIn('so.id', $subQuery);
            }
            $totalServiceDone = $bServiceDone->countAllResults();

            // ------------- 3. Total Service Nominal (SUM biaya_jasa from completed services) -------------
            $bServiceNominal = $this->db->table('tb_stock_out_services sos')
                ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
                ->select('SUM(sos.biaya_jasa * sos.jumlah) AS total_nominal', false)
                ->where('so.status_work_order', 'Selesai')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                $bServiceNominal->whereIn('so.id', $subQuery);
            }
            $rowServiceNominal = $bServiceNominal->get()->getRowArray();
            $totalServiceNominal = (float) ($rowServiceNominal['total_nominal'] ?? 0);

            // ------------- 4. Average Service Value -------------
            $averageServiceValue = ($totalServiceDone > 0) ? ($totalServiceNominal / $totalServiceDone) : 0;

            // ------------- 5. Top Services Data (Top 10 by frequency) -------------
            $bTopServices = [];
            if ($this->db->tableExists('tb_services')) {
                $bTopServicesQuery = $this->db->table('tb_stock_out_services sos')
                    ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
                    ->join('tb_services s', 's.id = sos.service_id', 'inner')
                    ->select('s.id AS service_id, s.nama_jasa AS service_name, SUM(sos.jumlah) AS frequency, SUM(sos.biaya_jasa * sos.jumlah) AS total_revenue, AVG(sos.biaya_jasa) AS avg_price, COUNT(DISTINCT sos.stock_out_id) AS wo_count', false)
                    ->where('so.tanggal_keluar >=', $startDT)
                    ->where('so.tanggal_keluar <=', $endDT);

                if ($productFilterActive) {
                    $bTopServicesQuery->whereIn('so.id', $subQuery);
                }

                $bTopServices = $bTopServicesQuery->groupBy('s.id')
                    ->orderBy('frequency', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
            }

            // ------------- 6. Workshop Activity Trend (Daily completed WO) -------------
            $bActivityTrend = $this->db->table('tb_stock_out so')
                ->select("DATE(so.tanggal_keluar) AS tanggal", false)
                ->select('COUNT(so.id) AS wo_completed', false)
                ->where('so.tipe_transaksi', 'Workshop')
                ->where('so.status_work_order', 'Selesai')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                $bActivityTrend->whereIn('so.id', $subQuery);
            }
            $activityTrend = $bActivityTrend->groupBy('DATE(so.tanggal_keluar)')
                ->orderBy('tanggal', 'ASC')
                ->get()->getResultArray();

            // ------------- 7. Service vs Parts Ratio (Quantity-based for Workshop) -------------
            // Total Service Quantity (Workshop only)
            $bServiceQty = $this->db->table('tb_stock_out_services sos')
                ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
                ->select('SUM(sos.jumlah) AS total_qty, SUM(sos.biaya_jasa * sos.jumlah) AS total_revenue', false)
                ->where('so.tipe_transaksi', 'Workshop')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                $bServiceQty->whereIn('so.id', $subQuery);
            }
            $rowServiceQty = $bServiceQty->get()->getRowArray();
            $totalServiceQty = (int) ($rowServiceQty['total_qty'] ?? 0);
            $totalServiceRevenue = (float) ($rowServiceQty['total_revenue'] ?? 0);

            // Total Parts Quantity (Workshop only)
            $bPartsQty = $this->db->table('tb_stock_out_items soi')
                ->join('tb_stock_out so', 'so.id = soi.stock_out_id', 'inner')
                ->select('SUM(soi.jumlah) AS total_qty, SUM(soi.jumlah * soi.harga_jual_satuan) AS total_revenue', false)
                ->where('so.tipe_transaksi', 'Workshop')
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT);

            if ($productFilterActive) {
                // Here we can join directly since we start from tb_stock_out_items
                $bPartsQty->join('tb_products p', 'p.id = soi.product_id', 'inner');
                if (!empty($productId)) $bPartsQty->where('p.id', (int) $productId);
                if (!empty($brandId)) $bPartsQty->where('p.brand_id', (int) $brandId);
                if (!empty($categoryId)) $bPartsQty->where('p.category_id', (int) $categoryId);
            }
            $rowPartsQty = $bPartsQty->get()->getRowArray();
            $totalPartsQty = (int) ($rowPartsQty['total_qty'] ?? 0);
            $totalPartsRevenue = (float) ($rowPartsQty['total_revenue'] ?? 0);

            $serviceVsPartsRatio = [
                'total_service_qty' => $totalServiceQty,
                'total_parts_qty' => $totalPartsQty,
                'total_service_revenue' => $totalServiceRevenue,
                'total_parts_revenue' => $totalPartsRevenue,
            ];

            // ------------- 8. Transaction Ratio ('Beli Putus' vs 'Workshop') -------------
            $bTransactionRatio = $this->db->table('tb_stock_out so')
                // Gunakan alias tabel untuk menghindari error kolom ambigu
                ->select("so.tipe_transaksi, COUNT(so.id) as count")
                ->where('so.tanggal_keluar >=', $startDT)
                ->where('so.tanggal_keluar <=', $endDT)
                ->whereIn('so.tipe_transaksi', ['Beli Putus', 'Workshop']);

            if ($productFilterActive) {
                // Memastikan filter produk tetap berjalan melalui subquery
                $bTransactionRatio->whereIn('so.id', $subQuery);
            }

            $transactionRatioResult = $bTransactionRatio->groupBy('so.tipe_transaksi')->get()->getResultArray();

            // Inisialisasi default menjamin chart tidak error jika data kosong
            $mappedData = ['Parts Only' => 0, 'With Services' => 0];

            foreach ($transactionRatioResult as $row) {
                if ($row['tipe_transaksi'] === 'Beli Putus') {
                    $mappedData['Parts Only'] = (int) $row['count'];
                } elseif ($row['tipe_transaksi'] === 'Workshop') {
                    $mappedData['With Services'] = (int) $row['count'];
                }
            }

            // Format final untuk dikonsumsi frontend (Chart.js/amCharts/Morris)
            $transactionRatio = [];
            foreach ($mappedData as $label => $value) {
                $transactionRatio[] = ['label' => $label, 'value' => $value];
            }

            // ------------- Prepare Workshop Response -------------
            $workshopResponse = [
                'active_wo_count'       => $activeWOCount,
                'total_service_done'    => $totalServiceDone,
                'total_service_nominal' => $totalServiceNominal,
                'average_service_value' => $averageServiceValue,
                'top_services_data'     => $bTopServices,
                'activity_trend'        => $activityTrend,
                'service_vs_parts'      => $serviceVsPartsRatio,
                'transaction_ratio'     => $transactionRatio,
            ];

            return $this->response->setJSON([
                'success'  => true,
                'workshop' => $workshopResponse,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in fetchWorkshopData: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Failed to fetch workshop data',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
