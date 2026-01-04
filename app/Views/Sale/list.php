<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Daftar Transaksi Stock Out</h4>
                </div>
                <div>
                    <a href="<?= base_url('sale') ?>" class="btn btn-primary">
                        <i class="ri-add-line"></i> Transaksi Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="saleTable" class="table table-striped table-bordered" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Nomor Transaksi</th>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Client</th>
                                <th>Tipe</th>
                                <th>Status WO</th>
                                <th>Grand Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
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
        // Initialize DataTable
        var table = $('#saleTable').DataTable({
            processing: true,
            serverSide: false,
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
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'nomor_transaksi'
                },
                {
                    data: 'nomor_invoice'
                },
                {
                    data: 'tanggal_keluar',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID');
                    }
                },
                {
                    data: 'nama_client'
                },
                {
                    data: 'tipe_transaksi',
                    render: function(data) {
                        var badgeClass = data === 'Workshop' ? 'badge-info' : 'badge-secondary';
                        return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                    }
                },
                {
                    data: 'status_work_order',
                    render: function(data) {
                        var badgeClass = data === 'Proses' ? 'badge-warning' : 'badge-success';
                        return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                    }
                },
                {
                    data: 'grand_total',
                    render: function(data) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                    },
                    className: 'text-right'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var buttons = '';

                        // Update WO button (only for Workshop + Proses) - ICON ONLY
                        if (row.tipe_transaksi === 'Workshop' && row.status_work_order === 'Proses') {
                            buttons += '<a href="<?= base_url('sale/updateWO/') ?>' + row.id + '" class="btn btn-sm btn-warning mr-1" title="Update Work Order">' +
                                '<i class="ri-edit-line"></i>' +
                                '</a>';
                        }

                        // View Invoice button
                        buttons += '<button class="btn btn-sm btn-info mr-1" onclick="viewInvoice(' + row.id + ')" title="Lihat Invoice">' +
                            '<i class="ri-eye-line"></i>' +
                            '</button>';

                        // Print PDF button
                        buttons += '<a href="<?= base_url('invoice/generatePDF/') ?>' + row.id + '" target="_blank" class="btn btn-sm btn-success" title="Cetak PDF">' +
                            '<i class="ri-printer-line"></i>' +
                            '</a>';

                        return buttons;
                    },
                    orderable: false,
                    className: 'text-center'
                }
            ],
            order: [
                [1, 'desc']
            ],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        // View invoice function
        window.viewInvoice = function(id) {
            window.location.href = '<?= base_url('invoice') ?>';
        };
    });
</script>
<?= $this->endSection() ?>