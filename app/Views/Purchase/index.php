<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Stock In (Barang Masuk)</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Header Form -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="supplier_id" required>
                                    <option value="">Pilih Supplier</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" onclick="quickAddSupplier()" title="Tambah Supplier Baru">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_masuk" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_jatuh_tempo">Tgl. Jatuh Tempo</label>
                            <input type="date" class="form-control" id="tanggal_jatuh_tempo">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <input type="text" class="form-control" id="catatan" placeholder="Catatan transaksi (opsional)">
                        </div>
                    </div>
                </div>

                <!-- Barcode Scanner Input with Autocomplete & Quick Add Product -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="barcode_scanner">Scan Barcode / Kode Barang</label>
                            <div class="input-group">
                                <div style="position: relative; flex: 1;">
                                    <input type="text" class="form-control form-control-lg" id="barcode_scanner"
                                        placeholder="Scan barcode atau ketik kode barang"
                                        autocomplete="off" autofocus>
                                    <div id="autocomplete_dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 400px; overflow-y: auto; z-index: 1000; display: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                </div>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success btn-lg" onclick="quickAddProduct()" title="Tambah Produk Baru">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Ketik minimal 2 karakter untuk melihat saran produk, atau tekan Enter untuk mencari</small>
                        </div>
                    </div>
                </div>

                <!-- Cart Table (NO PRICE COLUMNS) -->
                <div class="table-responsive">
                    <table id="cartTable" class="table table-striped table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Barang</th>
                                <th width="35%">Nama Barang</th>
                                <th width="10%">Satuan</th>
                                <th width="15%">Jumlah</th>
                                <th width="10%">Stok Saat Ini</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ri-shopping-cart-line" style="font-size: 48px; opacity: 0.3;"></i>
                                    <p class="mt-2">Keranjang kosong. Scan barcode untuk menambah barang.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Action Buttons (NO TOTAL DISPLAY) -->
                <div class="row mt-4">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-lg" id="btnClearCart">
                            <i class="ri-delete-bin-line"></i> Kosongkan Keranjang
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" id="btnCheckout">
                            <i class="ri-save-line"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let cart = [];

        // Load suppliers on page load
        loadSuppliers();

        // Load suppliers dropdown
        function loadSuppliers() {
            $.ajax({
                url: '<?= base_url('supplier/getForDropdown') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var options = '<option value="">Pilih Supplier</option>';
                        $.each(response.data, function(index, supplier) {
                            options += '<option value="' + supplier.id + '">' + supplier.nama_supplier + '</option>';
                        });
                        $('#supplier_id').html(options);
                    }
                }
            });
        }

        // Quick add supplier function
        window.quickAddSupplier = function() {
            if (typeof window.addSupplierQuick === 'function') {
                window.addSupplierQuick(function(newSupplier) {
                    loadSuppliers();
                    setTimeout(function() {
                        $('#supplier_id').val(newSupplier.id);
                        showAlert('success', 'Supplier berhasil ditambahkan: ' + newSupplier.nama_supplier);
                    }, 500);
                });
            } else {
                window.open('<?= base_url('supplier') ?>', '_blank');
                showAlert('info', 'Silakan tambah supplier di tab baru, lalu refresh halaman ini');
            }
        };

        // Quick add product function
        window.quickAddProduct = function() {
            window.open('<?= base_url('product') ?>', '_blank');
            showAlert('info', 'Silakan tambah produk di tab baru, lalu refresh halaman ini untuk melihat produk baru');
        };

        // Autocomplete on input
        let autocompleteTimeout;
        $('#barcode_scanner').on('input', function() {
            clearTimeout(autocompleteTimeout);
            var query = $(this).val().trim();

            if (query.length >= 2) {
                autocompleteTimeout = setTimeout(function() {
                    loadAutocomplete(query);
                }, 300);
            } else {
                $('#autocomplete_dropdown').hide();
            }
        });

        // Enter key to search
        $('#barcode_scanner').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                var code = $(this).val().trim();
                if (code) {
                    $('#autocomplete_dropdown').hide();
                    searchProduct(code);
                    $(this).val('');
                }
            }
        });

        // Click outside to close
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#barcode_scanner, #autocomplete_dropdown').length) {
                $('#autocomplete_dropdown').hide();
            }
        });

        // Load autocomplete suggestions
        function loadAutocomplete(query) {
            $.ajax({
                url: '<?= base_url('purchase/searchProducts') ?>',
                type: 'GET',
                data: {
                    q: query
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Autocomplete response:', response);

                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        var html = '';
                        $.each(response.data, function(index, product) {
                            var stockClass = product.stok_saat_ini <= 0 ? 'text-danger' :
                                product.stok_saat_ini <= product.min_stok ? 'text-warning' : 'text-success';

                            // Product image URL
                            var imageUrl = product.image && product.image !== 'default.png' ?
                                '<?= base_url('uploads/products/') ?>' + product.image :
                                '<?= base_url('dist/assets/images/default.png') ?>';

                            html += '<div class="autocomplete-item" data-code="' + product.kode_barang + '" ' +
                                'style="padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 12px; transition: background-color 0.2s;">' +
                                '<img src="' + imageUrl + '" alt="' + product.nama_barang + '" ' +
                                'style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">' +
                                '<div style="flex: 1;">' +
                                '<div style="font-weight: 600; color: #333; margin-bottom: 4px;">' +
                                '<span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-right: 8px;">' +
                                product.kode_barang + '</span>' +
                                product.nama_barang +
                                '</div>' +
                                '<div style="font-size: 13px; color: #666;">' +
                                'Stok: <span class="' + stockClass + '" style="font-weight: 600;">' + product.stok_saat_ini + '</span> ' + product.satuan +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        });
                        $('#autocomplete_dropdown').html(html).show();
                    } else {
                        $('#autocomplete_dropdown').html(
                            '<div style="padding: 15px; text-align: center; color: #999;">' +
                            '<i class="ri-search-line" style="font-size: 24px; opacity: 0.5;"></i>' +
                            '<p style="margin: 8px 0 0 0;">Produk tidak ditemukan</p>' +
                            '</div>'
                        ).show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Autocomplete error:', error);
                    $('#autocomplete_dropdown').hide();
                }
            });
        }

        // Click on autocomplete item
        $(document).on('click', '.autocomplete-item', function() {
            var code = $(this).data('code');
            $('#barcode_scanner').val('');
            $('#autocomplete_dropdown').hide();
            searchProduct(code);
        });

        // Hover effect
        $(document).on('mouseenter', '.autocomplete-item', function() {
            $(this).css('background-color', '#f8f9fa');
        }).on('mouseleave', '.autocomplete-item', function() {
            $(this).css('background-color', 'white');
        });

        // Search product by code
        function searchProduct(code) {
            $.ajax({
                url: '<?= base_url('purchase/findProductByCode') ?>',
                type: 'GET',
                data: {
                    code: code
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var product = response.data;
                        var existingItem = cart.find(item => item.product_id === product.id);

                        if (existingItem) {
                            showAlert('warning', 'Produk sudah ada di keranjang. Silakan ubah jumlahnya di tabel.');
                            return;
                        }

                        var jumlah = prompt('Masukkan jumlah untuk ' + product.nama_barang + ':', '1');
                        if (jumlah && parseInt(jumlah) > 0) {
                            addToCart(product, parseInt(jumlah));
                        }
                    } else {
                        showAlert('error', response.message || 'Produk tidak ditemukan');
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mencari produk');
                }
            });
        }

        // Add to cart
        function addToCart(product, jumlah) {
            cart.push({
                product_id: product.id,
                kode_barang: product.kode_barang,
                nama_barang: product.nama_barang,
                satuan: product.satuan,
                stok_saat_ini: product.stok_saat_ini,
                jumlah: jumlah
            });
            renderCart();
            showAlert('success', 'Produk ditambahkan ke keranjang');
            $('#barcode_scanner').focus();
        }

        // Render cart
        function renderCart() {
            var tbody = $('#cartBody');
            tbody.empty();

            if (cart.length === 0) {
                tbody.append(
                    '<tr><td colspan="7" class="text-center text-muted py-4">' +
                    '<i class="ri-shopping-cart-line" style="font-size: 48px; opacity: 0.3;"></i>' +
                    '<p class="mt-2">Keranjang kosong. Scan barcode untuk menambah barang.</p>' +
                    '</td></tr>'
                );
                return;
            }

            $.each(cart, function(index, item) {
                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.kode_barang + '</td>' +
                    '<td>' + item.nama_barang + '</td>' +
                    '<td>' + item.satuan + '</td>' +
                    '<td><input type="number" class="form-control form-control-sm item-jumlah" data-index="' + index + '" value="' + item.jumlah + '" min="1" style="width: 100px;"></td>' +
                    '<td>' + item.stok_saat_ini + '</td>' +
                    '<td class="text-center"><button class="btn btn-sm btn-danger btn-remove" data-index="' + index + '" title="Hapus"><i class="ri-delete-bin-line"></i></button></td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        // Update quantity
        $(document).on('change', '.item-jumlah', function() {
            var index = $(this).data('index');
            var newJumlah = parseInt($(this).val());
            if (newJumlah > 0) {
                cart[index].jumlah = newJumlah;
            } else {
                $(this).val(cart[index].jumlah);
                showAlert('warning', 'Jumlah harus lebih dari 0');
            }
        });

        // Remove item
        $(document).on('click', '.btn-remove', function() {
            var index = $(this).data('index');
            cart.splice(index, 1);
            renderCart();
            showAlert('info', 'Item dihapus dari keranjang');
            $('#barcode_scanner').focus();
        });

        // Clear cart
        $('#btnClearCart').on('click', function() {
            if (cart.length === 0) {
                showAlert('warning', 'Keranjang sudah kosong');
                return;
            }
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                cart = [];
                renderCart();
                showAlert('info', 'Keranjang dikosongkan');
                $('#barcode_scanner').focus();
            }
        });

        // Checkout
        $('#btnCheckout').on('click', function() {
            if (cart.length === 0) {
                showAlert('warning', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
                return;
            }

            var supplier_id = $('#supplier_id').val();
            if (!supplier_id) {
                showAlert('warning', 'Supplier harus dipilih');
                $('#supplier_id').focus();
                return;
            }

            var tanggal_masuk = $('#tanggal_masuk').val();
            if (!tanggal_masuk) {
                showAlert('warning', 'Tanggal masuk harus diisi');
                $('#tanggal_masuk').focus();
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menyimpan transaksi ini?')) {
                return;
            }

            $('#btnCheckout').prop('disabled', true).html('<i class="ri-loader-4-line"></i> Menyimpan...');

            var data = {
                supplier_id: parseInt(supplier_id),
                tanggal_masuk: tanggal_masuk,
                tanggal_jatuh_tempo: $('#tanggal_jatuh_tempo').val() || null,
                catatan: $('#catatan').val() || null,
                items: cart
            };

            $.ajax({
                url: '<?= base_url('purchase/store') ?>',
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('success', response.message + ' (No: ' + response.nomor_transaksi + ')');
                        cart = [];
                        renderCart();
                        $('#supplier_id').val('');
                        $('#catatan').val('');
                        $('#tanggal_masuk').val('<?= date('Y-m-d') ?>');
                        $('#barcode_scanner').focus();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Terjadi kesalahan saat menyimpan transaksi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMsg);
                },
                complete: function() {
                    $('#btnCheckout').prop('disabled', false).html('<i class="ri-save-line"></i> Simpan Transaksi');
                }
            });
        });

        // Show alert
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