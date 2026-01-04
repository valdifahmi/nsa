# TODO: Supplier & Client Implementation

## ✅ Completed Tasks:

### 1. Models Created

- ✅ `app/Models/SupplierModel.php`
  - Table: `tb_suppliers`
  - Fields: `id`, `nama_supplier`, `kontak`, `alamat`
  - Methods: `getForDropdown()`, `getSupplierWithDetails()`, `isNameExists()`
- ✅ `app/Models/ClientModel.php`
  - Table: `tb_clients`
  - Fields: `id`, `nama_klien`, `kontak`, `alamat`
  - Methods: `getForDropdown()`, `getClientWithDetails()`, `isNameExists()`

### 2. Controllers Created

- ✅ `app/Controllers/SupplierController.php`
  - CRUD operations with AJAX
  - Validation & logging
  - Check usage before delete
  - Dropdown endpoint for transactions
- ✅ `app/Controllers/ClientController.php`
  - CRUD operations with AJAX
  - Validation & logging
  - Check usage before delete
  - Dropdown endpoint for transactions

### 3. Views Created

- ✅ `app/Views/Supplier/index.php`
  - DataTables integration
  - Add/Edit modals
  - Quick add function: `window.addSupplierQuick(callback)`
- ✅ `app/Views/Client/index.php`
  - DataTables integration
  - Add/Edit modals
  - Quick add function: `window.addClientQuick(callback)`

### 4. Routes Registered

- ✅ `app/Config/Routes.php`
  - Supplier routes: `/supplier/*`
  - Client routes: `/client/*`
  - All protected with `auth` filter

---

## 📋 Next Steps:

### 1. Update Sidebar Menu

- [ ] Add "Supplier" menu item in `app/Views/Layout/template.php`
- [ ] Add "Client" menu item in `app/Views/Layout/template.php`
- [ ] Group under "Master Data" section

### 2. Update Product Model

- [ ] Add fields: `harga_beli_saat_ini` (BIGINT), `harga_jual_saat_ini` (BIGINT)
- [ ] Update `$allowedFields` array

### 3. Update StockIn Model & Items

- [ ] Change `supplier` (VARCHAR) to `supplier_id` (INT FK)
- [ ] Add `harga_beli_satuan` (BIGINT) to StockInItemModel
- [ ] Update `$allowedFields` arrays

### 4. Update StockOut Model & Items

- [ ] Change `penerima` (VARCHAR) to `client_id` (INT FK)
- [ ] Add `nomor_invoice` (VARCHAR) to StockOutModel
- [ ] Add `harga_beli_satuan` (BIGINT) and `harga_jual_satuan` (BIGINT) to StockOutItemModel
- [ ] Update `$allowedFields` arrays

### 5. Update Purchase Controller

- [ ] Implement Average Price calculation
- [ ] Formula: `(Stok Lama × Harga Lama) + (Stok Baru × Harga Baru) / (Stok Lama + Stok Baru)`
- [ ] Update `tb_products.harga_beli_saat_ini` after each Stock In
- [ ] Save `harga_beli_satuan` to `tb_stock_in_items`

### 6. Update Purchase View

- [ ] Replace text input `supplier` with dropdown `supplier_id`
- [ ] Add "Quick Add Supplier" button next to dropdown
- [ ] Add `harga_beli_satuan` input for each item in cart
- [ ] Calculate and display total purchase value

### 7. Update Sale Controller

