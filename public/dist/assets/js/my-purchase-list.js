$(document).ready(function() {
    // Initialize Select2 for supplier filter
    $('#supplierFilter').select2({
        theme: 'bootstrap4',
        placeholder: 'Semua Supplier',
        allowClear: true
    });

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
            let adminActions = '';
            if (USER_ROLE === 'admin') {
                adminActions = '<a class="dropdown-item delete-btn" href="#" data-id="' + data + '"><i class="ri-delete-bin-line text-danger"></i> Hapus</a>';
            }

            if(adminActions === ''){
                return '';
            }

            return '<div class="dropdown">' +
                '<button class="btn btn-sm btn-icon btn-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ri-more-2-fill"></i></button>' +
                '<div class="dropdown-menu dropdown-menu-right">' +
                adminActions +
                '</div></div>';
        }
    });

    var purchaseTable = $('#purchaseListTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: BASE_URL + 'purchase/fetchList',
            type: 'POST',
            data: function(d) {
                // Add filter data to the request
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.supplierId = $('#supplierFilter').val();
                // Add CSRF token
                d[CSRF_TOKEN_NAME] = CSRF_HASH;
            },
            // Re-generate CSRF hash on each request
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: columns,
        columnDefs: columnDefs,
        order: [[2, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // --- Filter Logic ---
    $('#filterBtn').on('click', function() {
        purchaseTable.ajax.reload();
    });

    function getCsrfData() {
        let data = {};
        data[CSRF_TOKEN_NAME] = CSRF_HASH;
        return data;
    }

    // --- Delete Logic ---
    $('#purchaseListTable tbody').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var stockInId = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Transaksi ini akan dihapus secara permanen dan stok barang akan dikembalikan. Aksi ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let postData = getCsrfData();
                
                $.ajax({
                    url: BASE_URL + 'purchase/delete/' + stockInId,
                    type: 'POST', // Using POST for delete for CSRF protection
                    data: postData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Dihapus!', response.message, 'success');
                            purchaseTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                    }
                });
            }
        });
    });
});