<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Daftar Transaksi Masuk (Stock In)</h4>
                </div>
                <div class="header-action">
                    <a href="<?= base_url('purchase') ?>" class="btn btn-primary">
                        <i class="ri-add-line"></i> Buat Transaksi Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="border p-3 mb-4 rounded">
                    <div class="form-row">
                        <div class="col-md-3">
                            <label for="startDate">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-t') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="supplierFilter">Supplier</label>
                            <select id="supplierFilter" class="form-control">
                                <option value="">Semua Supplier</option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option value="<?= $supplier['id'] ?>"><?= esc($supplier['nama_supplier']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="statusFilter">Status Pembayaran</label>
                            <select id="statusFilter" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Belum Lunas">Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button id="filterBtn" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table id="purchaseListTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">No. Transaksi</th>
                                <th width="10%">Tanggal</th>
                                <th>Supplier</th>
                                <th class="text-right">Grand Total</th>
                                <th width="10%">Jatuh Tempo</th>
                                <th width="12%" class="text-center">Status Pembayaran</th>
                                <th width="15%" class="text-center">Aksi</th>
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
<script src="<?= base_url('dist/assets/js/my-purchase-list.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>
