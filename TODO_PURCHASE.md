# TODO List - Barang Masuk (Purchases) dengan Keranjang

## 1. Update ProductController

- [x] Tambah method findProductByCode($code) - AJAX endpoint untuk cari produk by kode_barang (JSON)

## 2. Create PurchaseController

- [x] app/Controllers/PurchaseController.php dengan methods:
  - [x] index() - Load view Purchase/index.php
  - [x] store() - AJAX endpoint untuk simpan transaksi (dengan Database Transaction)

## 3. Create View Purchase/index.php

- [x] Form Header: Tanggal, Supplier, Catatan
- [x] Input Scan Barcode: <input type="text" id="barcode_scanner">
- [x] Tabel Keranjang: Untuk menampung item yang di-scan
- [x] Tombol Checkout: Untuk simpan transaksi
- [x] JavaScript/jQuery untuk:
  - [x] Event listener saat Enter di barcode_scanner
  - [x] AJAX cari produk by kode (findProductByCode)
  - [x] Prompt jumlah jika produk ketemu
  - [x] Tambah item ke tabel keranjang
  - [x] Hapus item dari keranjang
  - [x] Submit form checkout via AJAX (store)

## 4. Update Routes

- [x] app/Config/Routes.php - Tambah routes untuk PurchaseController dengan filter 'auth'
- [x] Tambah route untuk ProductController::findProductByCode

## 5. Database Transaction Logic

- [x] Simpan header ke tb_stock_in (get stock_in_id)
- [x] Loop items, simpan ke tb_stock_in_items
- [x] Update stok_saat_ini di tb_products (stok + jumlah)
- [x] transStart() dan transComplete()

## 6. Generate Nomor Transaksi

- [x] Format: SI-YYYYMMDD-XXXX (contoh: SI-20250116-0001)

## 7. Testing

- [ ] Test scan barcode (produk ketemu)
- [ ] Test scan barcode (produk tidak ketemu)
- [ ] Test tambah multiple items ke keranjang
- [ ] Test hapus item dari keranjang
- [ ] Test checkout (simpan transaksi)
- [ ] Test update stok setelah checkout
- [ ] Test database transaction (rollback jika error)
