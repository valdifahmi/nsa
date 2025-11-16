# DataTables Usage Guide - Bootstrap 4 Integration

## ✅ Instalasi Selesai!

Semua file DataTables sudah ter-load di `app/Views/Layout/template.php` dengan urutan yang benar:

### CSS (di `<head>`):

1. ✅ dataTables.bootstrap4.min.css
2. ✅ buttons.bootstrap4.min.css

### JS (sebelum `</body>`):

1. ✅ jquery.dataTables.min.js
2. ✅ dataTables.bootstrap4.min.js
3. ✅ jszip.min.js (untuk Excel)
4. ✅ pdfmake.min.js (untuk PDF)
5. ✅ vfs_fonts.js (untuk PDF fonts)
6. ✅ dataTables.buttons.min.js
7. ✅ buttons.bootstrap4.min.js
8. ✅ buttons.html5.min.js
9. ✅ buttons.print.min.js
10. ✅ buttons.colVis.min.js

---

## 📝 Cara Menggunakan DataTables

### 1. Basic DataTables (Tanpa Buttons)

```html
<!-- Di view Anda, misalnya app/Views/Product/index.php -->
<table id="productTable" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>Kode Barang</th>
      <th>Nama Barang</th>
      <th>Kategori</th>
      <th>Stok</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <!-- Data akan di-load via AJAX atau server-side -->
  </tbody>
</table>
```

```javascript
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productTable').DataTables({
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' // Bahasa Indonesia
        }
    });
});
</script>
<?= $this->endSection() ?>
```

---

### 2. DataTables dengan Buttons (Copy, Excel, PDF, Print)

```javascript
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Data Products',
                exportOptions: {
                    columns: ':visible:not(:last-child)' // Exclude last column (Actions)
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Data Products',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-info btn-sm',
                title: 'Data Products',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Column Visibility',
                className: 'btn btn-warning btn-sm'
            }
        ],
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
```

---

### 3. DataTables dengan AJAX (Recommended untuk data banyak)

```javascript
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print', 'colvis'
        ],
        processing: true,
        serverSide: false, // Set true jika data sangat banyak
        ajax: {
            url: '<?= base_url('product/fetchAll') ?>',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'kode_barang' },
            { data: 'nama_barang' },
            { data: 'nama_kategori' },
            { data: 'stok_saat_ini' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
```

---

### 4. Custom Button Styling (Bootstrap 4)

```javascript
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: {
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="ri-file-copy-line"></i> Copy',
                    className: 'btn btn-sm btn-secondary mr-1'
                },
                {
                    extend: 'excel',
                    text: '<i class="ri-file-excel-line"></i> Excel',
                    className: 'btn btn-sm btn-success mr-1',
                    title: 'Data Products - ' + new Date().toLocaleDateString('id-ID')
                },
                {
                    extend: 'pdf',
                    text: '<i class="ri-file-pdf-line"></i> PDF',
                    className: 'btn btn-sm btn-danger mr-1',
                    title: 'Data Products',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="ri-printer-line"></i> Print',
                    className: 'btn btn-sm btn-info mr-1'
                }
            ],
            dom: {
                button: {
                    className: 'btn'
                }
            }
        },
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
```

---

## 🎨 Custom Styling untuk Buttons

Tambahkan CSS custom di section `scripts` atau di file CSS terpisah:

```html
<?= $this->section('scripts') ?>
<style>
  /* DataTables Buttons Custom Style */
  .dt-buttons {
    margin-bottom: 15px;
  }

  .dt-button {
    margin-right: 5px !important;
    border-radius: 4px !important;
  }

  /* Responsive buttons on mobile */
  @media (max-width: 768px) {
    .dt-buttons {
      display: flex;
      flex-wrap: wrap;
    }

    .dt-button {
      margin-bottom: 5px;
      font-size: 12px;
    }
  }
</style>
<script>
  // Your DataTables initialization here
</script>
<?= $this->endSection() ?>
```

---

## 📊 Contoh Implementasi Lengkap untuk Report

```php
<!-- app/Views/Report/stock.php -->
<?= $this->extend('Layout/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Laporan Stok Barang</h4>
                </div>
            </div>
            <div class="card-body">
                <table id="stockTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Satuan</th>
                            <th>Stok Saat Ini</th>
                            <th>Min Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($products as $product): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $product['kode_barang'] ?></td>
                            <td><?= $product['nama_barang'] ?></td>
                            <td><?= $product['nama_kategori'] ?></td>
                            <td><?= $product['nama_brand'] ?? '-' ?></td>
                            <td><?= $product['satuan'] ?></td>
                            <td><?= $product['stok_saat_ini'] ?></td>
                            <td><?= $product['min_stok'] ?></td>
                            <td>
                                <?php if ($product['stok_saat_ini'] <= $product['min_stok']): ?>
                                    <span class="badge badge-danger">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#stockTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="ri-file-excel-line"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Laporan Stok Barang - ' + new Date().toLocaleDateString('id-ID'),
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="ri-file-pdf-line"></i> Export PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Laporan Stok Barang',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line"></i> Print',
                className: 'btn btn-info btn-sm',
                title: 'Laporan Stok Barang',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        responsive: true,
        pageLength: 25,
        order: [[6, 'asc']], // Sort by stok_saat_ini ascending
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
```

---

## 🔧 Troubleshooting

### 1. Buttons tidak muncul:

- Pastikan urutan load JS sudah benar (JSZip dan pdfMake sebelum buttons.html5.min.js)
- Cek console browser untuk error
- Pastikan `dom: 'Bfrtip'` sudah di-set

### 2. Export Excel/PDF tidak berfungsi:

- Pastikan JSZip dan pdfMake ter-load dengan benar
- Cek network tab di browser developer tools

### 3. Styling tidak sesuai:

- Pastikan menggunakan versi Bootstrap 4 (dataTables.bootstrap4.min.css)
- Jangan mix dengan Bootstrap 3 atau 5

### 4. Responsive tidak berfungsi:

- Tambahkan `responsive: true` di options
- Pastikan tabel memiliki class `table`

---

## ✅ Checklist Implementasi

- [ ] Semua file CSS dan JS sudah ter-load di template
- [ ] Tabel memiliki ID unik (misalnya `id="productTable"`)
- [ ] Tabel memiliki struktur `<thead>` dan `<tbody>`
- [ ] DataTables di-inisialisasi di section `scripts`
- [ ] Buttons di-konfigurasi sesuai kebutuhan
- [ ] Test export Excel, PDF, dan Print
- [ ] Test responsive di mobile

---

**DataTables sudah siap digunakan di semua halaman Anda!** 🎉
