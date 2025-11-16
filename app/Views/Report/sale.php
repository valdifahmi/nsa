<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Laporan Barang Keluar (Header)</h4>
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
                <!-- Filter Tanggal -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="startDate">Tanggal Awal</label>
                            <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-01') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="endDate">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" id="btnFilter">
                                <i class="ri-search-line"></i> Tampilkan Laporan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Laporan -->
                <div class="table-responsive">
                    <table id="reportTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Nomor Transaksi</th>
                                <th width="15%">Tanggal</th>
                                <th width="15%">User</th>
                                <th width="20%">Penerima</th>
                                <th width="30%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Klik "Tampilkan Laporan" untuk memuat data</td>
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
        $('#btnFilter').on('click', function() {
            loadReport();
        });

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
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            if (!startDate || !endDate) {
                alert('Tanggal awal dan akhir harus diisi');
                return;
            }

            if (startDate > endDate) {
                alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            $.ajax({
                url: '<?= base_url('report/fetchSaleReport') ?>',
                type: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
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
                            tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data untuk periode yang dipilih</td></tr>');
                        } else {
                            var html = '';
                            $.each(response.data, function(index, item) {
                                var tanggal = item.tanggal_keluar ? new Date(item.tanggal_keluar).toLocaleString('id-ID') : '-';
                                var penerima = item.penerima || '-';
                                var catatan = item.catatan || '-';
                                var userName = item.user_name || '-';

                                html += '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + item.nomor_transaksi + '</td>' +
                                    '<td>' + tanggal + '</td>' +
                                    '<td>' + userName + '</td>' +
                                    '<td>' + penerima + '</td>' +
                                    '<td>' + catatan + '</td>' +
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
                                    title: 'Laporan Barang Keluar - ' + startDate + ' s/d ' + endDate
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="ri-file-excel-line"></i> Excel',
                                    title: 'Laporan Barang Keluar - ' + startDate + ' s/d ' + endDate
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: '<i class="ri-file-pdf-line"></i> PDF',
                                    title: 'Laporan Barang Keluar',
                                    messageTop: 'Periode: ' + startDate + ' s/d ' + endDate,
                                    orientation: 'landscape',
                                    pageSize: 'A4'
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="ri-printer-line"></i> Print',
                                    title: 'Laporan Barang Keluar',
                                    messageTop: '<h4>Periode: ' + startDate + ' s/d ' + endDate + '</h4>'
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