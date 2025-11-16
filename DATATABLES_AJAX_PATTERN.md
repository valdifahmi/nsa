# DataTables AJAX Reload Pattern

## 📋 Pattern untuk Implementasi DataTables dengan AJAX Reload

Pattern ini sudah diimplementasikan di **Product/index.php** dan bisa digunakan sebagai template untuk halaman lain (Category, Brand, People, dll).

---

## 🔄 Alur Kerja (Workflow)

```
1. AJAX fetchAll() dipanggil
2. Destroy DataTable instance yang ada (jika ada)
3. Clear tbody
4. Build HTML string dengan forEach loop
5. Insert HTML ke tbody
6. Initialize DataTables dengan Buttons
```

---

## 💻 Kode Lengkap Pattern

```javascript
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
                        // Build row HTML
                        html += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + product.kode_barang + '</td>' +
                            // ... more columns ...
                            '</tr>';
                    });

                    // Step 4: Insert HTML into tbody
                    tbody.html(html);
                }

                // Step 5: Initialize DataTables with Buttons
                $('#productTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="ri-file-copy-line"></i> Copy',
                            className: 'btn btn-secondary btn-sm',
                            exportOptions: {
                                columns: ':visible:not(:last-child)' // Exclude Actions column
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="ri-file-excel-line"></i> Excel',
                            className: 'btn btn-success btn-sm',
                            title: 'Product List - ' + new Date().toLocaleDateString('id-ID'),
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="ri-file-pdf-line"></i> PDF',
                            className: 'btn btn-danger btn-sm',
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
                            className: 'btn btn-info btn-sm',
                            title: 'Product List',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        }
                    ],
                    pageLength: 25,
                    responsive: true,
                    destroy: true, // Important for AJAX reload
                    order: [[1, 'asc']], // Sort by first data column
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
```

---

## 🎯 Key Points (Poin Penting)

### 1. **Destroy Before Rebuild**

```javascript
if ($.fn.DataTable.isDataTable("#productTable")) {
  $("#productTable").DataTable().destroy();
}
```

- **Wajib** dilakukan sebelum rebuild tbody
- Mencegah error "DataTable already initialized"
- Membersihkan event handlers dan DOM modifications

### 2. **Build HTML String (Bukan Append Per Row)**

```javascript
var html = "";
$.each(response.data, function (index, item) {
  html += "<tr>...</tr>";
});
tbody.html(html);
```

- **Lebih cepat** daripada `tbody.append()` per row
- Mengurangi DOM manipulation
- Best practice untuk performa

### 3. **Initialize DataTables SETELAH HTML Ready**

```javascript
tbody.html(html);  // Insert HTML first
$('#productTable').DataTable({...});  // Then initialize
```

- DataTables harus di-initialize setelah tbody terisi
- Jangan initialize pada tbody kosong

### 4. **destroy: true Option**

```javascript
$("#productTable").DataTable({
  destroy: true, // Important!
  // ... other options
});
```

- Backup safety untuk destroy
- Memastikan re-initialization berjalan lancar

### 5. **exportOptions untuk Exclude Columns**

```javascript
exportOptions: {
  columns: ":visible:not(:last-child)"; // Exclude Actions column
}
```

- Exclude kolom Actions dari export
- Hanya export data yang relevan

### 6. **drawCallback untuk Re-initialize Tooltips**

```javascript
drawCallback: function() {
    $('[title]').tooltip();
}
```

- Tooltips perlu di-reinitialize setelah pagination/sort
- Dipanggil setiap kali tabel di-redraw

---

## 📝 Template untuk Halaman Lain

### Category List

```javascript
function loadCategories() {
    $.ajax({
        url: '<?= base_url('category/fetchAll') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Destroy existing DataTable
                if ($.fn.DataTable.isDataTable('#categoryTable')) {
                    $('#categoryTable').DataTable().destroy();
                }

                var tbody = $('#categoryTable tbody');
                tbody.empty();

                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center">No categories found</td></tr>');
                } else {
                    var html = '';
                    $.each(response.data, function(index, category) {
                        html += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + category.nama_kategori + '</td>' +
                            '<td>' + (category.deskripsi || '-') + '</td>' +
                            '<td class="text-center">' +
                            '<img src="<?= base_url('uploads/categories/') ?>' + category.image + '" class="img-thumbnail" style="width: 50px;">' +
                            '</td>' +
                            '<td class="text-center">' +
                            '<button class="btn btn-sm btn-warning btn-edit" data-id="' + category.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                            '<button class="btn btn-sm btn-danger btn-delete" data-id="' + category.id + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                            '</td>' +
                            '</tr>';
                    });
                    tbody.html(html);
                }

                // Initialize DataTables
                $('#categoryTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'excel', 'pdf', 'print'],
                    pageLength: 25,
                    responsive: true,
                    destroy: true,
                    order: [[1, 'asc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    drawCallback: function() {
                        $('[title]').tooltip();
                    }
                });
            }
        }
    });
}
```

