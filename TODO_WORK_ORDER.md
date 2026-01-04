# WORK ORDER & DYNAMIC TAXATION IMPLEMENTATION

## KONTEKS BISNIS

### Tipe Transaksi:

1. **Beli Putus**: Transaksi selesai seketika (seperti sekarang)
2. **Workshop**: Transaksi bisa dicicil, barang & jasa ditambah bertahap

### Status Work Order:

- **Proses**: Admin bisa menambah barang & jasa berkali-kali
- **Selesai**: Transaksi final, tidak bisa diubah

### Kalkulasi Pajak Dinamis:

```
Total_Barang_Net = Total_Barang + (Total_Barang × ppn_persen/100)
Total_Jasa_Net = Total_Jasa - (Total_Jasa × pph_persen/100)
Grand_Total = Total_Barang_Net + Total_Jasa_Net
```

## DATABASE SCHEMA

### tb_stock_out (Updated):

- tipe_transaksi: ENUM('Beli Putus', 'Workshop') DEFAULT 'Beli Putus'
- status_work_order: ENUM('Proses', 'Selesai') DEFAULT 'Selesai'
- ppn_persen: INT(11) DEFAULT 11
- pph_persen: INT(11) DEFAULT 2
- total_barang: BIGINT(20) DEFAULT 0
- total_jasa: BIGINT(20) DEFAULT 0
- total_ppn: BIGINT(20) DEFAULT 0
- total_pph: BIGINT(20) DEFAULT 0
- grand_total: BIGINT(20) DEFAULT 0
- status_pembayaran: ENUM('Belum Lunas', 'Lunas') DEFAULT 'Belum Lunas'

### tb_services (New):

- id: INT(11) AUTO_INCREMENT PRIMARY KEY
- nama_jasa: VARCHAR(255) NOT NULL
- harga_standar: BIGINT(20) DEFAULT 0
- created_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### tb_stock_out_services (New):

- id: INT(11) AUTO_INCREMENT PRIMARY KEY
- stock_out_id: INT(11) NOT NULL (FK to tb_stock_out)
- service_id: INT(11) NULL (FK to tb_services)
- nama_jasa: VARCHAR(255) NOT NULL
- harga_jasa: BIGINT(20) NOT NULL
- jumlah: INT(11) DEFAULT 1

## IMPLEMENTATION CHECKLIST

### Phase 1: Models ✅

- [x] Update StockOutModel - add new allowedFields
- [x] Create ServiceModel
- [x] Create StockOutServiceModel

### Phase 2: Controller Methods ✅

- [x] Update SaleController::store() - support tipe_transaksi
- [x] Create SaleController::updateWO() - display update WO page
- [x] Create SaleController::addItemToWO() - add item to existing WO
- [x] Create SaleController::addServiceToWO() - add service to WO
- [x] Create SaleController::finalizeWorkOrder() - calculate & finalize
- [x] Create SaleController::getWorkOrderDetail() - get WO data for update page (AJAX)

### Phase 3: Routes ✅

- [x] GET /sale/updateWO/(:num)
- [x] GET /sale/getWorkOrderDetail/(:num)
- [x] POST /sale/addItemToWO
- [x] POST /sale/addServiceToWO
- [x] POST /sale/finalizeWorkOrder

### Phase 4: Frontend Views ✅

- [x] Create Sale/update_wo.php - WO update page with:
  - Display existing items table
  - Barcode scanner for adding items
  - Service selection dropdown
  - Tax settings (editable PPN & PPh)
  - Live preview Grand Total
  - Finalize button
- [x] Create Sale/list.php - Transaction list page with "Update WO" button for status Proses
- [x] Add SaleController::listTransactions() method
- [x] Add route GET /sale/list

### Phase 5: Invoice Update ✅

- [x] Update InvoiceController::getDetail() - include services
- [x] Update InvoiceController::generatePDF() - include services
- [x] Update Invoice/print_pdf.php - separate tables for items & services, show tax breakdown, compact header (logo right, company left)

## TAX CALCULATION RULES

### IMPORTANT: NO HARDCODING!

- Always get ppn_persen from tb_stock_out.ppn_persen
- Always get pph_persen from tb_stock_out.pph_persen
- Never use fixed 11% or 2% in code

### Calculation Steps:

1. Calculate total_barang = SUM(jumlah × harga_jual_satuan) from tb_stock_out_items
2. Calculate total_jasa = SUM(jumlah × harga_jasa) from tb_stock_out_services
3. Calculate total_ppn = total_barang × (ppn_persen / 100)
4. Calculate total_pph = total_jasa × (pph_persen / 100)
5. Calculate grand_total = (total_barang + total_ppn) + (total_jasa - total_pph)

## UI/UX REQUIREMENTS

### Sale List Page:

- Show "Update WO" button only for tipe_transaksi='Workshop' AND status_work_order='Proses'
- Show status badge (Proses/Selesai)
- Show tipe transaksi badge (Beli Putus/Workshop)

### Update WO Page:

- Header: Show nomor_transaksi, client name, status
- Section 1: Existing Items Table (read-only)
- Section 2: Add New Item (barcode scanner + AJAX)
- Section 3: Services List (with add/remove)
- Section 4: Current Totals (auto-calculate)
- Button: "Finalize & Generate Invoice" (changes status to Selesai)

### Invoice PDF:

- Table 1: Spare Parts (from tb_stock_out_items)
- Table 2: Services (from tb_stock_out_services)
- Summary:
  - Subtotal Spare Parts: Rp xxx
  - PPN (11%): Rp xxx
  - Total Spare Parts: Rp xxx
  - Subtotal Jasa: Rp xxx
  - PPh 23 (2%): (Rp xxx)
  - Total Jasa: Rp xxx
  - **Grand Total: Rp xxx**

## TESTING CHECKLIST

### Scenario 1: Beli Putus (Normal Flow)

- [ ] Create transaction with tipe='Beli Putus'
- [ ] Verify status_work_order='Selesai'
- [ ] Verify no "Update WO" button appears
- [ ] Verify invoice generated correctly

### Scenario 2: Workshop - Add Items Gradually

- [ ] Create transaction with tipe='Workshop'
- [ ] Verify status_work_order='Proses'
- [ ] Click "Update WO" button
- [ ] Add 2-3 items via barcode scanner
- [ ] Verify stock reduced correctly
- [ ] Verify items appear in table

### Scenario 3: Workshop - Add Services

- [ ] Open WO in Proses status
- [ ] Add 2 services
- [ ] Verify services saved correctly
- [ ] Verify totals calculated

### Scenario 4: Workshop - Finalize

- [ ] Click "Finalize & Generate Invoice"
- [ ] Verify status changed to 'Selesai'
- [ ] Verify all totals calculated with dynamic tax
- [ ] Verify invoice shows separate tables
- [ ] Verify tax breakdown correct

### Scenario 5: Dynamic Tax Rates

- [ ] Create WO with ppn_persen=12, pph_persen=3
- [ ] Add items and services
- [ ] Finalize
- [ ] Verify calculations use 12% and 3%, not hardcoded values

## NOTES

- Single admin handles both gudang & workshop
- Admin must be able to easily reopen "Proses" transactions
- Barcode scanner must work seamlessly for adding items
- All monetary values in BIGINT (no decimals)
- Format currency as Rupiah in display
- Use database transactions for data integrity
- Log all activities for audit trail
