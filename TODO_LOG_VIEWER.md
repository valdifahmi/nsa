# TODO List - Log Viewer (Admin Only)

## 1. AdminFilter

- [x] app/Filters/AdminFilter.php sudah ada
  - [x] Check if user is logged in
  - [x] Check if user role is 'admin'
  - [x] Redirect to home if not admin

## 2. Register AdminFilter

- [x] app/Config/Filters.php
  - [x] AdminFilter sudah terdaftar (digunakan untuk /people dan /logs)

## 3. LogController

- [x] app/Controllers/LogController.php
  - [x] Method index()
  - [x] JOIN tb_logs dengan tb_users
  - [x] Select: l.\*, u.username, u.nama_lengkap
  - [x] Order by created_at DESC
  - [x] Pass data ke view

## 4. Log View

- [x] app/Views/Log/index.php
  - [x] Extends Layout/template
  - [x] Table dengan kolom: ID, User, Action, Module, Record ID, Message, Timestamp
  - [x] Badge warna untuk Action:
    - [x] CREATE = badge-success (hijau)
    - [x] UPDATE = badge-info (biru)
    - [x] DELETE = badge-danger (merah)
    - [x] LOGIN = badge-primary (biru tua)
    - [x] LOGOUT = badge-warning (kuning)
    - [x] ERROR = badge-danger (merah)
  - [x] Format timestamp: dd MMM YYYY HH:mm:ss
  - [x] Handle null user_id (System)
  - [x] Handle null record_id (-)

## 5. Routes

- [x] app/Config/Routes.php
  - [x] Route group 'logs' dengan filter 'admin'
  - [x] GET /logs -> LogController::index

## 6. Sidebar Menu

- [x] app/Views/Layout/template.php
  - [x] Add "System Logs" link
  - [x] Only visible for admin: <?php if (session()->get('user')['role'] === 'admin'): ?>
  - [x] Icon: book icon
  - [x] Link to /logs

## 7. Testing

- [ ] Login sebagai admin
- [ ] Akses /logs
- [ ] Verify tabel log tampil dengan data
- [ ] Verify badge warna sesuai action
- [ ] Verify JOIN dengan users berhasil (nama user tampil)
- [ ] Verify timestamp format benar
- [ ] Login sebagai staff
- [ ] Akses /logs (should redirect to home with error)
- [ ] Verify "System Logs" menu tidak tampil untuk staff
