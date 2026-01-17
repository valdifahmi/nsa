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

        // Calculate total for percentage calculation
        const total = data.reduce((acc, item) => acc + parseInt(item.value || 0, 10), 0);

        donutInstance = new Morris.Donut({
            element: 'donut-chart-div',
            data: data.map(d => ({
                label: d.label,
                value: parseInt(d.value || 0, 10)
            })),
            colors: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#06b6d4', '#84cc16', '#f43f5e'],
            resize: true,
            formatter: function(y) {
                // Show value and percentage on hover
                const percentage = total > 0 ? ((y / total) * 100).toFixed(1) : 0;
                return `${numberFormat(y)} (${percentage}%)`;
            }
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
        const avgVelocity = parseFloat(cards.avg_stock_velocity || 0).toFixed(1);
        $('#card-avg-velocity').text(avgVelocity + ' Hari');
    }

    function updateFinancialCards(financial) {
        // Helper to generate HTML for a card with a compact value and a full-value tooltip
        const createFinancialCardHTML = (value, iconColor) => {
            const compactValue = formatRupiahCompact(value);
            const fullValue = formatRupiahFull(value);
            // Return only the value if it's zero to avoid a pointless icon
            if (parseFloat(value) === 0) {
                return formatRupiah(value);
            }
            return `${compactValue} <i class="ri-information-line" style="font-size: 16px; color: ${iconColor}; cursor: help;" title="${fullValue}" data-toggle="tooltip"></i>`;
        };

        // 1. Revenue (Ex. PPN)
        $('#card-revenue-value').html(createFinancialCardHTML(financial.revenue_ex_ppn, '#10b981'));

        // 2. Inventory Value (Historical)
        $('#card-inventory-value').html(createFinancialCardHTML(financial.inventory_value_historical, '#f59e0b'));

        // 3. Potential Revenue
        $('#card-pot-revenue-value').html(createFinancialCardHTML(financial.potential_revenue, '#6366f1'));

        // 4. Potential Profit
        $('#card-pot-profit-value').html(createFinancialCardHTML(financial.potential_profit, '#ef4444'));

        // 5. Gross Profit
        $('#card-gross-profit').html(createFinancialCardHTML(financial.gross_profit, '#10b981'));

        // 6. Gross Margin % - Not a monetary value, so no icon
        const marginPercent = parseFloat(financial.margin_percent || 0);
        $('#card-gross-margin').text(marginPercent.toFixed(2) + '%');

        // 7. Total PPN (11%)
        $('#card-total-ppn').html(createFinancialCardHTML(financial.ppn, '#ef4444'));

        // 8. Total PPh 23 (2%)
        $('#card-total-pph23').html(createFinancialCardHTML(financial.pph_23, '#8b5cf6'));

        // Unpriced Items Alert
        const unpricedCount = parseInt(financial.unpriced_items_count || 0, 10);
        if (unpricedCount > 0) {
            $('#unpriced-count').text(unpricedCount);
            $('#unpriced-alert').show();
        } else {
            $('#unpriced-alert').hide();
        }

        // Initialize all new tooltips
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

    // Interactive Revenue Donut Chart
    let revenueDonutInstance = null;
    let revenueDonutData = {
        category: [],
        brand: [],
        type: []
    };

    function renderRevenueDonutChart(type) {
        const data = revenueDonutData[type] || [];

        if (revenueDonutInstance) {
            $('#revenue-donut-chart-div').empty();
            revenueDonutInstance = null;
        }

        if (!data.length) {
            $('#revenue-donut-chart-div').html('<div class="text-center text-muted">No data available</div>');
            return;
        }

        // Calculate total for percentage calculation
        const total = data.reduce((acc, item) => acc + parseFloat(item.value || 0), 0);

        // Special colors for Barang vs Jasa
        let chartColors = ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#06b6d4', '#84cc16', '#f43f5e', '#94a3b8', '#64748b'];
        if (type === 'type') {
            chartColors = ['#3b82f6', '#8b5cf6'];
        }

        // Add percentage to label string, as hoverCallback is not supported on Donut charts
        const chartData = data.map(d => {
            const value = parseFloat(d.value || 0);
            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
            return {
                label: `${d.label} (${percentage}%)`,
                value: value
            };
        });

        revenueDonutInstance = new Morris.Donut({
            element: 'revenue-donut-chart-div',
            data: chartData,
            colors: chartColors,
            resize: true,
            // Revert formatter to simple compact rupiah format as requested
            formatter: function(y) {
                return formatRupiahCompact(y);
            }
        });
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

    function updateTopRevenueChart(data) {
        if (window.morrisTopRevenue) {
            $('#top-revenue-chart-div').empty();
            window.morrisTopRevenue = null;
        }

        if (!data || !data.length) {
            $('#top-revenue-chart-div').html('<div class="text-center text-muted py-5">No product revenue data available</div>');
            return;
        }

        const chartData = data.map(d => ({
            product: d.product_name,
            revenue: parseFloat(d.total_revenue || 0)
        }));

        window.morrisTopRevenue = new Morris.Bar({
            element: 'top-revenue-chart-div',
            data: chartData,
            xkey: 'product',
            ykeys: ['revenue'],
            labels: ['Total Revenue'],
            barColors: ['#8b5cf6'],
            barRadius: [10, 10, 0, 0],
            hideHover: 'auto',
            resize: true,
            gridTextSize: 11,
            barSizeRatio: 0.6,
            yLabelFormat: function(y) {
                return formatRupiahCompact(y);
            },
            hoverCallback: function(index, options, content, row) {
                return `<div class="morris-hover-row-label">${row.product}</div>
                        <div class="morris-hover-point">Revenue: ${formatRupiah(row.revenue)}</div>`;
            }
        });
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

    function updateTopClientsChart(data) {
        if (window.morrisTopClients) {
            $('#top-clients-chart-div').empty();
            window.morrisTopClients = null;
        }

        if (!data || !data.length) {
            $('#top-clients-chart-div').html('<div class="text-center text-muted py-5">No client revenue data available</div>');
            return;
        }

        // Parse numbers and fix key to 'nama_klien'
        const chartData = data.map(d => ({
            nama_klien: d.nama_klien,
            revenue_barang: parseFloat(d.revenue_barang || 0),
            revenue_jasa: parseFloat(d.revenue_jasa || 0)
        }));

        window.morrisTopClients = new Morris.Bar({
            element: 'top-clients-chart-div',
            data: chartData,
            xkey: 'nama_klien',
            ykeys: ['revenue_barang', 'revenue_jasa'],
            labels: ['Parts', 'Services'],
            stacked: true,
            barColors: ['#10b981', '#3b82f6'], // Green for Parts, Blue for Services
            hideHover: 'auto',
            resize: true,
            gridTextSize: 10,
            xLabelAngle: 35,
            yLabelFormat: function(y) {
                return formatRupiahCompact(y);
            },
            hoverCallback: function(index, options, content, row) {
                const total = row.revenue_barang + row.revenue_jasa;
                return `<div class="morris-hover-row-label"><strong>${row.nama_klien}</strong></div>
                        <div class="morris-hover-point" style="color: #10b981;">Parts: ${formatRupiah(row.revenue_barang)}</div>
                        <div class="morris-hover-point" style="color: #3b82f6;">Services: ${formatRupiah(row.revenue_jasa)}</div>
                        <div class="morris-hover-point" style="border-top: 1px solid #ccc; margin-top: 5px; padding-top: 5px;"><strong>Total: ${formatRupiah(total)}</strong></div>`;
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

        // data is already in the format [{label: '...', value: ...}]
        const total = data.reduce((acc, item) => acc + parseInt(item.value || 0, 10), 0);

        if (total === 0) {
            $('#service-parts-ratio-chart-div').html('<div class="text-center text-muted">No data available</div>');
            return;
        }

        window.morrisServicePartsRatio = new Morris.Donut({
            element: 'service-parts-ratio-chart-div',
            data: data,
            colors: ['#f59e0b', '#3b82f6'], // Parts Only (Orange), With Services (Blue)
            resize: true,
            formatter: function(y) {
                const percentage = total > 0 ? ((y / total) * 100).toFixed(1) : 0;
                return `${numberFormat(y)} transaksi (${percentage}%)`;
            }
        });
    }

    function fetchWorkshopData() {
        const loadingSpinner = '<span class="pulsing-loader">Loading...</span>';
        $('#card-active-wo, #card-service-done, #card-service-nominal, #card-avg-service').html(loadingSpinner);

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
                        updateServicePartsRatioChart(res.workshop.transaction_ratio || []);
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
            const loadingSpinner = '<span class="pulsing-loader">Loading...</span>';
            $('#card-total-masuk, #card-total-keluar, #card-total-stok, #card-avg-velocity').html(loadingSpinner);
            $('#card-revenue-value, #card-inventory-value, #card-pot-revenue-value, #card-pot-profit-value, #card-gross-profit, #card-gross-margin, #card-total-ppn, #card-total-pph23').html(loadingSpinner);
        
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
                    if (res.sales_velocity) {
                        updateSalesVelocityChart(res.sales_velocity);
                    }
                    if (res.top_revenue_products) {
                        updateTopRevenueChart(res.top_revenue_products);
                    }
                    if (res.cost_profit_stack) {
                        updateCostProfitStackChart(res.cost_profit_stack);
                    }
                    if (res.top_clients_revenue) {
                        updateTopClientsChart(res.top_clients_revenue);
                    }

                    // Interactive Revenue Donut
                    if (res.revenue_donut_charts) {
                        revenueDonutData = res.revenue_donut_charts;
                        renderRevenueDonutChart($('#revenue-donut-type-select').val() || 'category');
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
        // Keep a reference to the initial server-rendered values for the reset button
        const initialStartDate = $('#filter_start_date').val();
        const initialEndDate = $('#filter_end_date').val();

        // event handlers
        $('#btn-apply-filter').on('click', function() {
            runAfterLibsReady(updateDashboardData);
        });
        $('#btn-reset-filter').on('click', function() {
            $('#dashboardFilters').find('select').val('');
            // Restore from our stored initial values
            $('#filter_start_date').val(initialStartDate);
            $('#filter_end_date').val(initialEndDate);
            runAfterLibsReady(updateDashboardData);
        });
        // Auto-apply when dropdowns changed
        $('#filter_product, #filter_brand, #filter_category').on('change', function() {
            runAfterLibsReady(updateDashboardData);
        });
        $('#donut-type-select').on('change', function() {
            renderDonutChart(this.value);
        });

        // Revenue Donut type selector
        $('#revenue-donut-type-select').on('change', function() {
            renderRevenueDonutChart(this.value);
        });

        // initial load after libs are ready
        runAfterLibsReady(updateDashboardData);
    });