$(document).ready(function() {
    // Initialize Select2 for supplier filter
    if ($('#supplierFilter').length) {
        $('#supplierFilter').select2({
            theme: 'bootstrap4',
            placeholder: 'Semua Supplier',
            allowClear: true
        });
    }

    // --- DataTable Initialization ---
    let columns = [
        { data: 'id', searchable: false, orderable: false, render: function (data, type, row, meta) {
            return meta.settings._iDisplayStart + meta.row + 1;
        }},
        { data: 'nomor_transaksi' },
        { data: 'tanggal_masuk' },
        { data: 'nama_supplier' },
        { data: 'jenis', className: 'text-center' },
        { data: 'jumlah_qty', className: 'text-center' },
    ];

    if (USER_ROLE === 'admin') {
        columns.push({ data: 'total', className: 'text-right' });
    }

    columns.push({ data: 'id', orderable: false, searchable: false, className: 'text-center' });

    let columnDefs = [
        {
            targets: 2, // tanggal_masuk
            render: function(data, type, row) {
                if (!data) return '-';
                return moment(data).format('DD MMM YYYY');
            }
        },
    ];

    if (USER_ROLE === 'admin') {
        columnDefs.push({
            targets: 6, // total
            render: function(data, type, row) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
            }
        });
    }
    
    let actionColumnIndex = (USER_ROLE === 'admin') ? 7 : 6;
    columnDefs.push({
        targets: actionColumnIndex, // Aksi
        render: function(data, type, row) {
            return '<button class="btn btn-sm btn-info detail-btn" data-id="' + data + '"><i class="ri-eye-line"></i> Detail</button>';
        }
    });

    var reportTable = $('#purchaseReportTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: BASE_URL + 'report/fetchPurchaseReport',
            type: 'POST',
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.supplierId = $('#supplierFilter').val();
                d[CSRF_TOKEN_NAME] = CSRF_HASH;
            },
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: columns,
        columnDefs: columnDefs,
        order: [[2, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        buttons: [{
                extend: 'copyHtml5',
                text: '<i class="ri-file-copy-line"></i> Copy',
                title: 'Laporan Pembelian - ' + new Date().toLocaleDateString('id-ID'),
                exportOptions: {
                    columns: ':visible:not(:last-child)' // Exclude the last column (Aksi)
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="ri-file-excel-line"></i> Excel',
                title: 'Laporan Pembelian - ' + new Date().toLocaleDateString('id-ID'),
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="ri-file-pdf-line"></i> PDF',
                title: 'Laporan Pembelian',
                messageTop: 'Tanggal: ' + new Date().toLocaleDateString('id-ID'),
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line"></i> Print',
                title: 'Laporan Pembelian',
                messageTop: '<h4>Tanggal: ' + new Date().toLocaleDateString('id-ID') + '</h4>',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            }
        ]
    });

    // --- Filter Logic ---
    $('#filterBtn').on('click', function() {
        reportTable.ajax.reload();
    });

    // --- Export Logic ---
    $('#exportCopy').on('click', function(e) { e.preventDefault(); reportTable.button(0).trigger(); });
    $('#exportExcel').on('click', function(e) { e.preventDefault(); reportTable.button(1).trigger(); });
    $('#exportPDF').on('click', function(e) { e.preventDefault(); reportTable.button(2).trigger(); });
    $('#exportPrint').on('click', function(e) { e.preventDefault(); reportTable.button(3).trigger(); });

    // --- Detail Modal Logic ---
    $('#purchaseReportTable tbody').on('click', '.detail-btn', function(e) {
        e.preventDefault();
        var stockInId = $(this).data('id');
        
        // Show loading in modal body
        $('#detailModal .modal-body').html('<p class="text-center">Memuat data...</p>');
        $('#detailModal').modal('show');

        $.ajax({
            url: BASE_URL + 'report/purchase/detail/' + stockInId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Build detail HTML
                    let html = buildDetailHtml(response.data);
                    $('#detailModal .modal-body').html(html);
                    $('#detailModalLabel').text('Detail Transaksi: ' + response.data.stock_in.nomor_transaksi);
                } else {
                    $('#detailModal .modal-body').html('<p class="text-center text-danger">Gagal memuat data: ' + response.message + '</p>');
                }
            },
            error: function() {
                $('#detailModal .modal-body').html('<p class="text-center text-danger">Tidak dapat terhubung ke server.</p>');
            }
        });
    });

    function buildDetailHtml(data) {
        const stockIn = data.stock_in;
        const items = data.items;
        const userRole = USER_ROLE;

        let itemsHtml = items.map(item => {
            let totalHarga = userRole === 'admin' ? `<td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga_total)}</td>` : '';
            let hargaBeli = userRole === 'admin' ? `<td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga_beli)}</td>` : '';
            
            return `<tr>
                <td>${item.nama_produk}</td>
                <td>${item.qty}</td>
                ${hargaBeli}
                ${totalHarga}
            </tr>`;
        }).join('');

        let totalSection = userRole === 'admin' ? `<div class="row mt-3">
            <div class="col-md-6 offset-md-6 text-right">
                <p><strong>Subtotal:</strong> Rp ${new Intl.NumberFormat('id-ID').format(stockIn.subtotal)}</p>
                <p><strong>Diskon:</strong> Rp ${new Intl.NumberFormat('id-ID').format(stockIn.diskon)}</p>
                <h3><strong>Total Akhir:</strong> Rp ${new Intl.NumberFormat('id-ID').format(stockIn.total_akhir)}</h3>
            </div>
        </div>` : '';
        
        let tableHead = userRole === 'admin' ? `<th>Harga Beli</th><th>Total Harga</th>` : '';

        return `
            <div class="mb-4">
                <p><strong>No. Transaksi:</strong> ${stockIn.nomor_transaksi}</p>
                <p><strong>Tanggal:</strong> ${moment(stockIn.tanggal_masuk).format('DD MMMM YYYY')}</p>
                <p><strong>Supplier:</strong> ${stockIn.nama_supplier}</p>
                <p><strong>Jenis:</strong> ${stockIn.jenis}</p>
                <p><strong>Catatan:</strong> ${stockIn.catatan || '-'}</p>
            </div>
            <h5>Item Transaksi:</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        ${tableHead}
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
            </table>
            ${totalSection}
        `;
    }
});
