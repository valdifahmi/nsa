<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Laporan Log Barang</h4>
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
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="startDate">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="startDate" value="<?= date('Y-m-01') ?>">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="endDate">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="endDate" value="<?= date('Y-m-t') ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">&nbsp;</label>
                        <button id="filterBtn" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
                <hr>
                <!-- Tabel Laporan -->
                <div class="table-responsive">
                    <table id="reportTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th width="15%">Tanggal</th>
                                <th width="20%">Ref</th>
                                <th width="15%">Kode</th>
                                <th width="25%">Nama Barang</th>
                                <th width="10%">Action</th>
                                <th width="15%" class="text-right">Qty</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    const BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('dist/assets/js/my-product-log-report.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>