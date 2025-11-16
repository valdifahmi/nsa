<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Brand List</h4>
                </div>
                <div>
                    <div class="btn-group mr-2" role="group">
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
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBrandModal">
                        <i class="ri-add-line"></i> Add Brand
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="brandTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="60%">Nama Brand</th>
                                <th width="20%">Created At</th>
                                <th width="10%">Action</th>
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

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBrandForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_brand">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_brand" name="nama_brand" placeholder="Masukkan nama brand" required>
                        <small class="text-danger" id="error_nama_brand"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBrandForm">
                <input type="hidden" id="edit_brand_id" name="brand_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_brand">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_brand" name="nama_brand" placeholder="Masukkan nama brand" required>
                        <small class="text-danger" id="error_edit_nama_brand"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
        // Load brands on page load
        loadBrands();

        // Add Brand Form Submit
        $('#addBrandForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            $('#error_nama_brand').text('');

            $.ajax({
                url: '<?= base_url('brand/store') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addBrandModal').modal('hide');
                        $('#addBrandForm')[0].reset();
                        loadBrands();
                        showAlert('success', response.message);
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            if (response.errors.nama_brand) {
                                $('#error_nama_brand').text(response.errors.nama_brand);
                            }
                        } else {
                            showAlert('error', response.message);
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menambahkan brand');
                }
            });
        });

        // Edit Brand Button Click
        $(document).on('click', '.btn-edit', function() {
            var brandId = $(this).data('id');

            $.ajax({
                url: '<?= base_url('brand/edit') ?>/' + brandId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#edit_brand_id').val(response.data.id);
                        $('#edit_nama_brand').val(response.data.nama_brand);
                        $('#error_edit_nama_brand').text('');
                        $('#editBrandModal').modal('show');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mengambil data brand');
                }
            });
        });

        // Edit Brand Form Submit
        $('#editBrandForm').on('submit', function(e) {
            e.preventDefault();

            var brandId = $('#edit_brand_id').val();
            $('#error_edit_nama_brand').text('');

            $.ajax({
                url: '<?= base_url('brand/update') ?>/' + brandId,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editBrandModal').modal('hide');
                        loadBrands();
                        showAlert('success', response.message);
                    } else if (response.status === 'error') {
                        if (response.errors) {
                            if (response.errors.nama_brand) {
                                $('#error_edit_nama_brand').text(response.errors.nama_brand);
                            }
                        } else {
                            showAlert('error', response.message);
                        }
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mengupdate brand');
                }
            });
        });

        // Delete Brand Button Click
        $(document).on('click', '.btn-delete', function() {
            if (confirm('Apakah Anda yakin ingin menghapus brand ini?')) {
                var brandId = $(this).data('id');

                $.ajax({
                    url: '<?= base_url('brand/delete') ?>/' + brandId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadBrands();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'Terjadi kesalahan saat menghapus brand');
                    }
                });
            }
        });

        // Export dropdown handlers
        $(document).on('click', '#exportCopy', function(e) {
            e.preventDefault();
            var table = $('#brandTable').DataTable();
            table.button(0).trigger();
        });

        $(document).on('click', '#exportExcel', function(e) {
            e.preventDefault();
            var table = $('#brandTable').DataTable();
            table.button(1).trigger();
        });

        $(document).on('click', '#exportPDF', function(e) {
            e.preventDefault();
            var table = $('#brandTable').DataTable();
            table.button(2).trigger();
        });

        $(document).on('click', '#exportPrint', function(e) {
            e.preventDefault();
            var table = $('#brandTable').DataTable();
            table.button(3).trigger();
        });

        // Load Brands Function
        function loadBrands() {
            $.ajax({
                url: '<?= base_url('brand/fetchAll') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Step 1: Destroy existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable('#brandTable')) {
                            $('#brandTable').DataTable().destroy();
                        }

                        // Step 2: Clear and rebuild tbody
                        var tbody = $('#brandTable tbody');
                        tbody.empty();

                        if (response.data.length === 0) {
                            tbody.append('<tr><td colspan="4" class="text-center">Tidak ada data brand</td></tr>');
                        } else {
                            // Step 3: Build HTML string
                            var html = '';
                            $.each(response.data, function(index, brand) {
                                var createdAt = brand.created_at ? new Date(brand.created_at).toLocaleString('id-ID') : '-';

                                html += '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + brand.nama_brand + '</td>' +
                                    '<td>' + createdAt + '</td>' +
                                    '<td>' +
                                    '<button class="btn btn-sm btn-warning btn-edit" data-id="' + brand.id + '" title="Edit">' +
                                    '<i class="ri-edit-line"></i>' +
                                    '</button> ' +
                                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + brand.id + '" title="Delete">' +
                                    '<i class="ri-delete-bin-line"></i>' +
                                    '</button>' +
                                    '</td>' +
                                    '</tr>';
                            });

                            // Step 4: Insert HTML into tbody
                            tbody.html(html);
                        }

                        // Step 5: Initialize DataTables with Buttons
                        var table = $('#brandTable').DataTable({
                            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                                '<"row"<"col-sm-12"tr>>' +
                                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                            buttons: [{
                                    extend: 'copyHtml5',
                                    text: '<i class="ri-file-copy-line"></i> Copy',
                                    exportOptions: {
                                        columns: ':visible:not(:last-child)'
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="ri-file-excel-line"></i> Excel',
                                    title: 'Brand List - ' + new Date().toLocaleDateString('id-ID'),
                                    exportOptions: {
                                        columns: ':visible:not(:last-child)'
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    text: '<i class="ri-file-pdf-line"></i> PDF',
                                    title: 'Brand List',
                                    orientation: 'portrait',
                                    pageSize: 'A4',
                                    exportOptions: {
                                        columns: ':visible:not(:last-child)'
                                    }
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="ri-printer-line"></i> Print',
                                    title: 'Brand List',
                                    exportOptions: {
                                        columns: ':visible:not(:last-child)'
                                    }
                                }
                            ],
                            pageLength: 25,
                            responsive: true,
                            destroy: true,
                            order: [
                                [0, 'asc']
                            ],
                            columnDefs: [{
                                    targets: 0,
                                    orderable: true,
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
                            },
                            drawCallback: function() {
                                $('[title]').tooltip();
                            }
                        });
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat memuat data brand');
                }
            });
        }

        // Show Alert Function (same as Category)
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
    });
</script>
<?= $this->endSection() ?>