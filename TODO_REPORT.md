# TODO List - Laporan (Reports) dengan Filter Tanggal

## 1. Create ReportController

- [x] app/Controllers/ReportController.php dengan methods:
  - [x] purchaseReport() - Load view Report/purchase.php
  - [x] fetchPurchaseReport() - AJAX get purchase data (JOIN tb_stock_in + tb_users)
  - [x] purchaseItemReport() - Load view Report/purchase_item.php
  - [x] fetchPurchaseItemReport() - AJAX get purchase items (JOIN tb_stock_in_items + tb_stock_in + tb_products)
  - [x] saleReport() - Load view Report/sale.php
  - [x] fetchSaleReport() - AJAX get sale data (JOIN tb_stock_out + tb_users)
  - [x] saleItemReport() - Load view Report/sale_item.php
  - [x] fetchSaleItemReport() - AJAX get sale items (JOIN tb_stock_out_items + tb_stock_out + tb_products)
  - [x] stockReport() - Load view Report/stock.php
  - [x] fetchStockReport() - AJAX get stock data (JOIN tb_products + tb_categories + tb_brands)

## 2. Create Views

- [x] app/Views/Report/purchase.php - Laporan Barang Masuk (Header)
- [x] app/Views/Report/purchase_item.php - Laporan Barang Masuk (Detail Items)
- [x] app/Views/Report/sale.php - Laporan Barang Keluar (Header)
- [x] app/Views/Report/sale_item.php - Laporan Barang Keluar (Detail Items)
- [x] app/Views/Report/stock.php - Laporan Stok Barang

## 3. Filter Tanggal

- [x] Input Start Date & End Date
- [x] Button "Tampilkan Laporan"
- [x] Default: Bulan ini (tanggal 1 s/d hari ini)
- [x] AJAX load data berdasarkan filter

## 4. Laporan Purchase (Header)

- [x] Kolom: No, Nomor Transaksi, Tanggal, User, Supplier, Catatan
- [x] Filter by date range
- [x] JOIN tb_stock_in + tb_users

## 5. Laporan Purchase Items (Detail)

- [x] Kolom: No, Nomor Transaksi, Tanggal, Kode Barang, Nama Barang, Jumlah, Satuan
- [x] Filter by date range
- [x] JOIN tb_stock_in_items + tb_stock_in + tb_products

## 6. Laporan Sale (Header)

- [x] Kolom: No, Nomor Transaksi, Tanggal, User, Penerima, Catatan
- [x] Filter by date range
- [x] JOIN tb_stock_out + tb_users

## 7. Laporan Sale Items (Detail)

- [x] Kolom: No, Nomor Transaksi, Tanggal, Kode Barang, Nama Barang, Jumlah, Satuan
- [x] Filter by date range
- [x] JOIN tb_stock_out_items + tb_stock_out + tb_products

## 8. Laporan Stock

- [x] Kolom: No, Kode Barang, Nama Barang, Kategori, Brand, Satuan, Stok Saat Ini, Min Stok
- [x] Highlight merah jika stok_saat_ini <= min_stok
- [x] JOIN tb_products + tb_categories + tb_brands
- [x] No date filter (auto-load)

## 9. Update Routes

- [x] app/Config/Routes.php - Tambah routes untuk ReportController dengan filter 'auth'

## 10. Update Template Sidebar

- [x] app/Views/Layout/template.php - Tambah submenu Reports dengan 5 link laporan

## 11. Print Functionality

- [x] Button Print di setiap laporan (window.print())

## 11. Testing

- [ ] Test filter tanggal purchase report
- [ ] Test filter tanggal purchase item report
- [ ] Test filter tanggal sale report
- [ ] Test filter tanggal sale item report
- [ ] Test stock report (no filter)
- [ ] Test highlight stok rendah (merah)
