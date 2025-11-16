<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="col-lg-12">
    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-3">Product List</h4>
            <p class="mb-0">The product list effectively dictates product presentation and provides space<br> to list your products and offering in the most appealing way.</p>
        </div>
        <a href="#" data-toggle="modal" data-target="#add-note" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Product</a>
    </div>
</div>
<div class="col-lg-12">
    <div class="table-responsive rounded mb-3">
        <table class="data-table table mb-0 tbl-server-info">
            <thead class="bg-white text-uppercase">
                <tr class="ligth ligth-data">

                    <th>Nama Barang</th>
                    <th>Brand Name</th>
                    <th>Kode Barang</th>
                    <th>Satuan</th>
                    <th>Min Stok</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="ligth-body">
                <tr>

                    <td>
                        <div class="d-flex align-items-center">
                            <img src="<?= base_url(); ?>dist/assets/images/table/product/01.jpg" class="img-fluid rounded avatar-50 mr-3" alt="image">
                            <div>
                                Organic Cream
                            </div>
                        </div>
                    </td>
                    <td>Beauty</td>
                    <td>CREM01</td>
                    <td>Unit</td>
                    <td>10</td>
                    <td>50</td>
                    <td>
                        <div class="d-flex align-items-center list-action">
                            <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"
                                href="#"><i class="ri-eye-line mr-0"></i></a>
                            <a class="badge bg-success mr-2" data-toggle="modal" data-target="#edit-note" data-original-title="Edit"
                                href="#"><i class="ri-pencil-line mr-0"></i></a>
                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"
                                href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
</div>
<!-- Page end  -->
</div>
<!-- Modal add -->
<div class="modal fade" id="add-note" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <div class="media align-items-top justify-content-between">
                        <h3 class="mb-3">Add Product</h3>
                        <div class="btn-cancel p-0" data-dismiss="modal"><i class="las la-times"></i></div>
                    </div>
                    <div class="content add-notes">
                        <div class="card card-transparent card-block card-stretch event-note mb-0">
                            <div class="card-body">
                                <form action="page-list-product.html" data-toggle="validator">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Category *</label>
                                                <select name="type" class="selectpicker form-control" data-style="py-0">
                                                    <option>Standard</option>
                                                    <option>Combo</option>
                                                    <option>Digital</option>
                                                    <option>Service</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Barang *</label>
                                                <input type="text" class="form-control" placeholder="Enter Name" data-errors="Please Enter Name." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kode Barang *</label>
                                                <input type="text" class="form-control" placeholder="Enter Code" data-errors="Please Enter Code." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Satuan</label>
                                                <input type="text" class="form-control" placeholder="Satuan" data-errors="Please Enter Satuan." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Min Stok *</label>
                                                <input type="text" class="form-control" placeholder="Enter Price" data-errors="Please Enter Price." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Quantity *</label>
                                                <input type="text" class="form-control" placeholder="Enter Quantity" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" class="form-control image-file" name="pic" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description / Product Details</label>
                                                <textarea class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Add Product</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit -->
<div class="modal fade" id="edit-note" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <div class="media align-items-top justify-content-between">
                        <h3 class="mb-3">Edit Product</h3>
                        <div class="btn-cancel p-0" data-dismiss="modal"><i class="las la-times"></i></div>
                    </div>
                    <div class="content edit-notes">
                        <div class="card card-transparent card-block card-stretch event-note mb-0">
                            <div class="card-body">
                                <form action="page-list-product.html" data-toggle="validator">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Category *</label>
                                                <select name="type" class="selectpicker form-control" data-style="py-0">
                                                    <option>Standard</option>
                                                    <option>Combo</option>
                                                    <option>Digital</option>
                                                    <option>Service</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Barang *</label>
                                                <input type="text" class="form-control" placeholder="Enter Name" data-errors="Please Enter Name." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kode Barang *</label>
                                                <input type="text" class="form-control" placeholder="Enter Code" data-errors="Please Enter Code." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Satuan</label>
                                                <input type="text" class="form-control" placeholder="Satuan" data-errors="Please Enter Satuan." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Min Stok *</label>
                                                <input type="text" class="form-control" placeholder="Enter Price" data-errors="Please Enter Price." required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Quantity *</label>
                                                <input type="text" class="form-control" placeholder="Enter Quantity" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" class="form-control image-file" name="pic" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description / Product Details</label>
                                                <textarea class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Update Product</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>

<?= $this->endSection(); ?>