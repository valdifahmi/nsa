<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Laporan Pajak</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" id="startDate" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="endDate">End Date</label>
                                <input type="date" id="endDate" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clientId">Client</label>
                                <select id="clientId" class="form-control select2 select2-custom" style="width: 100%;">
                                    <option value="">Semua Client</option>
                                    <?php foreach ($clients as $client) : ?>
                                        <option value="<?= $client['id']; ?>"><?= $client['nama_klien']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button id="filter" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <h3 class="card-title p-3">Data Pajak</h3>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item"><a class="nav-link active" href="#ppn-tab" data-toggle="tab">Laporan PPN</a></li>
                        <li class="nav-item"><a class="nav-link" href="#pph-tab" data-toggle="tab">Laporan PPh 23</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="ppn-tab">
                            <table id="ppnTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>No. Invoice</th>
                                        <th>Nama Klien</th>
                                        <th>DPP Barang</th>
                                        <th>PPN 11%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data PPN akan dimuat di sini oleh AJAX -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align:right">Total Keseluruhan:</th>
                                        <th id="total-dpp"></th>
                                        <th id="total-ppn"></th>
                                    </tr>
                                <tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="pph-tab">
                            <table id="pphTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>No. Invoice</th>
                                        <th>Nama Klien</th>
                                        <th>Bruto Jasa</th>
                                        <th>PPh 23 2%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data PPh akan dimuat di sini oleh AJAX -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align:right">Total Keseluruhan:</th>
                                        <th id="total-bruto"></th>
                                        <th id="total-pph"></th>
                                    </tr>
                                <tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2').select2({
            placeholder: "Pilih Klien",
            allowClear: true
        });
    });
</script>
<?= $this->endSection(); ?>
