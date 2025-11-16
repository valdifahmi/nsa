# TODO List - Barang Keluar (Sale) dengan Keranjang

## 1. Create SaleController

- [x] app/Controllers/SaleController.php dengan methods:
  - [x] index() - Load view Sale/index.php
  - [x] store() - AJAX endpoint untuk simpan transaksi (dengan Database Transaction)
  - [x] generateTransactionNumber() - Format: SO-YYYYMMDD-XXXX

## 2. Create View Sale/index.php

- [x] Form Header: Tanggal, Penerima, Catatan
- [x] Input Scan Barcode dengan Autocomplete
- [x] Tabel Keranjang
- [x] Tombol Checkout
- [x] JavaScript/jQuery untuk:
  - [x] Autocomplete dengan gambar
  - [x] Event listener Enter di barcode scanner
  - [x] AJAX cari produk by kode
  - [x] **VALIDASI STOK: Cek stok_saat_ini sebelum tambah ke cart**
  - [x] Prompt jumlah dengan validasi max = stok tersedia
  - [x] Tambah item ke keranjang
  - [x] Hapus item dari keranjang
  - [x] Submit checkout via AJAX

## 3. Update Routes

- [x] app/Config/Routes.php - Tambah routes untuk SaleController dengan filter 'auth'

## 4. Update Models

- [x] StockOutModel - Set useAutoIncrement, returnType, hapus created_at dari allowedFields
- [x] StockOutItemModel - Set useAutoIncrement, returnType

## 5. Database Transaction Logic (PENTING!)

- [x] **Validasi Stok di Backend:** Loop items, cek jumlah <= stok_saat_ini
- [x] Simpan header ke tb_stock_out (get stock_out_id)
- [x] Loop items, simpan ke tb_stock_out_items
- [x] Update stok_saat_ini di tb_products (stok - jumlah)
- [x] transBegin() dan transCommit()
- [x] Rollback jika validasi stok gagal

## 6. Generate Nomor Transaksi

- [x] Format: SO-YYYYMMDD-XXXX (contoh: SO-20250116-0001)

## 7. Validasi Stok (Double Check)

- [x] Frontend: Cek stok saat tambah ke cart
- [x] Backend: Cek stok sebelum commit transaction
- [x] Error message jika stok tidak cukup

## 8. Testing

- [ ] Test autocomplete
- [ ] Test scan produk dengan stok cukup
- [ ] Test scan produk dengan stok tidak cukup (harus error)
- [ ] Test tambah item melebihi stok (harus error)
- [ ] Test checkout sukses
- [ ] Test stok berkurang setelah checkout
- [ ] Test database transaction rollback
