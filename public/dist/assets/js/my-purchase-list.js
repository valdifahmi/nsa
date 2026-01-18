$(document).ready(function() {
    // Initialize Select2 for supplier filter
    $('#supplierFilter').select2({
        theme: 'bootstrap4',
        placeholder: 'Semua Supplier',
        allowClear: true
    });

    // --- DataTable Initialization ---
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
                d.paymentStatus = $('#statusFilter').val();
                // Add CSRF token
                d[CSRF_TOKEN_NAME] = CSRF_HASH;
            },
            // Re-generate CSRF hash on each request
            dataSrc: function(json) {
                // This is not standard, but if CI4 refreshes the hash on each POST, we need a way to update it.
                // Assuming the controller response won't have a new hash. A global ajaxSuccess handler would be better.
                // For now, we will assume the initial hash is valid for the session.
                return json.data;
            }
        },
        columns: [
            { data: 'id', searchable: false, orderable: false, render: function (data, type, row, meta) {
                return meta.settings._iDisplayStart + meta.row + 1;
            }},
            { data: 'nomor_transaksi' },
            { data: 'tanggal_masuk' },
            { data: 'nama_supplier' },
            { data: 'grand_total', className: 'text-right' },
            { data: 'tanggal_jatuh_tempo' },
            { data: 'status_pembayaran', className: 'text-center' },
            { data: 'id', orderable: false, searchable: false, className: 'text-center' }
        ],
        columnDefs: [
            {
                targets: [2, 5], // tanggal_masuk & tanggal_jatuh_tempo
                render: function(data, type, row) {
                    if (!data) return '-';
                    return moment(data).format('DD MMM YYYY');
                }
            },
            {
                targets: 4, // grand_total
                render: function(data, type, row) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                targets: 6, // status_pembayaran
                render: function(data, type, row) {
                    if (data === 'Lunas') {
                        return '<span class="badge badge-success">Lunas</span>';
                    } else if (data === 'Belum Lunas') {
                        var isOverdue = row.tanggal_jatuh_tempo && moment().isAfter(row.tanggal_jatuh_tempo, 'day');
                        var badgeClass = isOverdue ? 'badge-danger' : 'badge-warning';
                        return '<span class="badge ' + badgeClass + '">Belum Lunas</span>';
                    }
                    return data;
                }
            },
            {
                targets: 7, // action
                render: function(data, type, row) {
                    let adminActions = '';
                    if (USER_ROLE === 'admin') {
                        adminActions = '<div class="dropdown-divider"></div>' +
                            // '<a class="dropdown-item" href="' + BASE_URL + 'purchase/edit/' + data + '"><i class="ri-pencil-line text-info"></i> Edit</a>' +
                            '<a class="dropdown-item delete-btn" href="#" data-id="' + data + '"><i class="ri-delete-bin-line text-danger"></i> Hapus</a>';
                    }

                    var statusAction = '';
                    if (row.status_pembayaran === 'Belum Lunas') {
                        statusAction = '<a class="dropdown-item change-status" href="#" data-id="' + data + '" data-status="Lunas"><i class="ri-check-line text-success"></i> Tandai Lunas</a>';
                    } else {
                        statusAction = '<a class="dropdown-item change-status" href="#" data-id="' + data + '" data-status="Belum Lunas"><i class="ri-arrow-go-back-line text-warning"></i> Batal Lunas</a>';
                    }

                    return '<div class="dropdown">' +
                        '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>' +
                        '<div class="dropdown-menu">' +
                        statusAction +
                        adminActions +
                        '</div></div>';
                }
            }
        ],
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

    // --- Status Change Logic ---
    $('#purchaseListTable tbody').on('click', '.change-status', function(e) {
        e.preventDefault();
        var stockInId = $(this).data('id');
        var newStatus = $(this).data('status');
        var confirmationText = newStatus === 'Lunas' ?
            'Apakah Anda yakin ingin menandai transaksi ini sebagai LUNAS?' :
            'Apakah Anda yakin ingin mengubah status transaksi ini menjadi BELUM LUNAS?';

        Swal.fire({
            title: 'Konfirmasi Perubahan Status',
            text: confirmationText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah status!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let postData = getCsrfData();
                postData.id = stockInId;
                postData.status = newStatus;

                $.ajax({
                    url: BASE_URL + 'purchase/updatePaymentStatus',
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success');
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