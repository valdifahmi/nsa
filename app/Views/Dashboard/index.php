<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>

<!-- Optional Morris CSS (for better donut chart style) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" />

<div class="row">

    <!-- Filters -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="dashboardFilters" class="form-inline">
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_start_date" class="mr-2">Start</label>
                        <input type="date" id="filter_start_date" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_end_date" class="mr-2">End</label>
                        <input type="date" id="filter_end_date" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_product" class="mr-2">Product</label>
                        <select id="filter_product" class="form-control form-control-sm">
                            <option value="">All</option>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['nama_barang']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_brand" class="mr-2">Brand</label>
                        <select id="filter_brand" class="form-control form-control-sm">
                            <option value="">All</option>
                            <?php if (!empty($brands)): ?>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= esc($b['id']) ?>"><?= esc($b['nama_brand']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_category" class="mr-2">Category</label>
                        <select id="filter_category" class="form-control form-control-sm">
                            <option value="">All</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= esc($c['id']) ?>"><?= esc($c['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="button" id="btn-apply-filter" class="btn btn-primary btn-sm mb-2 mr-2">
                        <i class="ri-filter-2-line"></i> Apply
                    </button>
                    <button type="button" id="btn-reset-filter" class="btn btn-secondary btn-sm mb-2">
                        Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Greeting + Cards -->
    <div class="col-lg-4">
        <div class="card card-transparent card-block card-stretch card-height border-none">
            <div class="card-body p-0 mt-lg-2 mt-0">
                <h3 class="mb-3">Hi, Good Day <?= session()->get('user')['nama_lengkap'] ?? 'User' ?></h3>
                <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-4">
        <div class="card bg-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title text-white mb-0">Warehouse Overview</h4>
                <div class="quick-menu">
                    <a href="<?= base_url('purchase') ?>" class="btn btn-sm btn-outline-light mr-2">
                        <i class="ri-add-circle-line"></i> Stock-In
                    </a>
                    <a href="<?= base_url('sale') ?>" class="btn btn-sm btn-outline-light">
                        <i class="ri-add-circle-line"></i> Stock-Out
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2 bg-info-light">
                                <i class="ri-arrow-down-circle-line" style="font-size: 40px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Stock-In</p>
                                <h4 id="card-total-masuk">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="bg-info iq-progress progress-1" style="width: 0%"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2 bg-danger-light">
                                <i class="ri-arrow-up-circle-line" style="font-size: 40px; color: #ef4444;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Stock-Out</p>
                                <h4 id="card-total-keluar">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="bg-danger iq-progress progress-1" style="width: 0%"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2 bg-success-light">
                                <i class="ri-stack-line" style="font-size: 40px; color: #10b981;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Total Stock</p>
                                <h4 id="card-total-stok">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="bg-success iq-progress progress-1" style="width: 0%"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview line/area -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Overview</h4>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <div id="overview-loading" class="text-muted small" style="display:none;">Loading...</div>
                </div>
            </div>
            <div class="card-body">
                <div id="overview-chart-div"></div>
            </div>
        </div>
    </div>

    <!-- Top products bar -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top Products</h4>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <div id="top-products-loading" class="text-muted small" style="display:none;">Loading...</div>
                </div>
            </div>
            <div class="card-body">
                <div id="top-products-chart-div"></div>
            </div>
        </div>
    </div>

    <!-- Sales Velocity - Full Width -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Sales Velocity (Top 10 - Days to Sell)</h4>
                </div>
                <div class="card-header-toolbar">
                    <small class="text-muted">Lower is better (faster selling)</small>
                </div>
            </div>
            <div class="card-body">
                <div id="sales-velocity-chart-div" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Low stock -->
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Hampir Habis</h4>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <a href="<?= base_url('report/stockReport'); ?>">View all</a>
                </div>
            </div>
            <div class="card-body">
                <ul id="low-stock-list" class="list-unstyled row top-product mb-0">
                    <!-- filled via JS -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Donut proportion -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Proporsi Stock</h4>
                </div>
                <div class="card-header-toolbar d-flex align-items-center">
                    <select id="donut-type-select" class="form-control form-control-sm">
                        <option value="product">Product</option>
                        <option value="brand">Brand</option>
                        <option value="category">Category</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="donut-chart-div" style="height:300px;"></div>
            </div>
        </div>
    </div>



    <!-- ============= WORKSHOP SECTION ============= -->
    <div class="col-lg-12 mt-4">
        <div class="card" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
            <div class="card-header d-flex justify-content-between align-items-center border-0">
                <h4 class="card-title text-white mb-0">Workshop Overview</h4>
                <div class="quick-menu">
                    <a href="<?= base_url('sale?status=Proses') ?>" class="btn btn-sm btn-outline-light">
                        <i class="ri-file-list-3-line"></i> Work Order Aktif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Workshop Cards -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(6, 182, 212, 0.1);">
                                <i class="ri-file-list-line" style="font-size: 40px; color: #06b6d4;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Active Work Orders</p>
                                <h4 id="card-active-wo">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #06b6d4;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(16, 185, 129, 0.1);">
                                <i class="ri-checkbox-circle-line" style="font-size: 40px; color: #10b981;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Jasa Selesai</p>
                                <h4 id="card-service-done">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #10b981;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(139, 92, 246, 0.1);">
                                <i class="ri-money-dollar-circle-line" style="font-size: 40px; color: #8b5cf6;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Nominal Jasa</p>
                                <h4 id="card-service-nominal">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #8b5cf6;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(245, 158, 11, 0.1);">
                                <i class="ri-bar-chart-box-line" style="font-size: 40px; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Avg. Service Value</p>
                                <h4 id="card-avg-service">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #f59e0b;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Services Chart - Full Width -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top 10 Services by Frequency</h4>
                </div>
                <div class="card-header-toolbar">
                    <small class="text-muted">Most requested services</small>
                </div>
            </div>
            <div class="card-body">
                <div id="top-services-chart-div" style="height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Workshop Activity Trend Chart -->
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Workshop Activity Trend</h4>
                </div>
                <div class="card-header-toolbar">
                    <small class="text-muted">Daily completed work orders</small>
                </div>
            </div>
            <div class="card-body">
                <div id="workshop-activity-chart-div" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Service vs Parts Ratio Chart -->
    <div class="col-lg-4">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Service vs Parts Ratio</h4>
                </div>
                <div class="card-header-toolbar">
                    <small class="text-muted">Workshop revenue breakdown</small>
                </div>
            </div>
            <div class="card-body">
                <div id="service-parts-ratio-chart-div" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- ============= END OF WORKSHOP SECTION ============= -->

    <!-- ============= FINANCIAL SECTION ============= -->
    <div class="col-lg-12 mt-4">
        <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-header d-flex justify-content-between align-items-center border-0">
                <h4 class="card-title text-white mb-0">Financial Overview</h4>
                <div class="quick-menu">
                    <a href="<?= base_url('invoice') ?>" class="btn btn-sm btn-outline-light">
                        <i class="ri-file-list-3-line"></i> Daftar Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Widget for Unpriced Items -->
    <div class="col-lg-12">
        <div id="unpriced-alert" class="alert alert-warning alert-dismissible fade show" role="alert" style="display: none;">
            <i class="ri-alert-line"></i>
            <strong>Perhatian!</strong> Terdapat <span id="unpriced-count">0</span> produk yang belum diberikan harga.
            <a href="<?= base_url('pricing') ?>" class="alert-link">Klik di sini untuk mengatur harga</a>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <!-- Financial Cards - Row 1 -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(245, 158, 11, 0.1);">
                                <i class="ri-archive-line" style="font-size: 40px; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Inventory Value</p>
                                <h4 id="card-inventory-value">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #f59e0b;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(16, 185, 129, 0.1);">
                                <i class="ri-money-dollar-circle-line" style="font-size: 40px; color: #10b981;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Gross Profit</p>
                                <h4 id="card-gross-profit">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #10b981;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(99, 102, 241, 0.1);">
                                <i class="ri-percent-line" style="font-size: 40px; color: #6366f1;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Gross Margin %</p>
                                <h4 id="card-gross-margin">0%</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #6366f1;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Cards - Row 2 -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(239, 68, 68, 0.1);">
                                <i class="ri-file-text-line" style="font-size: 40px; color: #ef4444;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Total PPN (11%)</p>
                                <h4 id="card-total-ppn">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #ef4444;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(139, 92, 246, 0.1);">
                                <i class="ri-file-shield-line" style="font-size: 40px; color: #8b5cf6;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Total PPh 23 (2%)</p>
                                <h4 id="card-total-pph23">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #8b5cf6;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 1: Profit vs Revenue Trend -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Profit vs Revenue Trend</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="profit-revenue-chart-div"></div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Revenue by Category -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Revenue by Category</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="revenue-category-chart-div" style="height:300px;"></div>
            </div>
        </div>
    </div>



    <!-- Chart 4: Top Margin Products -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top 10 High Margin Products</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="top-margin-chart-div"></div>
            </div>
        </div>
    </div>

    <!-- Chart 5: Cost vs Profit Breakdown (Stacked Bar) -->
    <div class="col-12">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top 10 Products - Cost vs Profit Breakdown</h4>
                </div>
                <div class="card-header-toolbar">
                    <small class="text-muted">Modal (HPP) + Margin = Revenue</small>
                </div>
            </div>
            <div class="card-body">
                <div id="cost-profit-stack-chart-div" style="height: 400px;"></div>
            </div>
        </div>
    </div>

