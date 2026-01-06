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
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 card-total-sale">
                            <div class="icon iq-icon-box-2 bg-info-light">
                                <i class="ri-arrow-down-circle-line" style="font-size: 40px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <p class="mb-2">Stock-IN</p>
                                <h4 id="card-total-masuk">0</h4>
                            </div>
                        </div>
                        <div class="iq-progress-bar mt-2">
                            <span class="bg-info iq-progress progress-1" style="width: 0%"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
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
            <div class="col-lg-4 col-md-4">
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

    <!-- ============= FINANCIAL SECTION ============= -->
    <div class="col-lg-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Financial Overview</h4>
            </div>
        </div>
    </div>

    <!-- Financial Cards -->
    <div class="col-lg-3 col-md-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 card-total-sale">
                    <div class="icon iq-icon-box-2 bg-warning-light">
                        <i class="ri-archive-line" style="font-size: 40px; color: #f59e0b;"></i>
                    </div>
                    <div>
                        <p class="mb-2">Inventory Value</p>
                        <h4 id="card-inventory-value">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 card-total-sale">
                    <div class="icon iq-icon-box-2 bg-info-light">
                        <i class="ri-shopping-cart-line" style="font-size: 40px; color: #3b82f6;"></i>
                    </div>
                    <div>
                        <p class="mb-2">Purchase Value</p>
                        <h4 id="card-purchase-value">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 card-total-sale">
                    <div class="icon iq-icon-box-2 bg-success-light">
                        <i class="ri-money-dollar-circle-line" style="font-size: 40px; color: #10b981;"></i>
                    </div>
                    <div>
                        <p class="mb-2">Revenue</p>
                        <h4 id="card-revenue">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4 card-total-sale">
                    <div class="icon iq-icon-box-2 bg-primary-light">
                        <i class="ri-line-chart-line" style="font-size: 40px; color: #6366f1;"></i>
                    </div>
                    <div>
                        <p class="mb-2">Gross Profit</p>
                        <h4 id="card-profit">Rp 0</h4>
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

    <!-- Chart 3: Sales Velocity -->
    <div class="col-lg-6">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Sales Velocity (Days to Sell)</h4>
                </div>
            </div>
            <div class="card-body">
                <div id="sales-velocity-chart-div"></div>
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

    function updateCards(cards) {
        $('#card-total-masuk').text(numberFormat(cards.total_masuk));
        $('#card-total-keluar').text(numberFormat(cards.total_keluar));
        $('#card-total-stok').text(numberFormat(cards.total_stok));
    }

    function updateFinancialCards(financial) {
        $('#card-inventory-value').text(formatRupiah(financial.inventory_value));
        $('#card-purchase-value').text(formatRupiah(financial.total_purchase_value));
        $('#card-revenue').text(formatRupiah(financial.total_revenue));
        $('#card-profit').text(formatRupiah(financial.total_profit));
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
        const chartData = data.map(d => ({
            product: d.product_name,
            days: parseFloat(d.avg_days_to_sell || 0)
        }));

        if (!window.morrisSalesVelocity) {
            window.morrisSalesVelocity = new Morris.Bar({
                element: 'sales-velocity-chart-div',
                data: chartData,
                xkey: 'product',
                ykeys: ['days'],
                labels: ['Days'],
                barColors: ['#06b6d4'],
                barRadius: [10, 10, 0, 0],
                hideHover: 'auto',
                resize: true,
                gridTextSize: 11,
                barSizeRatio: 0.6
            });
        } else {
            window.morrisSalesVelocity.setData(chartData);
        }
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
                } catch (e) {
                    console.error(e);
                }
            },
            error: function() {
                $('#overview-loading, #top-products-loading').hide();
            }
        });
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