### Brand List

```javascript
function loadBrands() {
    $.ajax({
        url: '<?= base_url('brand/fetchAll') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                if ($.fn.DataTable.isDataTable('#brandTable')) {
                    $('#brandTable').DataTable().destroy();
                }

                var tbody = $('#brandTable tbody');
                tbody.empty();

                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="3" class="text-center">No brands found</td></tr>');
                } else {
                    var html = '';
                    $.each(response.data, function(index, brand) {
                        html += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + brand.nama_brand + '</td>' +
                            '<td class="text-center">' +
                            '<button class="btn btn-sm btn-warning btn-edit" data-id="' + brand.id + '" title="Edit"><i class="ri-edit-line"></i></button> ' +
                            '<button class="btn btn-sm btn-danger btn-delete" data-id="' + brand.id + '" title="Delete"><i class="ri-delete-bin-line"></i></button>' +
                            '</td>' +
                            '</tr>';
                    });
                    tbody.html(html);
                }

                $('#brandTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'excel', 'pdf', 'print'],
                    pageLength: 25,
                    responsive: true,
                    destroy: true,
                    order: [[1, 'asc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                    },
                    drawCallback: function() {
                        $('[title]').tooltip();
                    }
                });
            }
        }
    });
}
```

---

## ⚙️ Konfigurasi DataTables

### Opsi Umum

```javascript
{
    dom: 'Bfrtip',              // Layout: Buttons, filter, table, info, pagination
    pageLength: 25,             // 25 rows per page (sesuai requirement)
    responsive: true,           // Mobile-friendly
    destroy: true,              // Allow re-initialization
    order: [[1, 'asc']],       // Default sort by column 1 ascending
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'  // Bahasa Indonesia
    }
}
```

### Custom DOM Layout

```javascript
dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
  '<"row"<"col-sm-12"B>>' +
  '<"row"<"col-sm-12"tr>>' +
  '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>';
```

- `l` = length changing (show entries)
- `f` = filtering input (search box)
- `B` = buttons
- `t` = table
- `r` = processing display
- `i` = information summary
- `p` = pagination

---

## 🐛 Troubleshooting

### Problem 1: "DataTable already initialized"

**Solution:** Pastikan destroy dipanggil sebelum rebuild

```javascript
if ($.fn.DataTable.isDataTable("#myTable")) {
  $("#myTable").DataTable().destroy();
}
```

### Problem 2: Buttons tidak muncul setelah reload

**Solution:** Pastikan initialize DataTables dipanggil setelah tbody.html()

```javascript
tbody.html(html);  // First
$('#myTable').DataTable({...});  // Then
```

### Problem 3: Tooltips tidak berfungsi setelah pagination

**Solution:** Gunakan drawCallback

```javascript
drawCallback: function() {
    $('[title]').tooltip();
}
```

### Problem 4: Export include kolom Actions

**Solution:** Gunakan exportOptions

```javascript
exportOptions: {
  columns: ":visible:not(:last-child)";
}
```

---

## ✅ Checklist Implementasi

- [ ] Tabel memiliki ID unik (e.g., `id="productTable"`)
- [ ] AJAX endpoint mengembalikan JSON dengan format: `{status: 'success', data: [...]}`
- [ ] Destroy DataTable sebelum rebuild tbody
- [ ] Build HTML string (bukan append per row)
- [ ] Insert HTML ke tbody dengan `.html()`
- [ ] Initialize DataTables SETELAH tbody terisi
- [ ] Set `destroy: true` di options
- [ ] Set `pageLength: 25`
- [ ] Exclude kolom Actions dari export
- [ ] Re-initialize tooltips di drawCallback
- [ ] Test: Create, Update, Delete → tabel auto-refresh
- [ ] Test: Export Excel, PDF, Print
- [ ] Test: Pagination, Sorting, Search

---

## 📚 Resources

- DataTables Documentation: https://datatables.net/
- Buttons Extension: https://datatables.net/extensions/buttons/
- Bootstrap 4 Integration: https://datatables.net/examples/styling/bootstrap4.html

---

**Pattern ini sudah tested dan working di Product List!** ✅
