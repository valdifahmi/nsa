<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Data Supplier</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSupplierModal">
                        <i class="ri-add-line"></i> Add Supplier
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="supplierTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Supplier</th>
                                <th width="20%">Kontak</th>
                                <th width="30%">Alamat</th>
                                <th width="15%" class="text-center">Actions</th>
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addSupplierForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_nama_supplier">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_supplier" name="nama_supplier" required>
                        <small class="text-danger" id="add_error_nama_supplier"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_kontak">Kontak</label>
                        <input type="text" class="form-control" id="add_kontak" name="kontak" placeholder="No. Telp / Email">
                        <small class="text-danger" id="add_error_kontak"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_alamat">Alamat</label>
                        <textarea class="form-control" id="add_alamat" name="alamat" rows="3"></textarea>
                        <small class="text-danger" id="add_error_alamat"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSupplierForm">
                <input type="hidden" id="edit_supplier_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_supplier">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_supplier" name="nama_supplier" required>
                        <small class="text-danger" id="edit_error_nama_supplier"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_kontak">Kontak</label>
                        <input type="text" class="form-control" id="edit_kontak" name="kontak">
                        <small class="text-danger" id="edit_error_kontak"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        <small class="text-danger" id="edit_error_alamat"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load suppliers on page load
        loadSuppliers();

        // Add supplier form submit
        $('#addSupplierForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            $.ajax({
                url: '<?= base_url('supplier/create') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addSupplierModal').modal('hide');
                        $('#addSupplierForm')[0].reset();
                        loadSuppliers();
                        showAlert('success', response.message);
                    } else {
                        if (response.errors) {
                            displayErrors('add', response.errors);
                        } else {
                            showAlert('error', response.message);
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menyimpan supplier');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            clearErrors('edit');

            $.ajax({
                url: '<?= base_url('supplier/getSupplier') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var supplier = response.data;
                        $('#edit_supplier_id').val(supplier.id);
                        $('#edit_nama_supplier').val(supplier.nama_supplier);
                        $('#edit_kontak').val(supplier.kontak);
                        $('#edit_alamat').val(supplier.alamat);
                        $('#editSupplierModal').modal('show');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat memuat data supplier');
                }
            });
        });

        // Edit supplier form submit
        $('#editSupplierForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('edit');

            var id = $('#edit_supplier_id').val();

            $.ajax({
                url: '<?= base_url('supplier/update') ?>/' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editSupplierModal').modal('hide');
                        $('#editSupplierForm')[0].reset();
                        loadSuppliers();
                        showAlert('success', response.message);
                    } else {
                        if (response.errors) {
                            displayErrors('edit', response.errors);
                        } else {
                            showAlert('error', response.message);
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mengupdate supplier');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            if (confirm('Apakah Anda yakin ingin menghapus supplier "' + name + '"?')) {
                $.ajax({
                    url: '<?= base_url('supplier/delete') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadSuppliers();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Terjadi kesalahan saat menghapus supplier');
                    }
                });
            }
        });

        // Reset forms when modals are closed
        $('#addSupplierModal').on('hidden.bs.modal', function() {
            $('#addSupplierForm')[0].reset();
            clearErrors('add');
        });

        $('#editSupplierModal').on('hidden.bs.modal', function() {
            $('#editSupplierForm')[0].reset();
            clearErrors('edit');
        });
    });

    function loadSuppliers() {
        $.ajax({
            url: '<?= base_url('supplier/fetchSuppliers') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Destroy existing DataTable
                    if ($.fn.DataTable.isDataTable('#supplierTable')) {
                        $('#supplierTable').DataTable().destroy();
                    }

                    var tbody = $('#supplierTable tbody');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data supplier</td></tr>');
                    } else {
                        var html = '';
                        $.each(response.data, function(index, supplier) {
                            html += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + supplier.nama_supplier + '</td>' +
                                '<td>' + (supplier.kontak || '-') + '</td>' +
                                '<td>' + (supplier.alamat || '-') + '</td>' +
                                '<td class="text-center">' +
                                '<button class="btn btn-sm btn-warning btn-edit" data-id="' + supplier.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="' + supplier.id + '" data-name="' + supplier.nama_supplier + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                                '</td>' +
                                '</tr>';
                        });
                        tbody.html(html);
                    }

                    // Initialize DataTables
                    $('#supplierTable').DataTable({
                        pageLength: 25,
                        responsive: true,
                        order: [
                            [0, 'asc']
                        ],
                        columnDefs: [{
                                targets: 0,
                                type: 'num'
                            },
                            {
                                targets: -1,
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                        }
                    });
                }
            },
            error: function() {
                showAlert('error', 'Terjadi kesalahan saat memuat data supplier');
            }
        });
    }

    function clearErrors(prefix) {
        $('#' + prefix + '_error_nama_supplier').text('');
        $('#' + prefix + '_error_kontak').text('');
        $('#' + prefix + '_error_alamat').text('');
    }

    function displayErrors(prefix, errors) {
        if (errors.nama_supplier) {
            $('#' + prefix + '_error_nama_supplier').text(errors.nama_supplier);
        }
        if (errors.kontak) {
            $('#' + prefix + '_error_kontak').text(errors.kontak);
        }
        if (errors.alamat) {
            $('#' + prefix + '_error_alamat').text(errors.alamat);
        }
    }

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';

        $('.card-body').prepend(alertHtml);

        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Global function untuk dipanggil dari halaman lain (Purchase)
    window.addSupplierQuick = function(callback) {
        $('#addSupplierModal').modal('show');

        // Override submit handler untuk quick add
        $('#addSupplierForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            $.ajax({
                url: '<?= base_url('supplier/create') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addSupplierModal').modal('hide');
                        $('#addSupplierForm')[0].reset();

                        // Call callback with new supplier data
                        if (typeof callback === 'function') {
                            callback(response.data);
                        }

                        showAlert('success', response.message);
                    } else {
                        if (response.errors) {
                            displayErrors('add', response.errors);
                        } else {
                            showAlert('error', response.message);
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menyimpan supplier');
                }
            });
        });
    };
</script>
<?= $this->endSection() ?>