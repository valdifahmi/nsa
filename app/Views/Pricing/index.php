<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <!-- Alert Container -->
        <div id="alertContainer"></div>

        <!-- Pricing Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Manajemen Harga Produk</h4>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bulkMarkUpModal">
                    <i class="ri-percent-line"></i> Bulk Mark-Up
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pricingTable" class="table table-striped table-bordered" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Margin (%)</th>
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

<!-- Edit Price Modal -->
<div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editPriceModalLabel">
                    <i class="ri-price-tag-3-line"></i> Edit Harga Produk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPriceForm">
                    <input type="hidden" id="edit_product_id">

                    <!-- Product Info -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Produk:</strong> <span id="product_name"></span><br>
                                <strong>Kode:</strong> <span id="product_code"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Old Prices -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Harga Beli Lama:</label>
                            <div class="form-control bg-light" id="old_harga_beli">Rp 0</div>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold">Harga Jual Lama:</label>
                            <div class="form-control bg-light" id="old_harga_jual">Rp 0</div>
                        </div>
                    </div>

                    <hr>

                    <!-- New Prices -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_harga_beli">Harga Beli Baru <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="new_harga_beli" placeholder="Masukkan harga beli baru" required min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="margin_percentage">Margin (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="margin_percentage" placeholder="Contoh: 20" required min="0" step="0.01">
                                <small class="text-muted">Harga Jual = Harga Beli + (Margin %)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="new_harga_jual">Harga Jual Baru <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bg-light" id="new_harga_jual" placeholder="Auto-calculated" readonly required min="0" step="0.01">
                                <small class="text-muted">Otomatis dihitung berdasarkan Harga Beli + Margin</small>
                            </div>
                        </div>
                    </div>

                    <!-- Calculation Display -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success" id="calculation_display" style="display: none;">
                                <strong>Perhitungan:</strong><br>
                                <span id="calculation_formula"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="ri-close-line"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSavePrice">
                    <i class="ri-save-line"></i> Simpan Harga
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Mark-Up Modal -->
<div class="modal fade" id="bulkMarkUpModal" tabindex="-1" role="dialog" aria-labelledby="bulkMarkUpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bulkMarkUpModalLabel">
                    <i class="ri-percent-line"></i> Bulk Mark-Up Harga Jual
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulkMarkUpForm">
                    <div class="form-group">
                        <label for="filter_type">Filter Berdasarkan <span class="text-danger">*</span></label>
                        <select class="form-control" id="filter_type" required>
                            <option value="">Pilih Filter</option>
                            <option value="all">Semua Produk</option>
                            <option value="category">Kategori</option>
                            <option value="brand">Merk</option>
                        </select>
                    </div>

                    <div class="form-group" id="category_filter" style="display: none;">
                        <label for="category_id">Pilih Kategori</label>
                        <select class="form-control" id="category_id">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= $category['nama_kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="brand_filter" style="display: none;">
                        <label for="brand_id">Pilih Merk</label>
                        <select class="form-control" id="brand_id">
                            <option value="">Pilih Merk</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= $brand['nama_brand'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="markup_method">Metode Mark-Up <span class="text-danger">*</span></label>
                        <select class="form-control" id="markup_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="flat">Flat (Rata) - Set margin sama untuk semua produk</option>
                            <option value="addition">Addition (Tambah) - Tambahkan ke margin existing</option>
                        </select>
                        <small class="text-muted">Pilih cara perhitungan mark-up</small>
                    </div>

                    <div class="form-group">
                        <label for="markup_percentage">Nilai Mark-Up (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="markup_percentage" placeholder="Contoh: 15" required min="0" step="0.01">
                        <small class="text-muted" id="markup_description">Pilih metode terlebih dahulu</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="ri-alert-line"></i> <strong>Perhatian:</strong> Operasi ini akan mengupdate harga jual semua produk sesuai filter yang dipilih!
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="ri-close-line"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="btnApplyMarkUp">
                    <i class="ri-check-line"></i> Terapkan Mark-Up
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#pricingTable').DataTable({
            processing: true,
            ajax: {
                url: '<?= base_url('pricing/fetchAll') ?>',
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
                    data: 'kode_barang'
                },
                {
                    data: 'nama_barang'
                },
                {
                    data: 'nama_kategori'
                },
                {
                    data: 'nama_brand'
                },
                {
                    data: 'harga_beli_saat_ini',
                    render: function(data) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: 'harga_jual_saat_ini',
                    render: function(data) {
                        return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var hargaBeli = parseFloat(row.harga_beli_saat_ini);
                        var hargaJual = parseFloat(row.harga_jual_saat_ini);
                        var margin = hargaBeli > 0 ? ((hargaJual - hargaBeli) / hargaBeli * 100) : 0;
                        var badgeClass = margin > 0 ? 'badge-success' : 'badge-secondary';
                        return '<span class="badge ' + badgeClass + '">' + margin.toFixed(2) + '%</span>';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-primary btn-edit-price" data-id="' + row.id + '" title="Edit Harga">' +
                            '<i class="ri-edit-line"></i> Edit Harga' +
                            '</button>';
                    }
                }
            ],
            order: [
                [1, 'asc']
            ],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            rowCallback: function(row, data, index) {
                var pageInfo = table.page.info();
                var rowNumber = pageInfo.start + index + 1;
                $('td:eq(0)', row).html(rowNumber);
            }
        });

        // Edit Price Button Click
        $('#pricingTable').on('click', '.btn-edit-price', function() {
            var id = $(this).data('id');
            var row = table.row($(this).parents('tr')).data();

            // Populate modal
            $('#edit_product_id').val(row.id);
            $('#product_name').text(row.nama_barang);
            $('#product_code').text(row.kode_barang);
            $('#old_harga_beli').text('Rp ' + parseFloat(row.harga_beli_saat_ini).toLocaleString('id-ID'));
            $('#old_harga_jual').text('Rp ' + parseFloat(row.harga_jual_saat_ini).toLocaleString('id-ID'));

            // Set current values
            $('#new_harga_beli').val(row.harga_beli_saat_ini);

            // Calculate current margin
            var currentMargin = row.harga_beli_saat_ini > 0 ?
                ((row.harga_jual_saat_ini - row.harga_beli_saat_ini) / row.harga_beli_saat_ini * 100) : 0;
            $('#margin_percentage').val(currentMargin.toFixed(2));
            $('#new_harga_jual').val(row.harga_jual_saat_ini);

            // Show calculation
            updateCalculation();

            $('#editPriceModal').modal('show');
        });

        // Calculate selling price when buy price or margin changes
        $('#new_harga_beli, #margin_percentage').on('input', function() {
            updateCalculation();
        });

        function updateCalculation() {
            var hargaBeli = parseFloat($('#new_harga_beli').val()) || 0;
            var margin = parseFloat($('#margin_percentage').val()) || 0;
            var hargaJual = hargaBeli * (1 + (margin / 100));

            $('#new_harga_jual').val(hargaJual.toFixed(2));

            if (hargaBeli > 0 && margin > 0) {
                $('#calculation_display').show();
                $('#calculation_formula').html(
                    'Rp ' + hargaBeli.toLocaleString('id-ID') + ' + (' + margin + '%) = ' +
                    '<strong>Rp ' + hargaJual.toLocaleString('id-ID') + '</strong>'
                );
            } else {
                $('#calculation_display').hide();
            }
        }

        // Save Price
        $('#btnSavePrice').on('click', function() {
            var productId = $('#edit_product_id').val();
            var hargaBeliBaru = $('#new_harga_beli').val();
            var hargaJualBaru = $('#new_harga_jual').val();

            if (!hargaBeliBaru || !hargaJualBaru) {
                showAlert('warning', 'Semua field harus diisi');
                return;
            }

            if (parseFloat(hargaBeliBaru) <= 0 || parseFloat(hargaJualBaru) <= 0) {
                showAlert('warning', 'Harga harus lebih dari 0');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin mengupdate harga produk ini?')) {
                return;
            }

            $(this).prop('disabled', true).html('<i class="ri-loader-4-line"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url('pricing/updatePrice') ?>',
                type: 'POST',
                data: JSON.stringify({
                    product_id: parseInt(productId),
                    harga_beli_baru: parseFloat(hargaBeliBaru),
                    harga_jual_baru: parseFloat(hargaJualBaru)
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                        $('#editPriceModal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat menyimpan harga';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMsg);
                },
                complete: function() {
                    $('#btnSavePrice').prop('disabled', false).html('<i class="ri-save-line"></i> Simpan Harga');
                }
            });
        });

        // Filter Type Change (Bulk Mark-Up)
        $('#filter_type').on('change', function() {
            var filterType = $(this).val();

            $('#category_filter, #brand_filter').hide();
            $('#category_id, #brand_id').prop('required', false);

            if (filterType === 'category') {
                $('#category_filter').show();
                $('#category_id').prop('required', true);
            } else if (filterType === 'brand') {
                $('#brand_filter').show();
                $('#brand_id').prop('required', true);
            }
        });

        // Markup Method Change
        $('#markup_method').on('change', function() {
            var method = $(this).val();
            if (method === 'flat') {
                $('#markup_description').text('Semua produk akan memiliki margin yang sama persis dengan nilai ini');
            } else if (method === 'addition') {
                $('#markup_description').text('Nilai ini akan ditambahkan ke margin existing setiap produk');
            } else {
                $('#markup_description').text('Pilih metode terlebih dahulu');
            }
        });

        // Apply Bulk Mark-Up
        $('#btnApplyMarkUp').on('click', function() {
            var filterType = $('#filter_type').val();
            var filterId = null;
            var markupPercentage = $('#markup_percentage').val();
            var markupMethod = $('#markup_method').val();

            if (!filterType) {
                showAlert('warning', 'Pilih filter terlebih dahulu');
                return;
            }

            if (filterType === 'category') {
                filterId = $('#category_id').val();
                if (!filterId) {
                    showAlert('warning', 'Pilih kategori terlebih dahulu');
                    return;
                }
            } else if (filterType === 'brand') {
                filterId = $('#brand_id').val();
                if (!filterId) {
                    showAlert('warning', 'Pilih merk terlebih dahulu');
                    return;
                }
            }

            if (!markupMethod) {
                showAlert('warning', 'Pilih metode mark-up terlebih dahulu');
                return;
            }

            if (!markupPercentage || parseFloat(markupPercentage) < 0) {
                showAlert('warning', 'Masukkan nilai mark-up yang valid');
                return;
            }

            var methodText = markupMethod === 'flat' ? 'Flat (Rata)' : 'Addition (Tambah)';
            if (!confirm('Apakah Anda yakin ingin menerapkan mark-up ' + methodText + ' ' + markupPercentage + '% ke produk yang dipilih?')) {
                return;
            }

            $(this).prop('disabled', true).html('<i class="ri-loader-4-line"></i> Memproses...');

            $.ajax({
                url: '<?= base_url('pricing/bulkMarkUp') ?>',
                type: 'POST',
                data: JSON.stringify({
                    filter_type: filterType,
                    filter_id: filterId ? parseInt(filterId) : null,
                    markup_percentage: parseFloat(markupPercentage),
                    markup_method: markupMethod
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('success', response.message);
                        $('#bulkMarkUpModal').modal('hide');
                        $('#bulkMarkUpForm')[0].reset();
                        $('#category_filter, #brand_filter').hide();
                        table.ajax.reload(null, false);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat menerapkan mark-up';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMsg);
                },
                complete: function() {
                    $('#btnApplyMarkUp').prop('disabled', false).html('<i class="ri-check-line"></i> Terapkan Mark-Up');
                }
            });
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