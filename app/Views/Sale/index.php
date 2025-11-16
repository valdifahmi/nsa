<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">Stock Out (Barang Keluar)</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- Header Form -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal_keluar">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_keluar" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="penerima">Penerima</label>
                            <input type="text" class="form-control" id="penerima" placeholder="Nama Penerima">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <input type="text" class="form-control" id="catatan" placeholder="Catatan transaksi">
                        </div>
                    </div>
                </div>

                <!-- Barcode Scanner Input with Autocomplete -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="barcode_scanner">Scan Barcode / Kode Barang</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control form-control-lg" id="barcode_scanner" placeholder="Scan barcode atau ketik kode barang lalu tekan Enter" autocomplete="off" autofocus>
                                <div id="autocomplete_results" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 300px; overflow-y: auto; z-index: 1000; display: none;"></div>
                            </div>
                            <small class="text-muted">Scan barcode atau ketik kode barang, lalu tekan Enter</small>
                        </div>
                    </div>
                </div>

                <!-- Cart Table -->
                <div class="table-responsive">
                    <table id="cartTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Barang</th>
                                <th width="30%">Nama Barang</th>
                                <th width="10%">Satuan</th>
                                <th width="10%">Stok Tersedia</th>
                                <th width="15%">Jumlah</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Keranjang kosong. Scan barcode untuk menambah barang.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Checkout Button -->
                <div class="row mt-4">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary" id="btnClearCart">
                            <i class="ri-delete-bin-line"></i> Kosongkan Keranjang
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" id="btnCheckout">
                            <i class="ri-save-line"></i> Checkout (Simpan Transaksi)
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
        let autocompleteTimeout;

        // Barcode scanner event listener
        $('#barcode_scanner').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                var code = $(this).val().trim();

                if (code) {
                    searchProduct(code);
                    $(this).val(''); // Clear input
                    $('#autocomplete_results').hide();
                }
            }
        });

        // Autocomplete on input
        $('#barcode_scanner').on('input', function() {
            clearTimeout(autocompleteTimeout);
            var query = $(this).val().trim();

            if (query.length >= 2) {
                autocompleteTimeout = setTimeout(function() {
                    loadAutocomplete(query);
                }, 300);
            } else {
                $('#autocomplete_results').hide();
            }
        });

        // Click outside to close autocomplete
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#barcode_scanner, #autocomplete_results').length) {
                $('#autocomplete_results').hide();
            }
        });

        // Load autocomplete suggestions
        function loadAutocomplete(query) {
            $.ajax({
                url: '<?= base_url('product/searchAutocomplete') ?>',
                type: 'GET',
                data: {
                    q: query
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        var html = '';
                        $.each(response.data, function(index, product) {
                            var imagePath = product.image !== 'default.png' ?
                                '<?= base_url('uploads/products/') ?>' + product.image :
                                '<?= base_url('dist/assets/images/default.png') ?>';

                            var stockClass = product.stok_saat_ini > 0 ? 'text-success' : 'text-danger';
                            var stockText = product.stok_saat_ini > 0 ?
                                'Stok: ' + product.stok_saat_ini + ' ' + product.satuan :
                                'STOK HABIS';

                            html += '<div class="autocomplete-item" data-code="' + product.kode_barang + '" style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; align-items: center;">' +
                                '<img src="' + imagePath + '" alt="' + product.nama_barang + '" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px; border-radius: 4px;">' +
                                '<div>' +
                                '<strong>' + product.kode_barang + '</strong> - ' + product.nama_barang +
                                '<br><small class="' + stockClass + '">' + stockText + '</small>' +
                                '</div>' +
                                '</div>';
                        });
                        $('#autocomplete_results').html(html).show();
                    } else {
                        $('#autocomplete_results').hide();
                    }
                }
            });
        }

        // Click on autocomplete item
        $(document).on('click', '.autocomplete-item', function() {
            var code = $(this).data('code');
            $('#barcode_scanner').val(code);
            $('#autocomplete_results').hide();
            searchProduct(code);
            $('#barcode_scanner').val('').focus();
        });

        // Hover effect for autocomplete items
        $(document).on('mouseenter', '.autocomplete-item', function() {
            $(this).css('background-color', '#f8f9fa');
        }).on('mouseleave', '.autocomplete-item', function() {
            $(this).css('background-color', 'white');
        });

        // Search product by code
        function searchProduct(code) {
            $.ajax({
                url: '<?= base_url('product/findProductByCode') ?>',
                type: 'GET',
                data: {
                    code: code
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        var product = response.data;

                        // VALIDASI STOK - CRITICAL!
                        if (product.stok_saat_ini <= 0) {
                            showAlert('error', 'Stok ' + product.nama_barang + ' habis! Stok tersedia: 0');
                            return;
                        }

                        // Check if product already in cart
                        var existingItem = cart.find(item => item.product_id === product.id);

                        if (existingItem) {
                            showAlert('warning', 'Produk sudah ada di keranjang. Silakan ubah jumlahnya di tabel.');
                            return;
                        }

                        // Prompt for quantity with stock validation
                        var maxStock = product.stok_saat_ini;
                        var jumlah = prompt('Masukkan jumlah untuk ' + product.nama_barang + ' (Max: ' + maxStock + '):', '1');

                        if (jumlah) {
                            jumlah = parseInt(jumlah);

                            // Validasi jumlah
                            if (isNaN(jumlah) || jumlah <= 0) {
                                showAlert('error', 'Jumlah harus lebih dari 0');
                                return;
                            }

                            if (jumlah > maxStock) {
                                showAlert('error', 'Jumlah melebihi stok tersedia! Stok tersedia: ' + maxStock);
                                return;
                            }

                            addToCart(product, jumlah);
                        }
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat mencari produk');
                }
            });
        }

        // Add item to cart
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

        // Render cart table
        function renderCart() {
            var tbody = $('#cartBody');
            tbody.empty();

            if (cart.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center text-muted">Keranjang kosong. Scan barcode untuk menambah barang.</td></tr>');
                return;
            }

            $.each(cart, function(index, item) {
                var stockClass = item.stok_saat_ini >= item.jumlah ? 'text-success' : 'text-danger';

                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.kode_barang + '</td>' +
                    '<td>' + item.nama_barang + '</td>' +
                    '<td>' + item.satuan + '</td>' +
                    '<td class="' + stockClass + '">' + item.stok_saat_ini + '</td>' +
                    '<td>' +
                    '<input type="number" class="form-control form-control-sm item-jumlah" data-index="' + index + '" value="' + item.jumlah + '" min="1" max="' + item.stok_saat_ini + '" style="width: 100px;">' +
                    '</td>' +
                    '<td class="text-center">' +
                    '<button class="btn btn-sm btn-danger btn-remove" data-index="' + index + '" title="Hapus" data-toggle="tooltip"><i class="ri-delete-bin-line"></i></button>' +
                    '</td>' +
                    '</tr>';
                tbody.append(row);
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Update quantity in cart with stock validation
        $(document).on('change', '.item-jumlah', function() {
            var index = $(this).data('index');
            var newJumlah = parseInt($(this).val());
            var maxStock = cart[index].stok_saat_ini;

            if (newJumlah > 0 && newJumlah <= maxStock) {
                cart[index].jumlah = newJumlah;
            } else if (newJumlah > maxStock) {
                $(this).val(cart[index].jumlah);
                showAlert('warning', 'Jumlah melebihi stok tersedia! Stok tersedia: ' + maxStock);
            } else {
                $(this).val(cart[index].jumlah);
                showAlert('warning', 'Jumlah harus lebih dari 0');
            }
        });

        // Remove item from cart
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

            var tanggal_keluar = $('#tanggal_keluar').val();
            if (!tanggal_keluar) {
                showAlert('warning', 'Tanggal keluar harus diisi');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menyimpan transaksi ini?')) {
                return;
            }

            var data = {
                tanggal_keluar: tanggal_keluar,
                penerima: $('#penerima').val(),
                catatan: $('#catatan').val(),
                items: JSON.stringify(cart)
            };

            $.ajax({
                url: '<?= base_url('sale/store') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('success', response.message + ' (No: ' + response.nomor_transaksi + ')');

                        // Reset form
                        cart = [];
                        renderCart();
                        $('#penerima').val('');
                        $('#catatan').val('');
                        $('#tanggal_keluar').val('<?= date('Y-m-d') ?>');
                        $('#barcode_scanner').focus();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Terjadi kesalahan saat menyimpan transaksi');
                }
            });
        });

        function showAlert(type, message) {
            var alertClass = type === 'success' ? 'alert-success' :
                type === 'warning' ? 'alert-warning' :
                type === 'info' ? 'alert-info' : 'alert-danger';
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