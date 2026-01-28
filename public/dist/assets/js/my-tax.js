$(document).ready(function() {
    // Inisialisasi Select2
    $('#clientId').select2({
        placeholder: "Pilih Klien",
        allowClear: true
    });

    // Fungsi untuk memformat angka sebagai mata uang
    function formatCurrency(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // Fungsi untuk mengatur style tab
    function styleTabs() {
        $('.nav-pills .nav-link').each(function() {
            if ($(this).hasClass('active')) {
                $(this).addClass('text-white');
            } else {
                $(this).removeClass('text-white');
            }
        });
    }

    // Inisialisasi DataTables
    let ppnTable = $('#ppnTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": false, // Data di-handle client-side setelah load
        "ajax": {
            "url": "/report/getppndata", // URL endpoint PPN
            "type": "POST",
            "data": function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.clientId = $('#clientId').val();
            },
            "dataSrc": "data" // Sesuaikan dengan struktur JSON response
        },
        "columns": [{
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1; // Nomor urut
                }
            },
            {
                "data": "tanggal_keluar"
            },
            {
                "data": "nomor_invoice"
            },
            {
                "data": "nama_klien"
            },
            {
                "data": "DPP",
                "render": function(data) {
                    return formatCurrency(data);
                }
            },
            {
                "data": "total_ppn",
                "render": function(data) {
                    return formatCurrency(data);
                }
            }
        ],
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // Menghitung total untuk DPP
            var totalDpp = api
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            // Menghitung total untuk PPN
            var totalPpn = api
                .column(5, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            // Update footer
            $(api.column(4).footer()).html(formatCurrency(totalDpp));
            $(api.column(5).footer()).html(formatCurrency(totalPpn));
        },
        "dom": 'Bfrtip',
        "buttons": [{
                extend: 'copyHtml5',
                title: 'Laporan PPN'
            },
            {
                extend: 'excelHtml5',
                title: 'Laporan PPN'
            },
            {
                extend: 'pdfHtml5',
                title: 'Laporan PPN'
            },
            {
                extend: 'print',
                title: 'Laporan PPN'
            }
        ]
    });

    let pphTable = $('#pphTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "/report/getpphdata", // URL endpoint PPh
            "type": "POST",
            "data": function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.clientId = $('#clientId').val();
            },
            "dataSrc": "data"
        },
        "columns": [{
                "data": null,
                "render": function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": "tanggal_keluar"
            },
            {
                "data": "nomor_invoice"
            },
            {
                "data": "nama_klien"
            },
            {
                "data": "Bruto_Jasa",
                "render": function(data) {
                    return formatCurrency(data);
                }
            },
            {
                "data": "total_pph",
                "render": function(data) {
                    return formatCurrency(data);
                }
            }
        ],
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // Menghitung total untuk Bruto Jasa
            var totalBruto = api
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            // Menghitung total untuk PPh
            var totalPph = api
                .column(5, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            // Update footer
            $(api.column(4).footer()).html(formatCurrency(totalBruto));
            $(api.column(5).footer()).html(formatCurrency(totalPph));
        },
        "dom": 'Bfrtip',
        "buttons": [{
                extend: 'copyHtml5',
                title: 'Laporan PPh 23'
            },
            {
                extend: 'excelHtml5',
                title: 'Laporan PPh 23'
            },
            {
                extend: 'pdfHtml5',
                title: 'Laporan PPh 23'
            },
            {
                extend: 'print',
                title: 'Laporan PPh 23'
            }
        ]
    });

    // Tombol filter
    $('#filter').on('click', function() {
        ppnTable.ajax.reload();
        pphTable.ajax.reload();
    });

    // Muat data saat tab diaktifkan
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        styleTabs(); // Atur style tab

        var activeTab = $(e.target).attr('href');
        if (activeTab === '#ppn-tab') {
            ppnTable.ajax.reload();
        } else if (activeTab === '#pph-tab') {
            pphTable.ajax.reload();
        }
    });

    // Inisialisasi data pada tab aktif saat halaman dimuat
    ppnTable.ajax.reload();

    // Atur style tab saat halaman pertama kali dimuat
    styleTabs();

    // Export dropdown handlers
    $('#exportCopy').on('click', function() {
        if ($('#ppn-tab').hasClass('active')) {
            ppnTable.button(0).trigger();
        } else {
            pphTable.button(0).trigger();
        }
    });

    $('#exportExcel').on('click', function() {
        if ($('#ppn-tab').hasClass('active')) {
            ppnTable.button(1).trigger();
        } else {
            pphTable.button(1).trigger();
        }
    });

    $('#exportPDF').on('click', function() {
        if ($('#ppn-tab').hasClass('active')) {
            ppnTable.button(2).trigger();
        } else {
            phTable.button(2).trigger();
        }
    });

    $('#exportPrint').on('click', function() {
        if ($('#ppn-tab').hasClass('active')) {
            ppnTable.button(3).trigger();
        } else {
            pphTable.button(3).trigger();
        }
    });
});
