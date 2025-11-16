# TODO List - CRUD Product dengan Modal + AJAX

## 1. Create ProductController

- [x] app/Controllers/ProductController.php dengan methods:
  - [x] index() - Load view Product/index.php
  - [x] fetchAll() - AJAX endpoint untuk get all products dengan JOIN categories & brands (JSON)
  - [x] fetchDropdowns() - AJAX endpoint untuk get categories & brands untuk dropdown (JSON)
  - [x] store() - AJAX endpoint untuk create product (dengan upload image)
  - [x] edit($id) - AJAX endpoint untuk get single product by id (JSON)
  - [x] update($id) - AJAX endpoint untuk update product (dengan upload image)
  - [x] delete($id) - AJAX endpoint untuk delete product (hapus file image juga)

## 2. Create View Product/index.php

- [x] Tabel untuk menampilkan list products (dengan nama category & brand)
- [x] Modal untuk Tambah Product (form: kode_barang, nama_barang, category_id, brand_id, deskripsi, image, satuan, stok_saat_ini, min_stok)
- [x] Modal untuk Edit Product (form yang sama)
- [x] JavaScript/jQuery untuk:
  - [x] Load data via AJAX (fetchAll)
  - [x] Load dropdowns via AJAX (fetchDropdowns)
  - [x] Submit form Tambah via AJAX (store)
  - [x] Open modal Edit dan load data via AJAX (edit)
  - [x] Submit form Edit via AJAX (update)
  - [x] Delete product via AJAX (delete)

## 3. Update Routes

- [x] app/Config/Routes.php - Tambah routes untuk ProductController dengan filter 'auth'

## 4. Create Upload Directory

- [x] public/uploads/products/ untuk menyimpan image produk

## 5. Validation & File Upload Logic

- [x] Validasi: kode_barang unique, nama_barang required, category_id required, satuan required, stok & min_stok numeric
- [x] Upload handling: rename file, move to uploads/products/
- [x] Delete old image saat update atau delete

## 6. Testing

- [ ] Test create product dengan image
- [ ] Test create product tanpa image (gunakan default.png)
- [ ] Test edit product dengan ganti image
- [ ] Test edit product tanpa ganti image
- [ ] Test delete product
- [ ] Test validation errors
- [ ] Test dropdown categories & brands
