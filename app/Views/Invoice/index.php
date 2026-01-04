<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Invoice List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Daftar Invoice</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="invoiceTable" class="table table-striped table-bordered" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Nomor Invoice</th>
                                <th>Nama Klien</th>
                                <th>Tanggal</th>
                                <th>Total Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="ri-file-list-line"></i> Detail Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Company Header -->
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <img src="<?= base_url('dist/assets/images/product/01.png') ?>" alt="Logo" style="max-height: 80px; margin-bottom: 10px;">
                        <h4 class="font-weight-bold mb-0">PT. Nusantara Suplai Abadi</h4>
                        <p class="text-muted mb-0">Supplier Spare Part Alat Berat</p>
                        <hr>
                    </div>
                </div>

                <!-- Invoice Header -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Informasi Invoice</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150">Nomor Invoice</td>
                                <td>: <span id="detail_nomor_transaksi"></span></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>: <span id="detail_tanggal"></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Kepada</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150">Nama Klien</td>
                                <td>: <span id="detail_nama_client"></span></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: <span id="detail_alamat_client"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Items Table (Spare Parts) -->
                <h6 class="font-weight-bold">Spare Parts</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="100">Jumlah</th>
                                <th width="150">Harga Satuan</th>
                                <th width="150">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detail_items">
                            <!-- Items will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Services Table (if Workshop) -->
                <div id="servicesSection" style="display: none;">
                    <h6 class="font-weight-bold mt-4">Jasa Service</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Jasa</th>
                                    <th width="100">Jumlah</th>
                                    <th width="150">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="detail_services">
                                <!-- Services will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <table class="table table-sm" id="totalsTable">
                            <!-- Totals will be loaded dynamically -->
                        </table>
                    </div>
                </div>

                <!-- Notes & Bank Info -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Informasi Pembayaran</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="120"><strong>Bank</strong></td>
                                <td>: Bank Mandiri</td>
                            </tr>
                            <tr>
                                <td><strong>No. Rekening</strong></td>
                                <td>: 1234567890</td>
                            </tr>
                            <tr>
                                <td><strong>Atas Nama</strong></td>
                                <td>: PT. Nusantara Suplai Abadi</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Catatan</h6>
                        <p id="detail_catatan" class="text-muted">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="ri-close-line"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btnPrintFromModal">
                    <i class="ri-printer-line"></i> Cetak PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        var currentInvoiceId = null;

        // Initialize DataTable
        var table = $('#invoiceTable').DataTable({
            processing: true,
            ajax: {
                url: '<?= base_url('invoice/fetchInvoices') ?>',
                type: 'GET',
                dataSrc: function(json) {
                    if (json.status === 'success') {
                        return json.data;
                    }
                    return [];
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nomor_invoice',
                    render: function(data, type, row) {
                        return data || row.nomor_transaksi || '-';
                    }
                },
                {
                    data: 'nama_client',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'tanggal_keluar',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    }
                },
                {
                    data: 'grand_total',
                    render: function(data) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        });
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<div class="btn-group" role="group">' +
                            '<button class="btn btn-sm btn-info btn-view-detail" data-id="' + row.id + '" title="Lihat Detail">' +
                            '<i class="ri-eye-line"></i>' +
                            '</button>' +
                            '<a href="<?= base_url('invoice/generatePDF') ?>/' + row.id + '" target="_blank" class="btn btn-sm btn-primary" title="Cetak PDF">' +
                            '<i class="ri-printer-line"></i>' +
                            '</a>' +
                            '</div>';
                    }
                }
            ],
            order: [
                [1, 'desc']
            ],
            pageLength: 25,
            language: {
                "sProcessing": "Sedang memproses...",
                "sLengthMenu": "Tampilkan _MENU_ entri",
                "sZeroRecords": "Tidak ditemukan data yang sesuai",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "sInfoPostFix": "",
                "sSearch": "Cari:",
                "sUrl": "",
                "oPaginate": {
                    "sFirst": "Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext": "Selanjutnya",
                    "sLast": "Terakhir"
                }
            },
            rowCallback: function(row, data, index) {
                var pageInfo = table.page.info();
                var rowNumber = pageInfo.start + index + 1;
                $('td:eq(0)', row).html(rowNumber);
            },
            error: function(xhr, error, code) {
                console.log('DataTables Error:', error);
                console.log('Response:', xhr.responseText);
                showAlert('error', 'Error loading data: ' + error);
            }
        });

        // View Detail Button
        $('#invoiceTable').on('click', '.btn-view-detail', function() {
            var id = $(this).data('id');
            currentInvoiceId = id;
            loadInvoiceDetail(id);
        });

        // Load Invoice Detail
        function loadInvoiceDetail(id) {
            $.ajax({
                url: '<?= base_url('invoice/getDetail') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#detailModal').modal('show');
                    $('#detail_items').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        var data = response.data;
                        var invoice = data.invoice;
                        var items = data.items;

                        // Populate header
                        $('#detail_nomor_transaksi').text(invoice.nomor_invoice || invoice.nomor_transaksi);
                        $('#detail_tanggal').text(new Date(invoice.tanggal_keluar).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }));
                        $('#detail_nama_client').text(invoice.nama_client || '-');
                        $('#detail_alamat_client').text(invoice.alamat_client || '-');
                        $('#detail_catatan').text(invoice.catatan || '-');

                        // Populate items
                        var itemsHtml = '';
                        items.forEach(function(item, index) {
                            var total = item.jumlah * item.harga_jual_satuan;
                            itemsHtml += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + item.kode_barang + '</td>' +
                                '<td>' + item.nama_barang + '</td>' +
                                '<td class="text-center">' + item.jumlah + '</td>' +
                                '<td class="text-right">Rp ' + parseFloat(item.harga_jual_satuan).toLocaleString('id-ID') + '</td>' +
                                '<td class="text-right">Rp ' + total.toLocaleString('id-ID') + '</td>' +
                                '</tr>';
                        });
                        $('#detail_items').html(itemsHtml);

                        // Populate services (if Workshop)
                        var services = data.services || [];
                        if (services.length > 0) {
                            $('#servicesSection').show();
                            var servicesHtml = '';
                            services.forEach(function(service, index) {
                                var total = service.jumlah * service.biaya_jasa;
                                servicesHtml += '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + service.nama_jasa + '</td>' +
                                    '<td class="text-center">' + service.jumlah + '</td>' +
                                    '<td class="text-right">Rp ' + total.toLocaleString('id-ID') + '</td>' +
                                    '</tr>';
                            });
                            $('#detail_services').html(servicesHtml);
                        } else {
                            $('#servicesSection').hide();
                        }

                        // Populate totals (dynamic based on Workshop or Beli Putus)
                        var totalsHtml = '';
                        if (invoice.tipe_transaksi === 'Workshop' && services.length > 0) {
                            // Workshop: Show detailed breakdown
                            totalsHtml += '<tr><td colspan="2" class="font-weight-bold">Spare Parts:</td></tr>';
                            totalsHtml += '<tr><td style="padding-left: 20px;">Subtotal Barang:</td><td class="text-right">Rp ' + parseFloat(invoice.total_barang).toLocaleString('id-ID') + '</td></tr>';
                            totalsHtml += '<tr><td style="padding-left: 20px;">PPN (' + invoice.ppn_persen + '%):</td><td class="text-right">Rp ' + parseFloat(invoice.total_ppn).toLocaleString('id-ID') + '</td></tr>';
                            totalsHtml += '<tr class="border-top"><td style="padding-left: 20px;"><strong>Total Barang:</strong></td><td class="text-right"><strong>Rp ' + (parseFloat(invoice.total_barang) + parseFloat(invoice.total_ppn)).toLocaleString('id-ID') + '</strong></td></tr>';

                            totalsHtml += '<tr><td colspan="2" class="font-weight-bold pt-3">Jasa Service:</td></tr>';
                            totalsHtml += '<tr><td style="padding-left: 20px;">Subtotal Jasa:</td><td class="text-right">Rp ' + parseFloat(invoice.total_jasa).toLocaleString('id-ID') + '</td></tr>';
                            totalsHtml += '<tr><td style="padding-left: 20px;">PPh 23 (' + invoice.pph_persen + '%):</td><td class="text-right text-danger">(Rp ' + parseFloat(invoice.total_pph).toLocaleString('id-ID') + ')</td></tr>';
                            totalsHtml += '<tr class="border-top"><td style="padding-left: 20px;"><strong>Total Jasa:</strong></td><td class="text-right"><strong>Rp ' + (parseFloat(invoice.total_jasa) - parseFloat(invoice.total_pph)).toLocaleString('id-ID') + '</strong></td></tr>';
                        } else {
                            // Beli Putus: Simple breakdown
                            totalsHtml += '<tr><td><strong>Subtotal:</strong></td><td class="text-right">Rp ' + parseFloat(invoice.total_barang).toLocaleString('id-ID') + '</td></tr>';
                            totalsHtml += '<tr><td><strong>PPN (' + invoice.ppn_persen + '%):</strong></td><td class="text-right">Rp ' + parseFloat(invoice.total_ppn).toLocaleString('id-ID') + '</td></tr>';
                        }
                        totalsHtml += '<tr class="bg-light border-top"><td><strong>GRAND TOTAL:</strong></td><td class="text-right"><strong>Rp ' + parseFloat(invoice.grand_total).toLocaleString('id-ID') + '</strong></td></tr>';

                        $('#totalsTable').html(totalsHtml);
                    } else {
                        showAlert('error', response.message);
                        $('#detailModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Terjadi kesalahan saat memuat detail invoice');
                    $('#detailModal').modal('hide');
                }
            });
        }

        // Print from Modal
        $('#btnPrintFromModal').on('click', function() {
            if (currentInvoiceId) {
                window.open('<?= base_url('invoice/generatePDF') ?>/' + currentInvoiceId, '_blank');
            }
        });

        // Show Alert
        function showAlert(type, message) {
            var alertClass = type === 'success' ? 'alert-success' :
                type === 'warning' ? 'alert-warning' :
                type === 'info' ? 'alert-info' : 'alert-danger';
            var icon = type === 'success' ? 'ri-checkbox-circle-line' :
                type === 'warning' ? 'ri-error-warning-line' :
                type === 'info' ? 'ri-information-line' : 'ri-close-circle-line';

            var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                '<i class="' + icon + '"></i> ' + message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';

            $('#alertContainer').html(alertHtml);

            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }
    });
</script>
<?= $this->endSection() ?>