</div>
<!-- Page end  -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Charts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script>
    const BASE_URL = '<?= base_url() ?>';

    let donutInstance = null;
    let donutData = {
        product: [],
        brand: [],
        category: []
    };

    // Ensure chart libraries are ready before rendering
    function runAfterLibsReady(cb, retry = 0) {
        try {
            const morrisReady = typeof window.Morris !== 'undefined' && typeof window.Raphael !== 'undefined';
            if (morrisReady) {
                cb();
                return;
            }
        } catch (e) {
            // ignore
        }
        if (retry < 20) { // retry ~2s
            setTimeout(function() {
                runAfterLibsReady(cb, retry + 1);
            }, 100);
        } else {
            // fallback: run anyway
            cb();
        }
    }

    function numberFormat(x) {
        x = parseInt(x || 0, 10);
        return x.toLocaleString('id-ID');
    }

    function updateOverviewChart(categories, masuk, keluar) {
        const data = categories.map(function(cat, i) {
            return {
                waktu: cat,
                masuk: parseInt(masuk[i] || 0, 10),
                keluar: parseInt(keluar[i] || 0, 10)
            };
        });
        if (!window.morrisOverview) {
            window.morrisOverview = new Morris.Area({
                element: 'overview-chart-div',
                data: data,
                xkey: 'waktu',
                ykeys: ['masuk', 'keluar'],
                labels: ['Masuk', 'Keluar'],
                behaveLikeLine: true,
                parseTime: false,
                lineColors: ['#3b82f6', '#ef4444'],
                hideHover: 'auto',
                resize: true
            });
        } else {
            window.morrisOverview.setData(data);
        }
    }

    function updateTopProductsChart(labels, values) {
        // Sort descending by value
        const items = labels.map(function(lbl, i) {
            return {
                label: lbl,
                value: parseInt(values[i] || 0, 10)
            };
        }).sort(function(a, b) {
            return b.value - a.value;
        });

        const data = items.map(function(it) {
            return {
                label: it.label,
                value: it.value
            };
        });

        if (!window.morrisTopProducts) {
            window.morrisTopProducts = new Morris.Bar({
                element: 'top-products-chart-div',
                data: data,
                xkey: 'label',
                ykeys: ['value'],
                labels: ['Total'],
                barColors: ['#60a5fa'],
                barRadius: [10, 10, 0, 0],
                hideHover: 'auto',
                resize: true,
                gridTextSize: 11,
                barSizeRatio: 0.6
            });
        } else {
            window.morrisTopProducts.setData(data);
        }
    }

    function renderDonutChart(type) {
        const data = donutData[type] || [];
        if (donutInstance) {
            $('#donut-chart-div').empty();
            donutInstance = null;
        }
        if (!data.length) {
            $('#donut-chart-div').html('<div class="text-center text-muted">No data</div>');
            return;
        }
        donutInstance = new Morris.Donut({
            element: 'donut-chart-div',
            data: data.map(d => ({
                label: d.label,
                value: parseInt(d.value || 0, 10)
            })),
            colors: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#06b6d4', '#84cc16', '#f43f5e'],
            resize: true
        });
    }

    function renderLowStock(list) {
        const $ul = $('#low-stock-list');
        $ul.empty();
        if (!list || !list.length) {
            $ul.append('<li class="col-12"><div class="text-center text-muted">Tidak ada item hampir habis</div></li>');
            return;
        }
        list.forEach(item => {
            const imagePath = (item.image && item.image !== 'default.png') ?
                (BASE_URL + 'uploads/products/' + item.image) :
                (BASE_URL + 'dist/assets/images/default.png');
            const color = (parseInt(item.stok_saat_ini) <= parseInt(item.min_stok)) ? 'bg-danger-light' : 'bg-warning-light';
            const html = `
                <li class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card card-block card-stretch card-height mb-0" style="min-height: 250px;">
                        <div class="card-body" style="padding-bottom: 20px;">
                            <div class="${color} d-flex align-items-center justify-content-center" style="height: 100px; border-radius: 15px; overflow: hidden;">
                                <img src="${imagePath}" class="style-img img-fluid m-auto p-3" alt="image" style="max-height:100px;object-fit:contain;border-radius:10px;">
                            </div>
                            <div class="style-text text-left mt-4">
                                <h6 class="mb-1 text-truncate" title="${item.nama_barang}">${item.nama_barang}</h6>
                                <p class="mb-0" style="margin-top:8px;white-space:normal;line-height:1.35;">Stock : ${item.stok_saat_ini} Min Stock ${item.min_stok}</p>
                            </div>
                        </div>
                    </div>
                </li>
            `;
            $ul.append(html);
        });
    }

    function formatRupiah(x) {
        const num = parseFloat(x || 0);
        return 'Rp ' + new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }

    function formatRupiahCompact(x) {
        const num = parseFloat(x || 0);

        // If >= 1 billion, show in billions (M)
        if (num >= 1000000000) {
            const billions = num / 1000000000;
            return 'Rp ' + billions.toFixed(2) + ' M';
        }
        // If >= 1 million, show in millions (Jt)
        else if (num >= 1000000) {
            const millions = num / 1000000;
            return 'Rp ' + millions.toFixed(2) + ' Jt';
        }
        // If < 1 million, show full format
        else {
            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }
    }

    function formatRupiahFull(x) {
        const num = parseFloat(x || 0);
        return 'Rp ' + new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }

    function updateCards(cards) {
        $('#card-total-masuk').text(numberFormat(cards.total_masuk));
        $('#card-total-keluar').text(numberFormat(cards.total_keluar));
        $('#card-total-stok').text(numberFormat(cards.total_stok));
    }

    function updateFinancialCards(financial) {
        // Inventory Value - auto format with info icon
        const inventoryCompact = formatRupiahCompact(financial.inventory_value || 0);
        const inventoryFull = formatRupiahFull(financial.inventory_value || 0);
        $('#card-inventory-value').html(
            inventoryCompact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #f59e0b; cursor: help;" ' +
            'title="' + inventoryFull + '" data-toggle="tooltip"></i>'
        );

        // Gross Profit - auto format with info icon
        const profitCompact = formatRupiahCompact(financial.total_profit || 0);
        const profitFull = formatRupiahFull(financial.total_profit || 0);
        $('#card-gross-profit').html(
            profitCompact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #10b981; cursor: help;" ' +
            'title="' + profitFull + '" data-toggle="tooltip"></i>'
        );

        // Gross Margin % - percentage format
        const marginPercent = parseFloat(financial.gross_margin_percent || 0);
        $('#card-gross-margin').text(marginPercent.toFixed(2) + '%');

        // Total PPN - auto format with info icon
        const ppnCompact = formatRupiahCompact(financial.ppn_amount || 0);
        const ppnFull = formatRupiahFull(financial.ppn_amount || 0);
        $('#card-total-ppn').html(
            ppnCompact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #ef4444; cursor: help;" ' +
            'title="' + ppnFull + '" data-toggle="tooltip"></i>'
        );

        // Total PPh 23 - auto format with info icon
        const pph23Compact = formatRupiahCompact(financial.pph23_amount || 0);
        const pph23Full = formatRupiahFull(financial.pph23_amount || 0);
        $('#card-total-pph23').html(
            pph23Compact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #8b5cf6; cursor: help;" ' +
            'title="' + pph23Full + '" data-toggle="tooltip"></i>'
        );

        // Unpriced Items Alert
        const unpricedCount = parseInt(financial.unpriced_items_count || 0, 10);
        if (unpricedCount > 0) {
            $('#unpriced-count').text(unpricedCount);
            $('#unpriced-alert').show();
        } else {
            $('#unpriced-alert').hide();
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    }

    function updateProfitRevenueChart(data) {
        const chartData = data.map(d => ({
            tanggal: d.tanggal,
            revenue: parseFloat(d.revenue || 0),
            profit: parseFloat(d.profit || 0)
        }));

        if (!window.morrisProfitRevenue) {
            window.morrisProfitRevenue = new Morris.Area({
                element: 'profit-revenue-chart-div',
                data: chartData,
                xkey: 'tanggal',
                ykeys: ['revenue', 'profit'],
                labels: ['Revenue', 'Profit'],
                behaveLikeLine: true,
                parseTime: false,
                lineColors: ['#10b981', '#6366f1'],
                hideHover: 'auto',
                resize: true,
                yLabelFormat: function(y) {
                    return formatRupiah(y);
                }
            });
        } else {
            window.morrisProfitRevenue.setData(chartData);
        }
    }

    function updateRevenueCategoryChart(data) {
        if (window.morrisRevenueCategory) {
            $('#revenue-category-chart-div').empty();
            window.morrisRevenueCategory = null;
        }

        if (!data || !data.length) {
            $('#revenue-category-chart-div').html('<div class="text-center text-muted">No data</div>');
            return;
        }

        window.morrisRevenueCategory = new Morris.Donut({
            element: 'revenue-category-chart-div',
            data: data.map(d => ({
                label: d.category_name,
                value: parseFloat(d.value || 0)
            })),
            colors: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#06b6d4'],
            resize: true,
            formatter: function(y) {
                return formatRupiah(y);
            }
        });
    }

    function updateSalesVelocityChart(data) {
        if (!data || !data.length) {
            $('#sales-velocity-chart-div').html('<div class="text-center text-muted py-5">No sales velocity data available</div>');
            return;
        }

        const chartData = data.map(d => ({
            product: d.product_name,
            days: parseFloat(d.avg_days_to_sell || 0),
            sold: parseInt(d.total_sold || 0, 10)
        }));

        if (window.morrisSalesVelocity) {
            $('#sales-velocity-chart-div').empty();
            window.morrisSalesVelocity = null;
        }

        window.morrisSalesVelocity = new Morris.Bar({
            element: 'sales-velocity-chart-div',
            data: chartData,
            xkey: 'product',
            ykeys: ['days'],
            labels: ['Avg Days to Sell'],
            barColors: ['#06b6d4'],
            barRadius: [10, 10, 0, 0],
            hideHover: 'auto',
            resize: true,
            gridTextSize: 10,
            barSizeRatio: 0.5,
            xLabelAngle: 45,
            hoverCallback: function(index, options, content, row) {
                return '<div class="morris-hover-row-label">' + row.product + '</div>' +
                    '<div class="morris-hover-point">Days: ' + row.days.toFixed(1) + '</div>' +
                    '<div class="morris-hover-point">Total Sold: ' + row.sold + '</div>';
            }
        });
    }

    function updateTopMarginChart(data) {
        const chartData = data.map(d => ({
            product: d.product_name,
            margin: parseFloat(d.margin_percentage || 0)
        }));

        if (!window.morrisTopMargin) {
            window.morrisTopMargin = new Morris.Bar({
                element: 'top-margin-chart-div',
                data: chartData,
                xkey: 'product',
                ykeys: ['margin'],
                labels: ['Margin %'],
                barColors: ['#8b5cf6'],
                barRadius: [10, 10, 0, 0],
                hideHover: 'auto',
                resize: true,
                gridTextSize: 11,
                barSizeRatio: 0.6,
                yLabelFormat: function(y) {
                    return y.toFixed(1) + '%';
                }
            });
        } else {
            window.morrisTopMargin.setData(chartData);
        }
    }

    function updateCostProfitStackChart(data) {
        if (!data || !data.length) {
            $('#cost-profit-stack-chart-div').html('<div class="text-center text-muted py-5">No data available</div>');
            return;
        }

        const chartData = data.map(d => ({
            product: d.product_name,
            cost: parseFloat(d.total_cost || 0),
            profit: parseFloat(d.total_profit || 0),
            revenue: parseFloat(d.total_revenue || 0)
        }));

        if (window.morrisCostProfitStack) {
            $('#cost-profit-stack-chart-div').empty();
            window.morrisCostProfitStack = null;
        }

        window.morrisCostProfitStack = new Morris.Bar({
            element: 'cost-profit-stack-chart-div',
            data: chartData,
            xkey: 'product',
            ykeys: ['cost', 'profit'],
            labels: ['Modal (HPP)', 'Profit (Margin)'],
            stacked: true,
            barColors: ['#ef4444', '#10b981'],
            hideHover: 'auto',
            resize: true,
            gridTextSize: 10,
            barSizeRatio: 0.6,
            xLabelAngle: 45,
            yLabelFormat: function(y) {
                return formatRupiah(y);
            },
            hoverCallback: function(index, options, content, row) {
                return '<div class="morris-hover-row-label"><strong>' + row.product + '</strong></div>' +
                    '<div class="morris-hover-point" style="color: #ef4444;">Modal (HPP): ' + formatRupiah(row.cost) + '</div>' +
                    '<div class="morris-hover-point" style="color: #10b981;">Profit (Margin): ' + formatRupiah(row.profit) + '</div>' +
                    '<div class="morris-hover-point" style="border-top: 1px solid #ccc; margin-top: 5px; padding-top: 5px;"><strong>Total Revenue: ' + formatRupiah(row.revenue) + '</strong></div>';
            }
        });
    }

    function updateWorkshopCards(workshop) {
        $('#card-active-wo').text(numberFormat(workshop.active_wo_count || 0));
        $('#card-service-done').text(numberFormat(workshop.total_service_done || 0));

        // Nominal Jasa - auto format (Jt or M) with info icon
        const nominalCompact = formatRupiahCompact(workshop.total_service_nominal || 0);
        const nominalFull = formatRupiahFull(workshop.total_service_nominal || 0);
        $('#card-service-nominal').html(
            nominalCompact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #8b5cf6; cursor: help;" ' +
            'title="' + nominalFull + '" data-toggle="tooltip"></i>'
        );

        // Avg Service Value - auto format (Jt or M) with info icon
        const avgCompact = formatRupiahCompact(workshop.average_service_value || 0);
        const avgFull = formatRupiahFull(workshop.average_service_value || 0);
        $('#card-avg-service').html(
            avgCompact +
            ' <i class="ri-information-line" style="font-size: 16px; color: #f59e0b; cursor: help;" ' +
            'title="' + avgFull + '" data-toggle="tooltip"></i>'
        );

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    }

    function updateTopServicesChart(data) {
        if (!data || !data.length) {
            $('#top-services-chart-div').html('<div class="text-center text-muted py-5">No services data available</div>');
            return;
        }

        const chartData = data.map(d => ({
            service: d.service_name,
            frequency: parseInt(d.frequency || 0, 10),
            revenue: parseFloat(d.total_revenue || 0)
        }));

        if (window.morrisTopServices) {
            $('#top-services-chart-div').empty();
            window.morrisTopServices = null;
        }

        window.morrisTopServices = new Morris.Bar({
            element: 'top-services-chart-div',
            data: chartData,
            xkey: 'service',
            ykeys: ['frequency'],
            labels: ['Frequency'],
            barColors: ['#06b6d4'],
            barRadius: [10, 10, 0, 0],
            hideHover: 'auto',
            resize: true,
            gridTextSize: 10,
            barSizeRatio: 0.5,
            xLabelAngle: 45,
            hoverCallback: function(index, options, content, row) {
                return '<div class="morris-hover-row-label">' + row.service + '</div>' +
                    '<div class="morris-hover-point">Frequency: ' + row.frequency + '</div>' +
                    '<div class="morris-hover-point">Revenue: ' + formatRupiah(row.revenue) + '</div>';
            }
        });
    }

    function updateWorkshopActivityChart(data) {
        if (!data || !data.length) {
            $('#workshop-activity-chart-div').html('<div class="text-center text-muted py-5">No activity data available</div>');
            return;
        }

        const chartData = data.map(d => ({
            tanggal: d.tanggal,
            wo_completed: parseInt(d.wo_completed || 0, 10)
        }));

        if (window.morrisWorkshopActivity) {
            $('#workshop-activity-chart-div').empty();
            window.morrisWorkshopActivity = null;
        }

        window.morrisWorkshopActivity = new Morris.Area({
            element: 'workshop-activity-chart-div',
            data: chartData,
            xkey: 'tanggal',
            ykeys: ['wo_completed'],
            labels: ['WO Completed'],
            behaveLikeLine: true,
            parseTime: false,
            lineColors: ['#06b6d4'],
            fillOpacity: 0.6,
            hideHover: 'auto',
            resize: true,
            pointSize: 3,
            gridTextSize: 10,
            hoverCallback: function(index, options, content, row) {
                return '<div class="morris-hover-row-label">' + row.tanggal + '</div>' +
                    '<div class="morris-hover-point">WO Completed: ' + row.wo_completed + '</div>';
            }
        });
    }

    function updateServicePartsRatioChart(data) {
        if (window.morrisServicePartsRatio) {
            $('#service-parts-ratio-chart-div').empty();
            window.morrisServicePartsRatio = null;
        }

        const totalServiceQty = parseInt(data.total_service_qty || 0, 10);
        const totalPartsQty = parseInt(data.total_parts_qty || 0, 10);

        if (totalServiceQty === 0 && totalPartsQty === 0) {
            $('#service-parts-ratio-chart-div').html('<div class="text-center text-muted">No data available</div>');
            return;
        }

        const chartData = [];
        if (totalServiceQty > 0) {
            chartData.push({
                label: 'Service (' + numberFormat(totalServiceQty) + ')',
                value: totalServiceQty
            });
        }
        if (totalPartsQty > 0) {
            chartData.push({
                label: 'Parts (' + numberFormat(totalPartsQty) + ')',
                value: totalPartsQty
            });
        }

        window.morrisServicePartsRatio = new Morris.Donut({
            element: 'service-parts-ratio-chart-div',
            data: chartData,
            colors: ['#06b6d4', '#8b5cf6'],
            resize: true,
            formatter: function(y) {
                return numberFormat(y) + ' items';
            }
        });
    }

    function fetchWorkshopData() {
        const payload = {
            startDate: $('#filter_start_date').val(),
            endDate: $('#filter_end_date').val(),
            productId: $('#filter_product').val(),
            brandId: $('#filter_brand').val(),
            categoryId: $('#filter_category').val(),
        };

        $.ajax({
            url: BASE_URL + 'dashboard/fetchWorkshopData',
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function(res) {
                try {
                    if (res.success && res.workshop) {
                        updateWorkshopCards(res.workshop);
                        updateTopServicesChart(res.workshop.top_services_data || []);
                        updateWorkshopActivityChart(res.workshop.activity_trend || []);
                        updateServicePartsRatioChart(res.workshop.service_vs_parts || {});
                    }
                } catch (e) {
                    console.error('Error updating workshop data:', e);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching workshop data:', error);
            }
        });
    }

    function updateDashboardData() {
        $('#overview-loading, #top-products-loading').show();
        const payload = {
            startDate: $('#filter_start_date').val(),
            endDate: $('#filter_end_date').val(),
            productId: $('#filter_product').val(),
            brandId: $('#filter_brand').val(),
            categoryId: $('#filter_category').val(),
        };
        $.ajax({
            url: BASE_URL + 'dashboard/fetchData',
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function(res) {
                $('#overview-loading, #top-products-loading').hide();
                try {
                    updateCards(res.cards || {});

                    // Overview
                    const cats = (res.overview_chart || []).map(r => r.waktu);
                    const masuk = (res.overview_chart || []).map(r => parseInt(r.masuk || 0, 10));
                    const keluar = (res.overview_chart || []).map(r => parseInt(r.keluar || 0, 10));
                    updateOverviewChart(cats, masuk, keluar);

                    // Top products
                    const labels = (res.top_products_chart || []).map(r => r.produk);
                    const values = (res.top_products_chart || []).map(r => parseInt(r.total || 0, 10));
                    updateTopProductsChart(labels, values);

                    // Low stock
                    renderLowStock(res.low_stock_products || []);

                    // Donut
                    donutData = res.donut_charts || {
                        product: [],
                        brand: [],
                        category: []
                    };
                    renderDonutChart($('#donut-type-select').val() || 'product');

                    // Financial Section
                    if (res.financial_cards) {
                        updateFinancialCards(res.financial_cards);
                    }
                    if (res.profit_revenue_trend) {
                        updateProfitRevenueChart(res.profit_revenue_trend);
                    }
                    if (res.revenue_by_category) {
                        updateRevenueCategoryChart(res.revenue_by_category);
                    }
                    if (res.sales_velocity) {
                        updateSalesVelocityChart(res.sales_velocity);
                    }
                    if (res.top_margin_products) {
                        updateTopMarginChart(res.top_margin_products);
                    }
                    if (res.cost_profit_stack) {
                        updateCostProfitStackChart(res.cost_profit_stack);
                    }
                } catch (e) {
                    console.error(e);
                }
            },
            error: function() {
                $('#overview-loading, #top-products-loading').hide();
            }
        });

        // Fetch Workshop Data
        fetchWorkshopData();
    }

    $(document).ready(function() {
        // default last 30 days
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - 29);
        const f = d => d.toISOString().slice(0, 10);
        $('#filter_start_date').val(f(start));
        $('#filter_end_date').val(f(end));

        // event handlers
        $('#btn-apply-filter').on('click', function() {
            runAfterLibsReady(updateDashboardData);
        });
        $('#btn-reset-filter').on('click', function() {
            $('#dashboardFilters').find('select').val('');
            $('#filter_start_date').val(f(start));
            $('#filter_end_date').val(f(end));
            runAfterLibsReady(updateDashboardData);
        });
        // Auto-apply when dropdowns changed
        $('#filter_product, #filter_brand, #filter_category').on('change', function() {
            runAfterLibsReady(updateDashboardData);
        });
        $('#donut-type-select').on('change', function() {
            renderDonutChart(this.value);
        });

        // initial load after libs are ready
        runAfterLibsReady(updateDashboardData);
    });
</script>
<?= $this->endSection() ?>