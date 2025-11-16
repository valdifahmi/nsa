# TODO List - CRUD Category dengan Modal + AJAX

## 1. Create CategoryController

- [x] app/Controllers/CategoryController.php dengan methods:
  - [x] index() - Load view Category/index.php
  - [x] fetchAll() - AJAX endpoint untuk get all categories (JSON)
  - [x] store() - AJAX endpoint untuk create category (dengan upload image)
  - [x] edit($id) - AJAX endpoint untuk get single category by id (JSON)
  - [x] update($id) - AJAX endpoint untuk update category (dengan upload image)
  - [x] delete($id) - AJAX endpoint untuk delete category (hapus file image juga)

## 2. Create View Category/index.php

- [x] Tabel untuk menampilkan list categories
- [x] Modal untuk Tambah Category (form: nama_kategori, deskripsi, image)
- [x] Modal untuk Edit Category (form: nama_kategori, deskripsi, image)
- [x] JavaScript/jQuery untuk:
  - [x] Load data via AJAX (fetchAll)
  - [x] Submit form Tambah via AJAX (store)
  - [x] Open modal Edit dan load data via AJAX (edit)
  - [x] Submit form Edit via AJAX (update)
  - [x] Delete category via AJAX (delete)

## 3. Update Routes

- [x] app/Config/Routes.php - Tambah routes untuk CategoryController dengan filter 'auth'

## 4. Create Upload Directory

- [x] writable/uploads/categories/ untuk menyimpan image kategori

## 5. Validation & File Upload Logic

- [x] Validasi: nama_kategori required, image optional (jpg, jpeg, png, max 2MB)
- [x] Upload handling: rename file, move to uploads/categories/
- [x] Delete old image saat update atau delete

## 6. Testing

- [ ] Test create category dengan image
- [ ] Test create category tanpa image (gunakan default.png)
- [ ] Test edit category dengan ganti image
- [ ] Test edit category tanpa ganti image
- [ ] Test delete category
- [ ] Test validation errors

## Summary

✅ CategoryController created with all CRUD methods
✅ Category/index.php view with modals and AJAX functionality
✅ Routes configured with auth filter
✅ Upload directory created
✅ Validation and file upload logic implemented

Ready for testing!
