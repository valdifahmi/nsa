# TODO: PRICING MODULE (ADMIN ONLY)

## 🎯 Objective:

Membuat halaman khusus Admin untuk mengelola harga produk tanpa melibatkan Staff.

## 📋 Tasks:

### 1. Backend

- [ ] Create AdminFilter.php (protect admin-only routes)
- [ ] Create PricingController.php
  - [ ] index() - Load pricing view
  - [ ] fetchAll() - Get all products with pricing (AJAX)
  - [ ] updatePrice() - Update single product price (AJAX)
  - [ ] bulkMarkUp() - Bulk price update by category/brand (AJAX)
- [ ] Update Routes.php (add pricing routes with AdminFilter)

### 2. Stock In Adjustment

- [ ] Update PurchaseController::store()
  - [ ] Use harga_beli_saat_ini from tb_products
  - [ ] Save to tb_stock_in_items.harga_beli_satuan

### 3. Stock Out Adjustment

- [ ] Update SaleController::store()
  - [ ] Already implemented (auto-capture pricing)
  - [ ] Verify it uses harga_beli_saat_ini and harga_jual_saat_ini

### 4. Frontend

- [ ] Create Pricing/index.php
  - [ ] DataTables for product list
  - [ ] Edit Price button (opens modal)
  - [ ] Modal with:
    - [ ] Old price display
    - [ ] New price input
    - [ ] Margin calculator (Harga Jual = Harga Beli + Margin %)
  - [ ] Bulk Mark-Up section
    - [ ] Filter by Category/Brand
    - [ ] Percentage input
    - [ ] Apply button

### 5. Activity Logging

- [ ] Log all price changes
- [ ] Log bulk mark-up operations

## 🔐 Security:

- AdminFilter must check session role = 'admin'
- Staff cannot access /pricing routes
- All pricing operations logged

## 📊 Database:

- tb_products: harga_beli_saat_ini, harga_jual_saat_ini (already exists)
- tb_stock_in_items: harga_beli_satuan (already exists)
- tb_stock_out_items: harga_beli_satuan, harga_jual_satuan (already exists)
- tb_logs: activity logging (already exists)

## 🎨 UI Features:

- DataTables with search/filter
- Modal for single price edit
- Margin calculator (auto-calculate selling price)
- Bulk mark-up by category/brand
- Success/error alerts
