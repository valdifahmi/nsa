<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Data Client</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addClientModal">
                        <i class="ri-add-line"></i> Add Client
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="clientTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Client</th>
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

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addClientForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_nama_klien">Nama Client <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_klien" name="nama_klien" required>
                        <small class="text-danger" id="add_error_nama_klien"></small>
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
                    <button type="submit" class="btn btn-primary">Save Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editClientForm">
                <input type="hidden" id="edit_client_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_klien">Nama Client <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_klien" name="nama_klien" required>
                        <small class="text-danger" id="edit_error_nama_klien"></small>
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
                    <button type="submit" class="btn btn-primary">Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load clients on page load
        loadClients();

        // Add client form submit
        $('#addClientForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            $.ajax({
                url: '<?= base_url('client/create') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addClientModal').modal('hide');
                        $('#addClientForm')[0].reset();
                        loadClients();
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
                    showAlert('error', 'Terjadi kesalahan saat menyimpan client');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            clearErrors('edit');

            $.ajax({
                url: '<?= base_url('client/getClient') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var client = response.data;
                        $('#edit_client_id').val(client.id);
                        $('#edit_nama_klien').val(client.nama_klien);
                        $('#edit_kontak').val(client.kontak);
                        $('#edit_alamat').val(client.alamat);
                        $('#editClientModal').modal('show');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat memuat data client');
                }
            });
        });

        // Edit client form submit
        $('#editClientForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('edit');

            var id = $('#edit_client_id').val();

            $.ajax({
                url: '<?= base_url('client/update') ?>/' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editClientModal').modal('hide');
                        $('#editClientForm')[0].reset();
                        loadClients();
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
                    showAlert('error', 'Terjadi kesalahan saat mengupdate client');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            if (confirm('Apakah Anda yakin ingin menghapus client "' + name + '"?')) {
                $.ajax({
                    url: '<?= base_url('client/delete') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadClients();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Terjadi kesalahan saat menghapus client');
                    }
                });
            }
        });

        // Reset forms when modals are closed
        $('#addClientModal').on('hidden.bs.modal', function() {
            $('#addClientForm')[0].reset();
            clearErrors('add');
        });

        $('#editClientModal').on('hidden.bs.modal', function() {
            $('#editClientForm')[0].reset();
            clearErrors('edit');
        });
    });

    function loadClients() {
        $.ajax({
            url: '<?= base_url('client/fetchClients') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Destroy existing DataTable
                    if ($.fn.DataTable.isDataTable('#clientTable')) {
                        $('#clientTable').DataTable().destroy();
                    }

                    var tbody = $('#clientTable tbody');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data client</td></tr>');
                    } else {
                        var html = '';
                        $.each(response.data, function(index, client) {
                            html += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + client.nama_klien + '</td>' +
                                '<td>' + (client.kontak || '-') + '</td>' +
                                '<td>' + (client.alamat || '-') + '</td>' +
                                '<td class="text-center">' +
                                '<button class="btn btn-sm btn-warning btn-edit" data-id="' + client.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="' + client.id + '" data-name="' + client.nama_klien + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                                '</td>' +
                                '</tr>';
                        });
                        tbody.html(html);
                    }

                    // Initialize DataTables
                    $('#clientTable').DataTable({
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
                showAlert('error', 'Terjadi kesalahan saat memuat data client');
            }
        });
    }

    function clearErrors(prefix) {
        $('#' + prefix + '_error_nama_klien').text('');
        $('#' + prefix + '_error_kontak').text('');
        $('#' + prefix + '_error_alamat').text('');
    }

    function displayErrors(prefix, errors) {
        if (errors.nama_klien) {
            $('#' + prefix + '_error_nama_klien').text(errors.nama_klien);
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

    // Global function untuk dipanggil dari halaman lain (Sale)
    window.addClientQuick = function(callback) {
        $('#addClientModal').modal('show');

        // Override submit handler untuk quick add
        $('#addClientForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            $.ajax({
                url: '<?= base_url('client/create') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addClientModal').modal('hide');
                        $('#addClientForm')[0].reset();

                        // Call callback with new client data
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
                    showAlert('error', 'Terjadi kesalahan saat menyimpan client');
                }
            });
        });
    };
</script>
<?= $this->endSection() ?>