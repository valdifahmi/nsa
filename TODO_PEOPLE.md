# TODO List - CRUD User (People) dengan Modal + AJAX

## 1. Create PeopleController

- [ ] app/Controllers/PeopleController.php dengan methods:
  - [ ] index() - Load view People/index.php
  - [ ] fetchAll() - AJAX get all users (exclude password)
  - [ ] store() - AJAX create user (hash password)
  - [ ] edit($id) - AJAX get user by id (exclude password)
  - [ ] update($id) - AJAX update user (hash password jika diisi)
  - [ ] delete($id) - AJAX delete user

## 2. Create View People/index.php

- [ ] Tabel users dengan DataTables
- [ ] Modal Tambah User (username, nama_lengkap, role, password)
- [ ] Modal Edit User (username, nama_lengkap, role, password optional)
- [ ] Tombol Tambah, Edit, Delete
- [ ] JavaScript/jQuery untuk:
  - [ ] Load data via AJAX (fetchAll)
  - [ ] Submit form tambah via AJAX
  - [ ] Load data edit via AJAX
  - [ ] Submit form edit via AJAX (password optional)
  - [ ] Delete via AJAX dengan konfirmasi

## 3. Update Routes

- [ ] app/Config/Routes.php - Tambah routes untuk PeopleController dengan filter 'auth'

## 4. Create AdminFilter (PENTING!)

- [ ] app/Filters/AdminFilter.php - Cek session role = 'admin'
- [ ] Register di app/Config/Filters.php
- [ ] Apply ke routes /people/\*

## 5. Password Logic

- [ ] Store: Hash password dengan password_hash()
- [ ] Update: Hash password HANYA jika field password diisi
- [ ] Update: Jika password kosong, jangan update kolom password

## 6. Validation Rules

- [ ] Store: username (required, is_unique), nama_lengkap (required), role (required, in_list[admin,staff]), password (required, min_length[6])
- [ ] Update: username (required, is_unique except current id), nama_lengkap (required), role (required, in_list[admin,staff]), password (permit_empty, min_length[6])

## 7. Security

- [ ] Exclude password dari fetchAll() dan edit()
- [ ] Only admin can access /people
- [ ] Prevent delete self (optional)

## 8. Testing

- [ ] Test akses /people sebagai admin (berhasil)
- [ ] Test akses /people sebagai staff (ditolak)
- [ ] Test tambah user baru
- [ ] Test edit user tanpa ubah password
- [ ] Test edit user dengan ubah password
- [ ] Test delete user
- [ ] Test validasi username duplicate
