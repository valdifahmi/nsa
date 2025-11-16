<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Category List</h4>
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
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="ri-add-line"></i> Add Category
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categoryTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Category Name</th>
                                <th width="50%">Description</th>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCategoryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_nama_kategori">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_kategori" name="nama_kategori" required>
                        <small class="text-danger" id="add_error_nama_kategori"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_deskripsi">Description</label>
                        <textarea class="form-control" id="add_deskripsi" name="deskripsi" rows="3"></textarea>
                        <small class="text-danger" id="add_error_deskripsi"></small>
                    </div>
                    <div class="form-group">
                        <label for="add_image">Image</label>
                        <input type="file" class="form-control-file" id="add_image" name="image" accept="image/*">
                        <small class="text-muted">Max size: 2MB. Allowed: JPG, JPEG, PNG</small>
                        <small class="text-danger d-block" id="add_error_image"></small>
                    </div>
                    <div class="form-group">
                        <img id="add_image_preview" src="" alt="Preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCategoryForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_category_id" name="id">
                <input type="hidden" id="edit_old_image" name="old_image">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_kategori">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                        <small class="text-danger" id="edit_error_nama_kategori"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit_deskripsi">Description</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                        <small class="text-danger" id="edit_error_deskripsi"></small>
                    </div>
                    <div class="form-group">
                        <label>Current Image</label>
                        <div>
                            <img id="edit_current_image" src="" alt="Current" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_image">Change Image (optional)</label>
                        <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                        <small class="text-muted">Max size: 2MB. Allowed: JPG, JPEG, PNG</small>
                        <small class="text-danger d-block" id="edit_error_image"></small>
                    </div>
                    <div class="form-group">
                        <img id="edit_image_preview" src="" alt="Preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load categories on page load
        loadCategories();

        // Add category form submit
        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('category/store') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
                        $('#add_image_preview').hide();
                        loadCategories();
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
                    showAlert('error', 'An error occurred while saving the category');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            clearErrors('edit');

            $.ajax({
                url: '<?= base_url('category/edit') ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var category = response.data;
                        $('#edit_category_id').val(category.id);
                        $('#edit_nama_kategori').val(category.nama_kategori);
                        $('#edit_deskripsi').val(category.deskripsi);
                        $('#edit_old_image').val(category.image);

                        var imagePath = category.image !== 'default.png' ?
                            '<?= base_url('uploads/categories/') ?>' + category.image :
                            '<?= base_url('dist/assets/images/default.png') ?>';
                        $('#edit_current_image').attr('src', imagePath);
                        $('#edit_image_preview').hide();

                        $('#editCategoryModal').modal('show');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'An error occurred while loading category data');
                }
            });
        });

        // Edit category form submit
        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('edit');

            var id = $('#edit_category_id').val();
            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('category/update') ?>/' + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editCategoryModal').modal('hide');
                        $('#editCategoryForm')[0].reset();
                        loadCategories();
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
                    showAlert('error', 'An error occurred while updating the category');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            if (confirm('Are you sure you want to delete category "' + name + '"?')) {
                $.ajax({
                    url: '<?= base_url('category/delete') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadCategories();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'An error occurred while deleting the category');
                    }
                });
            }
        });

        // Image preview for add form
        $('#add_image').on('change', function() {
            previewImage(this, '#add_image_preview');
        });

        // Image preview for edit form
        $('#edit_image').on('change', function() {
            previewImage(this, '#edit_image_preview');
        });

        // Reset add form when modal is closed
        $('#addCategoryModal').on('hidden.bs.modal', function() {
            $('#addCategoryForm')[0].reset();
            $('#add_image_preview').hide();
            clearErrors('add');
        });

        // Reset edit form when modal is closed
        $('#editCategoryModal').on('hidden.bs.modal', function() {
            $('#editCategoryForm')[0].reset();
            $('#edit_image_preview').hide();
            clearErrors('edit');
        });

        // Export dropdown handlers
        $(document).on('click', '#exportCopy', function(e) {
            e.preventDefault();
            var table = $('#categoryTable').DataTable();
            table.button(0).trigger();
        });

        $(document).on('click', '#exportExcel', function(e) {
            e.preventDefault();
            var table = $('#categoryTable').DataTable();
            table.button(1).trigger();
        });

        $(document).on('click', '#exportPDF', function(e) {
            e.preventDefault();
            var table = $('#categoryTable').DataTable();
            table.button(2).trigger();
        });

        $(document).on('click', '#exportPrint', function(e) {
            e.preventDefault();
            var table = $('#categoryTable').DataTable();
            table.button(3).trigger();
        });
    });

    function loadCategories() {
        $.ajax({
            url: '<?= base_url('category/fetchAll') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Step 1: Destroy existing DataTable instance if it exists
                    if ($.fn.DataTable.isDataTable('#categoryTable')) {
                        $('#categoryTable').DataTable().destroy();
                    }

                    // Step 2: Clear and rebuild tbody
                    var tbody = $('#categoryTable tbody');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="4" class="text-center">No categories found</td></tr>');
                    } else {
                        // Step 3: Build HTML string
                        var html = '';
                        $.each(response.data, function(index, category) {
                            var imagePath = category.image !== 'default.png' ?
                                '<?= base_url('uploads/categories/') ?>' + category.image :
                                '<?= base_url('dist/assets/images/default.png') ?>';

                            html += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' +
                                '<div class="d-flex align-items-center">' +
                                '<img src="' + imagePath + '" alt="' + category.nama_kategori + '" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">' +
                                '<span>' + category.nama_kategori + '</span>' +
                                '</div>' +
                                '</td>' +
                                '<td>' + (category.deskripsi || '-') + '</td>' +
                                '<td class="text-center">' +
                                '<button class="btn btn-sm btn-warning btn-edit" data-id="' + category.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="' + category.id + '" data-name="' + category.nama_kategori + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                                '</td>' +
                                '</tr>';
                        });

                        // Step 4: Insert HTML into tbody
                        tbody.html(html);
                    }

                    // Step 5: Initialize DataTables with Buttons
                    var table = $('#categoryTable').DataTable({
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
                                title: 'Category List - ' + new Date().toLocaleDateString('id-ID'),
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ri-file-pdf-line"></i> PDF',
                                title: 'Category List',
                                orientation: 'portrait',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="ri-printer-line"></i> Print',
                                title: 'Category List',
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
                showAlert('error', 'An error occurred while loading categories');
            }
        });
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewId).attr('src', e.target.result).show();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearErrors(prefix) {
        $('#' + prefix + '_error_nama_kategori').text('');
        $('#' + prefix + '_error_deskripsi').text('');
        $('#' + prefix + '_error_image').text('');
    }

    function displayErrors(prefix, errors) {
        if (errors.nama_kategori) {
            $('#' + prefix + '_error_nama_kategori').text(errors.nama_kategori);
        }
        if (errors.deskripsi) {
            $('#' + prefix + '_error_deskripsi').text(errors.deskripsi);
        }
        if (errors.image) {
            $('#' + prefix + '_error_image').text(errors.image);
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
</script>
<?= $this->endSection() ?>