<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Laporan Stok Barang</h4>
                </div>
                <div class="btn-group" role="group">
                    <button id="btnExport" type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-download-line"></i> Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnExport">
                        <a class="dropdown-item" href="#" id="exportCopy"><i class="ri-file-copy-line"></i> Copy</a>
                        <a class="dropdown-item" href="#" id="exportExcel"><i class="ri-file-excel-line"></i> Excel</a>
                        <a class="dropdown-item" href="#" id="exportPDF"><i class="ri-file-pdf-line"></i> PDF</a>
                        <a class="dropdown-item" href="#" id="exportPrint"><i class="ri-printer-line"></i> Print</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ri-information-line"></i> Baris dengan <strong class="text-danger">warna merah</strong> menandakan stok barang sudah mencapai atau di bawah minimum stok.
                </div>

                <!-- Tabel Laporan -->
                <div class="table-responsive">
                    <table id="reportTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Kode Barang</th>
                                <th width="25%">Nama Barang</th>
                                <th width="15%">Kategori</th>
                                <th width="13%">Brand</th>
                                <th width="10%">Satuan</th>
                                <th width="10%">Stok Saat Ini</th>
                                <th width="10%">Min Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load report on page load
        loadReport();

        // Export dropdown handlers
        $(document).on('click', '#exportCopy', function(e) {
            e.preventDefault();
            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().button(0).trigger();
            }
        });

        $(document).on('click', '#exportExcel', function(e) {
            e.preventDefault();
            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().button(1).trigger();
            }
        });

        $(document).on('click', '#exportPDF', function(e) {
            e.preventDefault();
            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().button(2).trigger();
            }
        });

        $(document).on('click', '#exportPrint', function(e) {
            e.preventDefault();
            if ($.fn.DataTable.isDataTable('#reportTable')) {
                $('#reportTable').DataTable().button(3).trigger();
            }
        });

        function loadReport() {
            $.ajax({
                url: '<?= base_url('report/fetchStockReport') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Destroy existing DataTable
                        if ($.fn.DataTable.isDataTable('#reportTable')) {
                            $('#reportTable').DataTable().destroy();
                        }

                        var tbody = $('#reportTable tbody');
                        tbody.empty();

                        if (response.data.length === 0) {
                            tbody.append('<tr><td colspan="8" class="text-center">Tidak ada data produk</td></tr>');
                        } else {
                            var html = '';
                            $.each(response.data, function(index, item) {
                                var kategori = item.nama_kategori || '-';
                                var brand = item.nama_brand || '-';

                                // Check if stock is low
                                var isLowStock = parseInt(item.stok_saat_ini) <= parseInt(item.min_stok);
                                var rowClass = isLowStock ? 'table-danger' : '';
                                var stockClass = isLowStock ? 'text-danger font-weight-bold' : '';

                                html += '<tr class="' + rowClass + '">' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + item.kode_barang + '</td>' +
                                    '<td>' + item.nama_barang + '</td>' +
                                    '<td>' + kategori + '</td>' +
                                    '<td>' + brand + '</td>' +
                                    '<td>' + item.satuan + '</td>' +
                                    '<td class="text-right ' + stockClass + '">' + item.stok_saat_ini + '</td>' +
                                    '<td class="text-right">' + item.min_stok + '</td>' +
                                    '</tr>';
                            });
                            tbody.html(html);
                        }

                        // Initialize DataTables
                        $('#reportTable').DataTable({
                            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                '<"row"<"col-sm-12"tr>>' +
                                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                            buttons: [{
                                    extend: 'copyHtml5',
                                    text: '<i class="ri-file-copy-line"></i> Copy',
                                    title: 'Laporan Stok Barang - ' + new Date().toLocaleDateString('id-ID')
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="ri-file-excel-line"></i> Excel',
                                    title: 'Laporan Stok Barang - ' + new Date().toLocaleDateString('id-ID')
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: '<i class="ri-file-pdf-line"></i> PDF',
                                    title: 'Laporan Stok Barang',
                                    messageTop: 'Tanggal: ' + new Date().toLocaleDateString('id-ID'),
                                    orientation: 'landscape',
                                    pageSize: 'A4'
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="ri-printer-line"></i> Print',
                                    title: 'Laporan Stok Barang',
                                    messageTop: '<h4>Tanggal: ' + new Date().toLocaleDateString('id-ID') + '</h4>'
                                }
                            ],
                            pageLength: 25,
                            responsive: true,
                            order: [
                                [0, 'asc']
                            ],
                            columnDefs: [{
                                targets: 0,
                                type: 'num'
                            }],
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                            },
                            drawCallback: function() {
                                $('[title]').tooltip();
                            }
                        });
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat memuat laporan');
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>