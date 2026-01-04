<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Update Work Order</h4>
                </div>
                <div>
                    <a href="<?= base_url('sale') ?>" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Work Order Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Nomor Transaksi: <span class="text-primary"><?= $workOrder['nomor_transaksi'] ?></span></h5>
                        <p class="mb-1"><strong>Client:</strong> <?= $client['nama_klien'] ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?= $client['alamat'] ?? '-' ?></p>
                        <p class="mb-1"><strong>Telepon:</strong> <?= $client['telepon'] ?? '-' ?></p>
                    </div>
                    <div class="col-md-6 text-right">
                        <h5>Status: <span class="badge badge-warning">Proses</span></h5>
                        <p class="mb-1"><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($workOrder['tanggal_keluar'])) ?></p>
                        <p class="mb-1"><strong>Invoice:</strong> <?= $workOrder['nomor_invoice'] ?></p>
                    </div>
                </div>

                <hr>

                <!-- Section 1: Existing Items -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-shopping-cart-line"></i> Barang yang Sudah Keluar</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="existingItemsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode Barang</th>
                                        <th width="35%">Nama Barang</th>
                                        <th width="10%">Satuan</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Harga Jual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($items)): ?>
                                        <?php $no = 1;
                                        foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $item['kode_barang'] ?></td>
                                                <td><?= $item['nama_barang'] ?></td>
                                                <td><?= $item['satuan'] ?></td>
                                                <td class="text-center"><?= $item['jumlah'] ?></td>
                                                <td class="text-right">Rp <?= number_format($item['harga_jual_satuan'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada barang</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Section 2: Add New Item -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-add-circle-line"></i> Tambah Barang Baru</h5>
                        <div class="form-row">
                            <div class="col-md-6">
                                <label>Scan/Input Kode Barang</label>
                                <input type="text" class="form-control" id="barcodeInput" placeholder="Scan barcode atau ketik kode barang" autofocus>
                            </div>
                            <div class="col-md-3">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" id="jumlahInput" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="btnAddItem">
                                    <i class="ri-add-line"></i> Tambah
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Tekan Enter setelah scan barcode untuk menambah barang</small>
                    </div>
                </div>

                <hr>

                <!-- Section 3: Services -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="ri-tools-line"></i> Jasa Service</h5>

                        <!-- Add Service Form -->
                        <div class="form-row mb-3">
                            <div class="col-md-6">
                                <label>Pilih Jasa</label>
                                <div class="input-group">
                                    <select class="form-control" id="serviceSelect">
                                        <option value="">-- Pilih Jasa --</option>
                                        <?php foreach ($allServices as $svc): ?>
                                            <option value="<?= $svc['id'] ?>" data-price="<?= $svc['harga_standar'] ?>" data-name="<?= $svc['nama_jasa'] ?>">
                                                <?= $svc['nama_jasa'] ?> - Rp <?= number_format($svc['harga_standar'], 0, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <a href="<?= base_url('service') ?>" target="_blank" class="btn btn-outline-secondary" title="Kelola Master Jasa">
                                            <i class="ri-settings-3-line"></i>
                                        </a>
                                    </div>
                                </div>
                                <small class="text-muted">Klik icon untuk mengelola master jasa</small>
                            </div>
                            <div class="col-md-3">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" id="serviceJumlah" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-success btn-block" id="btnAddService">
                                    <i class="ri-add-line"></i> Tambah Jasa
                                </button>
                            </div>
                        </div>

                        <!-- Services Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="servicesTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="50%">Nama Jasa</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Harga</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($services)): ?>
                                        <?php $no = 1;
                                        foreach ($services as $service): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $service['nama_jasa'] ?></td>
                                                <td class="text-center"><?= $service['jumlah'] ?></td>
                                                <td class="text-right">Rp <?= number_format($service['harga_jasa'], 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger" onclick="deleteService(<?= $service['id'] ?>)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="noServiceRow">
                                            <td colspan="5" class="text-center">Belum ada jasa</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Section 4: Tax Settings & Preview -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="ri-percent-line"></i> Pengaturan Pajak</h5>
                        <div class="form-group">
                            <label>PPN Barang (%)</label>
                            <input type="number" class="form-control" id="ppnPersen" value="<?= $workOrder['ppn_persen'] ?>" min="0" max="100" step="0.1">
                            <small class="text-muted">Default: 11%</small>
                        </div>
                        <div class="form-group">
                            <label>PPh 23 Jasa (%) - Potongan</label>
                            <input type="number" class="form-control" id="pphPersen" value="<?= $workOrder['pph_persen'] ?>" min="0" max="100" step="0.1">
                            <small class="text-muted">Default: 2%</small>
                        </div>
                        <button type="button" class="btn btn-info btn-block" id="btnUpdateTax">
                            <i class="ri-refresh-line"></i> Update Perhitungan
                        </button>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="ri-calculator-line"></i> Preview Grand Total</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td><strong>Total Barang:</strong></td>
                                        <td class="text-right" id="previewTotalBarang">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PPN (<span id="previewPpnPersen">11</span>%):</strong></td>
                                        <td class="text-right" id="previewPPN">Rp 0</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Subtotal Barang:</strong></td>
                                        <td class="text-right" id="previewSubtotalBarang">Rp 0</td>
                                    </tr>
                                    <tr class="mt-2">
                                        <td><strong>Total Jasa:</strong></td>
                                        <td class="text-right" id="previewTotalJasa">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PPh 23 (<span id="previewPphPersen">2</span>%):</strong></td>
                                        <td class="text-right text-danger" id="previewPPh">(Rp 0)</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Subtotal Jasa:</strong></td>
                                        <td class="text-right" id="previewSubtotalJasa">Rp 0</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td>
                                            <h5><strong>GRAND TOTAL:</strong></h5>
                                        </td>
                                        <td class="text-right">
                                            <h5 class="text-primary" id="previewGrandTotal">Rp 0</h5>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Finalize Button -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-success btn-lg" id="btnFinalize">
                            <i class="ri-check-double-line"></i> Finalisasi & Generate Invoice
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery UI for Autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    const workOrderId = <?= $workOrder['id'] ?>;
    const baseUrl = '<?= base_url() ?>';

    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // Initialize Autocomplete for Barcode Input
    $(document).ready(function() {
        $('#barcodeInput').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: baseUrl + 'product/searchProducts',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response($.map(data.data, function(item) {
                            return {
                                label: item.kode_barang + ' - ' + item.nama_barang + ' (Stok: ' + item.stok_saat_ini + ')',
                                value: item.kode_barang,
                                product: item
                            };
                        }));
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                // Store selected product data
                $(this).data('selectedProduct', ui.item.product);
                $('#jumlahInput').focus();
                return true;
            }
        });
    });

    // Calculate Preview
    function calculatePreview() {
        const ppnPersen = parseFloat(document.getElementById('ppnPersen').value) || 0;
        const pphPersen = parseFloat(document.getElementById('pphPersen').value) || 0;

        // Get total barang from existing items
        let totalBarang = 0;
        const itemRows = document.querySelectorAll('#existingItemsTable tbody tr');
        itemRows.forEach(row => {
            const cells = row.cells;
            if (cells.length > 5) {
                const jumlah = parseInt(cells[4].textContent) || 0;
                const hargaText = cells[5].textContent.replace(/[^0-9]/g, '');
                const harga = parseInt(hargaText) || 0;
                totalBarang += (jumlah * harga);
            }
        });

        // Get total jasa from services
        let totalJasa = 0;
        const serviceRows = document.querySelectorAll('#servicesTable tbody tr');
        serviceRows.forEach(row => {
            if (row.id !== 'noServiceRow' && row.cells.length > 3) {
                const jumlah = parseInt(row.cells[2].textContent) || 0;
                const hargaText = row.cells[3].textContent.replace(/[^0-9]/g, '');
                const harga = parseInt(hargaText) || 0;
                totalJasa += (jumlah * harga);
            }
        });

        // Calculate taxes
        const totalPPN = Math.round(totalBarang * (ppnPersen / 100));
        const totalPPh = Math.round(totalJasa * (pphPersen / 100));

        // Calculate subtotals
        const subtotalBarang = totalBarang + totalPPN;
        const subtotalJasa = totalJasa - totalPPh;

        // Calculate grand total
        const grandTotal = subtotalBarang + subtotalJasa;

        // Update preview
        document.getElementById('previewTotalBarang').textContent = formatRupiah(totalBarang);
        document.getElementById('previewPpnPersen').textContent = ppnPersen;
        document.getElementById('previewPPN').textContent = formatRupiah(totalPPN);
        document.getElementById('previewSubtotalBarang').textContent = formatRupiah(subtotalBarang);

        document.getElementById('previewTotalJasa').textContent = formatRupiah(totalJasa);
        document.getElementById('previewPphPersen').textContent = pphPersen;
        document.getElementById('previewPPh').textContent = '(' + formatRupiah(totalPPh) + ')';
        document.getElementById('previewSubtotalJasa').textContent = formatRupiah(subtotalJasa);

        document.getElementById('previewGrandTotal').textContent = formatRupiah(grandTotal);
    }

    // Add Item to WO (with AJAX table update - no reload)
    function addItemToWO() {
        const barcodeInput = document.getElementById('barcodeInput');
        const kodeBarang = barcodeInput.value.trim();
        const jumlah = parseInt(document.getElementById('jumlahInput').value) || 1;

        if (!kodeBarang) {
            alert('Masukkan kode barang!');
            return;
        }

        // Get selected product from autocomplete data
        const selectedProduct = $(barcodeInput).data('selectedProduct');

        if (selectedProduct) {
            // Use selected product from autocomplete
            addItemToWOAjax(selectedProduct, jumlah);
        } else {
            // Find product by code
            fetch(baseUrl + 'product/findProductByCode?code=' + encodeURIComponent(kodeBarang))
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'success' && response.data) {
                        addItemToWOAjax(response.data, jumlah);
                    } else {
                        alert('Produk tidak ditemukan!');
                    }
                })
                .catch(() => alert('Gagal mencari produk!'));
        }
    }

    // Add Item to WO via AJAX and update table without reload
    function addItemToWOAjax(product, jumlah) {
        fetch(baseUrl + 'sale/addItemToWO', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    stock_out_id: workOrderId,
                    product_id: product.id,
                    jumlah: jumlah
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Add new row to table without reload
                    addItemRowToTable(product, jumlah, res.data.harga_jual_satuan);

                    // Clear inputs
                    document.getElementById('barcodeInput').value = '';
                    document.getElementById('jumlahInput').value = '1';
                    $(document.getElementById('barcodeInput')).removeData('selectedProduct');

                    // Recalculate preview
                    calculatePreview();

                    // Focus back to barcode input
                    document.getElementById('barcodeInput').focus();

                    // Show success message
                    showToast('success', 'Item berhasil ditambahkan!');
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(() => alert('Gagal menambahkan item!'));
    }

    // Add row to items table
    function addItemRowToTable(product, jumlah, hargaJual) {
        const tbody = document.querySelector('#existingItemsTable tbody');

        // Remove "no data" row if exists
        const noDataRow = tbody.querySelector('tr td[colspan="6"]');
        if (noDataRow) {
            noDataRow.parentElement.remove();
        }

        // Get current row count
        const rowCount = tbody.querySelectorAll('tr').length + 1;

        // Create new row
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td>${product.kode_barang}</td>
            <td>${product.nama_barang}</td>
            <td>${product.satuan}</td>
            <td class="text-center">${jumlah}</td>
            <td class="text-right">${formatRupiah(hargaJual)}</td>
        `;

        tbody.appendChild(newRow);
    }

    // Show toast notification
    function showToast(type, message) {
        // Simple toast - you can replace with better library like Toastr
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        toast.innerHTML = `
            <strong>${type === 'success' ? 'Berhasil!' : 'Error!'}</strong> ${message}
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Event listeners for Add Item
    document.getElementById('btnAddItem').addEventListener('click', addItemToWO);
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addItemToWO();
        }
    });

    // Add Service (with AJAX table update - no reload)
    document.getElementById('btnAddService').addEventListener('click', function() {
        const serviceSelect = document.getElementById('serviceSelect');
        const serviceId = serviceSelect.value;
        const jumlah = parseInt(document.getElementById('serviceJumlah').value) || 1;

        if (!serviceId) {
            alert('Pilih jasa terlebih dahulu!');
            return;
        }

        // Get service data from selected option
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const serviceName = selectedOption.getAttribute('data-name');
        const servicePrice = parseInt(selectedOption.getAttribute('data-price'));

        fetch(baseUrl + 'sale/addServiceToWO', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    stock_out_id: workOrderId,
                    service_id: serviceId,
                    jumlah: jumlah
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Add new row to services table without reload
                    addServiceRowToTable(res.data.service_id || serviceId, serviceName, jumlah, servicePrice);

                    // Reset form
                    serviceSelect.value = '';
                    document.getElementById('serviceJumlah').value = '1';

                    // Recalculate preview
                    calculatePreview();

                    // Show success message
                    showToast('success', 'Jasa berhasil ditambahkan!');
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(() => alert('Gagal menambahkan jasa!'));
    });

    // Add row to services table
    function addServiceRowToTable(serviceId, serviceName, jumlah, hargaJasa) {
        const tbody = document.querySelector('#servicesTable tbody');

        // Remove "no data" row if exists
        const noServiceRow = document.getElementById('noServiceRow');
        if (noServiceRow) {
            noServiceRow.remove();
        }

        // Get current row count
        const rowCount = tbody.querySelectorAll('tr').length + 1;

        // Create new row
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td>${serviceName}</td>
            <td class="text-center">${jumlah}</td>
            <td class="text-right">${formatRupiah(hargaJasa)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-danger" onclick="deleteService(${serviceId})">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </td>
        `;

        tbody.appendChild(newRow);
    }

    // Update Tax Calculation
    document.getElementById('btnUpdateTax').addEventListener('click', calculatePreview);
    document.getElementById('ppnPersen').addEventListener('change', calculatePreview);
    document.getElementById('pphPersen').addEventListener('change', calculatePreview);

    // Finalize Work Order
    document.getElementById('btnFinalize').addEventListener('click', function() {
        if (!confirm('Finalisasi Work Order? Status akan berubah menjadi Selesai dan tidak bisa diubah lagi.')) {
            return;
        }

        const ppnPersen = parseFloat(document.getElementById('ppnPersen').value) || 11;
        const pphPersen = parseFloat(document.getElementById('pphPersen').value) || 2;

        fetch(baseUrl + 'sale/finalizeWorkOrder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    stock_out_id: workOrderId,
                    ppn_persen: ppnPersen,
                    pph_persen: pphPersen
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    alert('Work Order berhasil diselesaikan!\nGrand Total: ' + formatRupiah(res.data.grand_total));
                    window.location.href = baseUrl + 'invoice';
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(() => alert('Gagal finalisasi Work Order!'));
    });

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculatePreview();
        document.getElementById('barcodeInput').focus();
    });
</script>

<?= $this->endSection() ?>