- [ ] Implement Historical Pricing
- [ ] Save `harga_beli_satuan` (from product's current average) to `tb_stock_out_items`
- [ ] Save `harga_jual_satuan` (user input) to `tb_stock_out_items`
- [ ] Generate `nomor_invoice` automatically
- [ ] Calculate profit: `(harga_jual - harga_beli) × jumlah`

### 8. Update Sale View

- [ ] Replace text input `penerima` with dropdown `client_id`
- [ ] Add "Quick Add Client" button next to dropdown
- [ ] Add `harga_jual_satuan` input for each item in cart
- [ ] Display `harga_beli_satuan` (read-only) for reference
- [ ] Calculate and display: Total Sale, Total Cost, Total Profit
- [ ] Display generated `nomor_invoice`

### 9. Update Reports

- [ ] Purchase Report: Show supplier name instead of text
- [ ] Sale Report: Show client name and invoice number
- [ ] Add Profit/Loss Report (new)

### 10. Testing

- [ ] Test Supplier CRUD
- [ ] Test Client CRUD
- [ ] Test Quick Add from Purchase page
- [ ] Test Quick Add from Sale page
- [ ] Test Average Price calculation
- [ ] Test Historical Pricing
- [ ] Test Reports with new fields

---

## 🎯 Quick Add Feature Usage:

### From Purchase Page:

```javascript
// Call this when "Add Supplier" button is clicked
window.addSupplierQuick(function (newSupplier) {
  // newSupplier = { id: 123, nama_supplier: "PT ABC" }
  // Add to dropdown and select it
  $("#supplier_id").append(
    $("<option>", {
      value: newSupplier.id,
      text: newSupplier.nama_supplier,
      selected: true,
    })
  );
});
```

### From Sale Page:

```javascript
// Call this when "Add Client" button is clicked
window.addClientQuick(function (newClient) {
  // newClient = { id: 456, nama_klien: "CV XYZ" }
  // Add to dropdown and select it
  $("#client_id").append(
    $("<option>", {
      value: newClient.id,
      text: newClient.nama_klien,
      selected: true,
    })
  );
});
```

---

## 📊 Database Schema Reference:

### tb_suppliers

```sql
CREATE TABLE tb_suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(255) NOT NULL,
    kontak VARCHAR(100),
    alamat TEXT
);
```

### tb_clients

```sql
CREATE TABLE tb_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_klien VARCHAR(255) NOT NULL,
    kontak VARCHAR(100),
    alamat TEXT
);
```

### tb_products (Updated)

```sql
ALTER TABLE tb_products
ADD COLUMN harga_beli_saat_ini BIGINT DEFAULT 0,
ADD COLUMN harga_jual_saat_ini BIGINT DEFAULT 0;
```

### tb_stock_in (Updated)

```sql
ALTER TABLE tb_stock_in
DROP COLUMN supplier,
ADD COLUMN supplier_id INT,
ADD CONSTRAINT fk_in_supp FOREIGN KEY (supplier_id) REFERENCES tb_suppliers(id);
```

### tb_stock_in_items (Updated)

```sql
ALTER TABLE tb_stock_in_items
ADD COLUMN harga_beli_satuan BIGINT;
```

### tb_stock_out (Updated)

```sql
ALTER TABLE tb_stock_out
DROP COLUMN penerima,
ADD COLUMN client_id INT,
ADD COLUMN nomor_invoice VARCHAR(100) UNIQUE,
ADD CONSTRAINT fk_out_client FOREIGN KEY (client_id) REFERENCES tb_clients(id);
```

### tb_stock_out_items (Updated)

```sql
ALTER TABLE tb_stock_out_items
ADD COLUMN harga_beli_satuan BIGINT,
ADD COLUMN harga_jual_satuan BIGINT;
```

---

## 🔧 Average Price Calculation Example:

**Scenario:**

- Product A: Current stock = 100 pcs, Current avg price = Rp 10,000
- New purchase: 50 pcs @ Rp 12,000

**Calculation:**

```
New Average = (100 × 10,000) + (50 × 12,000) / (100 + 50)
            = (1,000,000 + 600,000) / 150
            = 1,600,000 / 150
            = Rp 10,667 (rounded)
```

**Update:**

- `tb_products.stok_saat_ini` = 150
- `tb_products.harga_beli_saat_ini` = 10,667
- `tb_stock_in_items.harga_beli_satuan` = 12,000 (historical)

---

## ✅ Implementation Priority:

1. **High Priority** (Core functionality):

   - Update sidebar menu
   - Update Models (Product, StockIn, StockOut)
   - Update Purchase Controller & View (Average Price)
   - Update Sale Controller & View (Historical Pricing)

2. **Medium Priority** (Enhanced UX):

   - Quick Add integration
   - Invoice generation
   - Profit calculation display

3. **Low Priority** (Nice to have):
   - Profit/Loss Report
   - Advanced analytics
   - Export features for new reports
