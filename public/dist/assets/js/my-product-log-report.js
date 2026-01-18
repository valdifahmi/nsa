$(document).ready(function() {
    function getReportTitle() {
        var startDate = $('#startDate').val() ? moment($('#startDate').val()).format('DD MMM YYYY') : '';
        var endDate = $('#endDate').val() ? moment($('#endDate').val()).format('DD MMM YYYY') : '';
        if (!startDate || !endDate) {
            return 'Laporan Log Barang';
        }
        return `Laporan Log Barang (${startDate} - ${endDate})`;
    }

    // Initialize DataTable
    var reportTable = $('#reportTable').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"row"<"col-sm-12"tr>>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [{
                extend: 'copyHtml5',
                text: '<i class="ri-file-copy-line"></i> Copy',
                title: getReportTitle
            },
            {
                extend: 'excelHtml5',
                text: '<i class="ri-file-excel-line"></i> Excel',
                title: getReportTitle
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="ri-file-pdf-line"></i> PDF',
                title: getReportTitle,
                orientation: 'portrait',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line"></i> Print',
                title: getReportTitle
            }
        ],
        responsive: true,
        processing: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: "Pilih rentang tanggal untuk melihat laporan.",
            processing: '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Memuat data...</p></div>'
        },
        ajax: function(data, callback, settings) {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            if (startDate && endDate) {
                $.ajax({
                    url: BASE_URL + 'report/getLogBarang',
                    type: 'POST',
                    data: {
                        startDate: startDate,
                        endDate: endDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            callback({
                                data: response.data
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Memuat Laporan',
                                text: response.message || 'Terjadi kesalahan pada server.'
                            });
                            callback({
                                data: []
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memuat laporan.',
                        });
                        callback({
                            data: []
                        });
                    }
                });
            } else {
                callback({
                    data: []
                });
            }
        },
        columns: [
            { "data": "tanggal" },
            { "data": "referensi" },
            { "data": "kode_barang" },
            { "data": "nama_barang" },
            { "data": "action" },
            { "data": "qty", "className": "text-right" }
        ],
        columnDefs: [{
                targets: 0, // Tanggal
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return moment(data).format('DD/MM/YYYY');
                    }
                    return data;
                }
            },
            {
                targets: 4, // Action
                render: function(data, type, row) {
                    if (data === 'Stock-IN') {
                        return '<span class="badge badge-success">Stock-IN</span>';
                    } else {
                        return '<span class="badge badge-warning">Stock-OUT</span>';
                    }
                }
            }
        ],
        order: [] // Use server-side ordering
    });

    // Export dropdown handlers
    $('#exportCopy').on('click', (e) => { e.preventDefault(); reportTable.button(0).trigger(); });
    $('#exportExcel').on('click', (e) => { e.preventDefault(); reportTable.button(1).trigger(); });
    $('#exportPDF').on('click', (e) => { e.preventDefault(); reportTable.button(2).trigger(); });
    $('#exportPrint').on('click', (e) => { e.preventDefault(); reportTable.button(3).trigger(); });

    // Filter button click handler
    $('#filterBtn').on('click', function() {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Silakan tentukan rentang tanggal terlebih dahulu.',
            });
            return;
        }
        reportTable.ajax.reload();
    });

    // Initial load
    reportTable.ajax.reload();
});