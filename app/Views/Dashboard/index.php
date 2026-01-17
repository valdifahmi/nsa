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
                        <input type="date" id="filter_start_date" class="form-control form-control-sm" value="<?= esc($defaultStartDate) ?>">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label for="filter_end_date" class="mr-2">End</label>
                        <input type="date" id="filter_end_date" class="form-control form-control-sm" value="<?= esc($defaultEndDate) ?>">
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
                    <a href="<?= base_url('purchase') ?>" class="btn btn-sm btn-outline-dark mr-2">
                        <i class="ri-add-circle-line"></i> Stock-In
                    </a>
                    <a href="<?= base_url('sale') ?>" class="btn btn-sm btn-outline-dark">
                        <i class="ri-add-circle-line"></i> Stock-Out
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2 bg-primary-light">
                                <i class="ri-clockwise-2-line text-white" style="font-size: 40px; "></i>
                            </div>
                            <div>
                                <p class="mb-2">Avg. Stock Velocity</p>
                                <h4 id="card-avg-velocity">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="bg-primary iq-progress progress-1" style="width: 0%"></span>
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
            <strong>Perhatian! </strong> Terdapat <span id="unpriced-count"> 0 </span> produk yang belum diberikan harga.
            <a href="<?= base_url('pricing') ?>" class="alert-link"> Klik di sini untuk mengatur harga</a>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

    <!-- Financial Cards - Row 1 -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(16, 185, 129, 0.1);">
                                <i class="ri-wallet-2-line" style="font-size: 40px; color: #10b981;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Revenue (Ex. PPN)</p>
                                <h4 id="card-revenue-value">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #10b981;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3">
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

            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(99, 102, 241, 0.1);">
                                <i class="ri-archive-line" style="font-size: 40px; color: #6366f1;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Potential Revenue</p>
                                <h4 id="card-pot-revenue-value">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #6366f1;"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2" style="background: rgba(239, 68, 68, 0.1);">
                                <i class="ri-archive-line" style="font-size: 40px; color: #ef4444;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Potential Profit</p>
                                <h4 id="card-pot-profit-value">Rp 0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="iq-progress progress-1" style="width: 0%; background: #ef4444;"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Financial Cards - Row 2 -->
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-3 col-md-3">
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
            <div class="col-lg-3 col-md-3">
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
            <div class="col-lg-3 col-md-3">
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
            <div class="col-lg-3 col-md-3">
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

    <!-- Chart 2: Interactive Revenue Breakdown -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Revenue Breakdown</h4>
                </div>
                <div class="card-header-toolbar">
                    <select id="revenue-donut-type-select" class="form-control form-control-sm">
                        <option value="category">By Category</option>
                        <option value="brand">By Brand</option>
                        <option value="type">Barang vs Jasa</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="revenue-donut-chart-div" style="height:300px;"></div>
            </div>
        </div>
    </div>



    <!-- Chart 4: Top 10 Revenue Products -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top 10 Revenue Products</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="top-revenue-chart-div"></div>
            </div>
        </div>
    </div>

    <!-- Chart 6: Top Clients by Revenue -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Top 10 Clients by Revenue</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="top-clients-chart-div" style="height: 300px;"></div>
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
<style>
    .pulsing-loader {
        animation: pulse-animation 1.5s infinite;
    }

    @keyframes pulse-animation {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }

        100% {
            opacity: 1;
        }
    }
</style>
<!-- Charts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script>
    const BASE_URL = '<?= base_url() ?>/';
</script>
<script src="<?= base_url('dist/assets/js/my-dashboard.js?v=' . filemtime(FCPATH . 'dist/assets/js/my-dashboard.js')) ?>"></script>
<script>

</script>
<?= $this->endSection() ?>