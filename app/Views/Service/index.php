<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Master Jasa Service</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#serviceModal" onclick="openAddModal()">
                        <i class="ri-add-line"></i> Tambah Jasa
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="serviceTable" class="table table-striped table-bordered" style="width:100%">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Nama Jasa</th>
                                <th>Deskripsi</th>
                                <th>Harga Standar</th>
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

<!-- Modal Add/Edit Service -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Tambah Jasa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="serviceForm" enctype="multipart/form-data">
                <input type="hidden" id="serviceId" name="serviceId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_jasa">Nama Jasa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_jasa" name="nama_jasa" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="harga_standar">Harga Standar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_standar" name="harga_standar" min="0" step="1" required>
                        <small class="text-muted">Harga dalam Rupiah</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let table;
    let isEdit = false;

    $(document).ready(function() {
        // Initialize DataTable
        table = $('#serviceTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= base_url('service/fetchAll') ?>',
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
                    data: 'nama_jasa'
                },
                {
                    data: 'deskripsi',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'harga_standar',
                    render: function(data) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                    },
                    className: 'text-right'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<button class="btn btn-sm btn-warning" onclick="editService(' + row.id + ')" title="Edit">' +
                            '<i class="ri-edit-line"></i>' +
                            '</button> ' +
                            '<button class="btn btn-sm btn-danger" onclick="deleteService(' + row.id + ')" title="Hapus">' +
                            '<i class="ri-delete-bin-line"></i>' +
                            '</button>';
                    },
                    orderable: false,
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'asc']
            ],
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        // Form submit
        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            const formData = new FormData();
            formData.append('nama_jasa', $('#nama_jasa').val());
            formData.append('deskripsi', $('#deskripsi').val());
            formData.append('harga_standar', $('#harga_standar').val());

            const url = isEdit ?
                '<?= base_url('service/update/') ?>' + $('#serviceId').val() :
                '<?= base_url('service/store') ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#serviceModal').modal('hide');
                        table.ajax.reload();
                        alert(response.message);
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field).next('.invalid-feedback').text(message);
                            });
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan data');
                }
            });
        });
    });

    // Open Add Modal
    function openAddModal() {
        isEdit = false;
        $('#serviceModalLabel').text('Tambah Jasa');
        $('#serviceForm')[0].reset();
        $('#serviceId').val('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    // Edit Service
    function editService(id) {
        isEdit = true;
        $('#serviceModalLabel').text('Edit Jasa');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajax({
            url: '<?= base_url('service/edit/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const service = response.data;
                    $('#serviceId').val(service.id);
                    $('#nama_jasa').val(service.nama_jasa);
                    $('#deskripsi').val(service.deskripsi);
                    $('#harga_standar').val(service.harga_standar);
                    $('#serviceModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Gagal mengambil data jasa');
            }
        });
    }

    // Delete Service
    function deleteService(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus jasa ini?')) {
            return;
        }

        $.ajax({
            url: '<?= base_url('service/delete/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    table.ajax.reload();
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Gagal menghapus jasa');
            }
        });
    }
</script>
<?= $this->endSection() ?>