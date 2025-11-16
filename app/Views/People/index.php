<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Manajemen User</h4>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                    <i class="ri-add-line"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="userTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Username</th>
                                <th width="25%">Nama Lengkap</th>
                                <th width="10%">Role</th>
                                <th width="15%">Dibuat</th>
                                <th width="15%">Diupdate</th>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambah">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_username" name="username" required>
                        <small class="text-danger" id="error_add_username"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_lengkap" name="nama_lengkap" required>
                        <small class="text-danger" id="error_add_nama_lengkap"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_role">Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                        <small class="text-danger" id="error_add_role"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="add_password" name="password" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                        <small class="text-danger d-block" id="error_add_password"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                        <small class="text-danger" id="error_edit_username"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                        <small class="text-danger" id="error_edit_nama_lengkap"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                        <small class="text-danger" id="error_edit_role"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.</small>
                        <small class="text-danger d-block" id="error_edit_password"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load users on page load
        loadUsers();

        // Form Tambah Submit
        $('#formTambah').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.text-danger').text('');

            $.ajax({
                url: '<?= base_url('people/store') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalTambah').modal('hide');
                        $('#formTambah')[0].reset();
                        showAlert('success', response.message);
                        loadUsers();
                    } else {
                        if (response.errors) {
                            // Display validation errors
                            $.each(response.errors, function(field, message) {
                                $('#error_add_' + field).text(message);
                            });
                        }
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menambahkan user');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');

            // Clear previous errors
            $('.text-danger').text('');

            $.ajax({
                url: '<?= base_url('people/edit') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var user = response.data;
                        $('#edit_id').val(user.id);
                        $('#edit_username').val(user.username);
                        $('#edit_nama_lengkap').val(user.nama_lengkap);
                        $('#edit_role').val(user.role);
                        $('#edit_password').val(''); // Clear password field
                        $('#modalEdit').modal('show');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mengambil data user');
                }
            });
        });

        // Form Edit Submit
        $('#formEdit').on('submit', function(e) {
            e.preventDefault();

            var id = $('#edit_id').val();

            // Clear previous errors
            $('.text-danger').text('');

            $.ajax({
                url: '<?= base_url('people/update') ?>/' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalEdit').modal('hide');
                        showAlert('success', response.message);
                        loadUsers();
                    } else {
                        if (response.errors) {
                            // Display validation errors
                            $.each(response.errors, function(field, message) {
                                $('#error_edit_' + field).text(message);
                            });
                        }
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mengupdate user');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var username = $(this).data('username');

            if (confirm('Apakah Anda yakin ingin menghapus user "' + username + '"?')) {
                $.ajax({
                    url: '<?= base_url('people/delete') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert('success', response.message);
                            loadUsers();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Terjadi kesalahan saat menghapus user');
                    }
                });
            }
        });

        // Load users function
        function loadUsers() {
            $.ajax({
                url: '<?= base_url('people/fetchAll') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var tbody = $('#userTable tbody');
                        tbody.empty();

                        if (response.data.length > 0) {
                            $.each(response.data, function(index, user) {
                                var roleClass = user.role === 'admin' ? 'badge-primary' : 'badge-secondary';
                                var createdAt = user.created_at ? new Date(user.created_at).toLocaleDateString('id-ID') : '-';
                                var updatedAt = user.updated_at ? new Date(user.updated_at).toLocaleDateString('id-ID') : '-';

                                var row = '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + user.username + '</td>' +
                                    '<td>' + user.nama_lengkap + '</td>' +
                                    '<td><span class="badge ' + roleClass + '">' + user.role.toUpperCase() + '</span></td>' +
                                    '<td>' + createdAt + '</td>' +
                                    '<td>' + updatedAt + '</td>' +
                                    '<td class="text-center">' +
                                    '<button class="btn btn-sm btn-warning btn-edit" data-id="' + user.id + '" title="Edit" data-toggle="tooltip"><i class="ri-edit-line"></i></button> ' +
                                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + user.id + '" data-username="' + user.username + '" title="Hapus" data-toggle="tooltip"><i class="ri-delete-bin-line"></i></button>' +
                                    '</td>' +
                                    '</tr>';
                                tbody.append(row);
                            });

                            // Initialize tooltips
                            $('[data-toggle="tooltip"]').tooltip();
                        } else {
                            tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data user</td></tr>');
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat memuat data user');
                }
            });
        }

        // Show alert function
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

        // Clear errors when modal is closed
        $('#modalTambah, #modalEdit').on('hidden.bs.modal', function() {
            $('.text-danger').text('');
            $('#formTambah')[0].reset();
        });
    });
</script>
<?= $this->endSection() ?>