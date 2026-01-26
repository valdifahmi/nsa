<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Laporan Transaksi Masuk (Stock In)</h4>
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
                <!-- Filters -->
                <div class="border p-3 mb-4 rounded">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="startDate">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-t') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="supplierFilter">Supplier</label>
                            <select id="supplierFilter" class="form-control" style="border: 1px solid #ced4da;">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="filterBtn" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table id="purchaseReportTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center">Jml Qty</th>
                                <?php if (session()->get('user')['role'] === 'admin') : ?>
                                <th class="text-right">Total</th>
                                <?php endif; ?>
                                <th class="text-center">Aksi</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Detail content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Moment.js is required for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    const BASE_URL = '<?= base_url() ?>';
    const USER_ROLE = '<?= session()->get('user')['role'] ?? 'staff' ?>';
    const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';
</script>
<!-- The JS file for this page -->
<script src="<?= base_url('dist/assets/js/my-purchase-report.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
