<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Product List</h4>
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
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProductModal">
                        <i class="ri-add-line"></i> Add Product
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="productTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Product Code</th>
                                <th width="23%">Product Name</th>
                                <th width="12%">Category</th>
                                <th width="12%">Brand</th>
                                <th width="8%">Unit</th>
                                <th width="8%">Stock</th>
                                <th width="8%">Min Stock</th>
                                <th width="12%" class="text-center">Actions</th>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_kode_barang">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_kode_barang" name="kode_barang" required>
                                <small class="text-danger" id="add_error_kode_barang"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_nama_barang">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_nama_barang" name="nama_barang" required>
                                <small class="text-danger" id="add_error_nama_barang"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_category_id">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="add_category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                </select>
                                <small class="text-danger" id="add_error_category_id"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_brand_id">Brand</label>
                                <select class="form-control" id="add_brand_id" name="brand_id">
                                    <option value="">Select Brand</option>
                                </select>
                                <small class="text-danger" id="add_error_brand_id"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_satuan">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_satuan" name="satuan" placeholder="e.g., Pcs, Unit, Box" required>
                                <small class="text-danger" id="add_error_satuan"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_stok_saat_ini">Current Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_stok_saat_ini" name="stok_saat_ini" value="0" required>
                                <small class="text-danger" id="add_error_stok_saat_ini"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_min_stok">Min Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_min_stok" name="min_stok" value="0" required>
                                <small class="text-danger" id="add_error_min_stok"></small>
                            </div>
                        </div>
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
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_product_id" name="id">
                <input type="hidden" id="edit_old_image" name="old_image">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kode_barang">Product Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_kode_barang" name="kode_barang" required>
                                <small class="text-danger" id="edit_error_kode_barang"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nama_barang">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                                <small class="text-danger" id="edit_error_nama_barang"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_category_id">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                </select>
                                <small class="text-danger" id="edit_error_category_id"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_brand_id">Brand</label>
                                <select class="form-control" id="edit_brand_id" name="brand_id">
                                    <option value="">Select Brand</option>
                                </select>
                                <small class="text-danger" id="edit_error_brand_id"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_satuan">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_satuan" name="satuan" required>
                                <small class="text-danger" id="edit_error_satuan"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_stok_saat_ini">Current Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_stok_saat_ini" name="stok_saat_ini" required>
                                <small class="text-danger" id="edit_error_stok_saat_ini"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_min_stok">Min Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_min_stok" name="min_stok" required>
                                <small class="text-danger" id="edit_error_min_stok"></small>
                            </div>
                        </div>
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
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Load products on page load
        loadProducts();

        // Load dropdowns when add modal is opened
        $('#addProductModal').on('show.bs.modal', function() {
            loadDropdowns('add');
        });

        // Add product form submit
        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('add');

            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('product/store') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        $('#add_image_preview').hide();
                        loadProducts();
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
                    showAlert('error', 'An error occurred while saving the product');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            clearErrors('edit');

            // Load dropdowns first, then load product data
            $.ajax({
                url: '<?= base_url('product/fetchDropdowns') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(dropdownResponse) {
                    if (dropdownResponse.status === 'success') {
                        var categories = dropdownResponse.data.categories;
                        var brands = dropdownResponse.data.brands;

                        // Populate categories dropdown
                        var categorySelect = $('#edit_category_id');
                        categorySelect.find('option:not(:first)').remove();
                        $.each(categories, function(index, category) {
                            categorySelect.append('<option value="' + category.id + '">' + category.nama_kategori + '</option>');
                        });

                        // Populate brands dropdown
                        var brandSelect = $('#edit_brand_id');
                        brandSelect.find('option:not(:first)').remove();
                        $.each(brands, function(index, brand) {
                            brandSelect.append('<option value="' + brand.id + '">' + brand.nama_brand + '</option>');
                        });

                        // Now load product data
                        $.ajax({
                            url: '<?= base_url('product/edit') ?>/' + id,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    var product = response.data;
                                    $('#edit_product_id').val(product.id);
                                    $('#edit_kode_barang').val(product.kode_barang);
                                    $('#edit_nama_barang').val(product.nama_barang);
                                    $('#edit_category_id').val(product.category_id);
                                    $('#edit_brand_id').val(product.brand_id || '');
                                    $('#edit_satuan').val(product.satuan);
                                    $('#edit_stok_saat_ini').val(product.stok_saat_ini);
                                    $('#edit_min_stok').val(product.min_stok);
                                    $('#edit_deskripsi').val(product.deskripsi);
                                    $('#edit_old_image').val(product.image);

                                    var imagePath = product.image !== 'default.png' ?
                                        '<?= base_url('uploads/products/') ?>' + product.image :
                                        '<?= base_url('dist/assets/images/default.png') ?>';
                                    $('#edit_current_image').attr('src', imagePath);
                                    $('#edit_image_preview').hide();

                                    $('#editProductModal').modal('show');
                                } else {
                                    showAlert('error', response.message);
                                }
                            },
                            error: function() {
                                showAlert('error', 'An error occurred while loading product data');
                            }
                        });
                    }
                },
                error: function() {
                    showAlert('error', 'An error occurred while loading dropdown data');
                }
            });
        });

        // Edit product form submit
        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors('edit');

            var id = $('#edit_product_id').val();
            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('product/update') ?>/' + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editProductModal').modal('hide');
                        $('#editProductForm')[0].reset();
                        loadProducts();
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
                    showAlert('error', 'An error occurred while updating the product');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');

            if (confirm('Are you sure you want to delete product "' + name + '"?')) {
                $.ajax({
                    url: '<?= base_url('product/delete') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadProducts();
                            showAlert('success', response.message);
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', 'An error occurred while deleting the product');
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
        $('#addProductModal').on('hidden.bs.modal', function() {
            $('#addProductForm')[0].reset();
            $('#add_image_preview').hide();
            clearErrors('add');
        });

        // Reset edit form when modal is closed
        $('#editProductModal').on('hidden.bs.modal', function() {
            $('#editProductForm')[0].reset();
            $('#edit_image_preview').hide();
            clearErrors('edit');
        });

        // Export dropdown handlers
        $(document).on('click', '#exportCopy', function(e) {
            e.preventDefault();
            var table = $('#productTable').DataTable();
            table.button(0).trigger();
        });

        $(document).on('click', '#exportExcel', function(e) {
            e.preventDefault();
            var table = $('#productTable').DataTable();
            table.button(1).trigger();
        });

        $(document).on('click', '#exportPDF', function(e) {
            e.preventDefault();
            var table = $('#productTable').DataTable();
            table.button(2).trigger();
        });

        $(document).on('click', '#exportPrint', function(e) {
            e.preventDefault();
            var table = $('#productTable').DataTable();
            table.button(3).trigger();
        });
    });

    function loadProducts() {
        $.ajax({
            url: '<?= base_url('product/fetchAll') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Step 1: Destroy existing DataTable instance if it exists
                    if ($.fn.DataTable.isDataTable('#productTable')) {
                        $('#productTable').DataTable().destroy();
                    }

                    // Step 2: Clear and rebuild tbody
                    var tbody = $('#productTable tbody');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="9" class="text-center">No products found</td></tr>');
                    } else {
                        // Step 3: Build HTML string with forEach loop
                        var html = '';
                        $.each(response.data, function(index, product) {
                            var imagePath = product.image !== 'default.png' ?
                                '<?= base_url('uploads/products/') ?>' + product.image :
                                '<?= base_url('dist/assets/images/default.png') ?>';

                            var stockClass = product.stok_saat_ini <= product.min_stok ? 'text-danger font-weight-bold' : '';

                            html += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + product.kode_barang + '</td>' +
                                '<td>' +
                                '<div class="d-flex align-items-center">' +
                                '<img src="' + imagePath + '" alt="' + product.nama_barang + '" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">' +
                                '<span>' + product.nama_barang + '</span>' +
                                '</div>' +
                                '</td>' +
                                '<td>' + (product.nama_kategori || '-') + '</td>' +
                                '<td>' + (product.nama_brand || '-') + '</td>' +
                                '<td>' + product.satuan + '</td>' +
                                '<td class="' + stockClass + '">' + product.stok_saat_ini + '</td>' +
                                '<td>' + product.min_stok + '</td>' +
                                '<td class="text-center">' +
                                '<button class="btn btn-sm btn-warning btn-edit" data-id="' + product.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                                '<button class="btn btn-sm btn-danger btn-delete" data-id="' + product.id + '" data-name="' + product.nama_barang + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                                '</td>' +
                                '</tr>';
                        });

                        // Step 4: Insert HTML into tbody
                        tbody.html(html);
                    }

                    // Step 5: Initialize DataTables with Buttons
                    var table = $('#productTable').DataTable({
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
                                title: 'Product List - ' + new Date().toLocaleDateString('id-ID'),
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="ri-file-pdf-line"></i> PDF',
                                title: 'Product List',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="ri-printer-line"></i> Print',
                                title: 'Product List',
                                exportOptions: {
                                    columns: ':visible:not(:last-child)'
                                }
                            }
                        ],
                        pageLength: 25,
                        responsive: true,
                        destroy: true, // Important for AJAX reload
                        order: [
                            [0, 'asc']
                        ], // Sort by No column (ascending)
                        columnDefs: [{
                                targets: 0, // No column
                                orderable: true,
                                type: 'num'
                            },
                            {
                                targets: -1, // Actions column (last)
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                        },
                        drawCallback: function() {
                            // Re-initialize tooltips after table draw
                            $('[title]').tooltip();
                        }
                    });
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while loading products');
            }
        });
    }

    function loadDropdowns(prefix) {
        $.ajax({
            url: '<?= base_url('product/fetchDropdowns') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var categories = response.data.categories;
                    var brands = response.data.brands;

                    // Populate categories dropdown
                    var categorySelect = $('#' + prefix + '_category_id');
                    categorySelect.find('option:not(:first)').remove();
                    $.each(categories, function(index, category) {
                        categorySelect.append('<option value="' + category.id + '">' + category.nama_kategori + '</option>');
                    });

                    // Populate brands dropdown
                    var brandSelect = $('#' + prefix + '_brand_id');
                    brandSelect.find('option:not(:first)').remove();
                    $.each(brands, function(index, brand) {
                        brandSelect.append('<option value="' + brand.id + '">' + brand.nama_brand + '</option>');
                    });
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while loading dropdown data');
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
        $('#' + prefix + '_error_kode_barang').text('');
        $('#' + prefix + '_error_nama_barang').text('');
        $('#' + prefix + '_error_category_id').text('');
        $('#' + prefix + '_error_brand_id').text('');
        $('#' + prefix + '_error_deskripsi').text('');
        $('#' + prefix + '_error_satuan').text('');
        $('#' + prefix + '_error_stok_saat_ini').text('');
        $('#' + prefix + '_error_min_stok').text('');
        $('#' + prefix + '_error_image').text('');
    }

    function displayErrors(prefix, errors) {
        if (errors.kode_barang) {
            $('#' + prefix + '_error_kode_barang').text(errors.kode_barang);
        }
        if (errors.nama_barang) {
            $('#' + prefix + '_error_nama_barang').text(errors.nama_barang);
        }
        if (errors.category_id) {
            $('#' + prefix + '_error_category_id').text(errors.category_id);
        }
        if (errors.brand_id) {
            $('#' + prefix + '_error_brand_id').text(errors.brand_id);
        }
        if (errors.deskripsi) {
            $('#' + prefix + '_error_deskripsi').text(errors.deskripsi);
        }
        if (errors.satuan) {
            $('#' + prefix + '_error_satuan').text(errors.satuan);
        }
        if (errors.stok_saat_ini) {
            $('#' + prefix + '_error_stok_saat_ini').text(errors.stok_saat_ini);
        }
        if (errors.min_stok) {
            $('#' + prefix + '_error_min_stok').text(errors.min_stok);
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