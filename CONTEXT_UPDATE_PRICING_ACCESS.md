# CONTEXT UPDATE: LOGIKA AKSES & HARGA (REVISI V3)

## 🔐 PERUBAHAN PENTING - ROLE-BASED PRICING ACCESS

### **Prinsip Utama:**

1. **Staff** = Tidak boleh lihat/input harga sama sekali
2. **Admin** = Full access ke semua fitur termasuk harga
3. **Harga** = Dikelola di halaman Product (Admin only)
4. **Transaksi** = Simplified (tanpa input harga manual)
5. **Historical Pricing** = Auto-captured dari Product saat transaksi

---

## 📊 SKEMA DATABASE (TETAP SAMA):

### **tb_products** (Updated with Pricing):

```sql
CREATE TABLE tb_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    brand_id INT NULL,
    kode_barang VARCHAR(100) NOT NULL UNIQUE,
    nama_barang VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    image VARCHAR(255) NULL DEFAULT 'default.png',
    satuan VARCHAR(50) NOT NULL,
    stok_saat_ini INT NOT NULL DEFAULT 0,
    min_stok INT NOT NULL DEFAULT 0,
    harga_beli_saat_ini BIGINT DEFAULT 0,  -- Harga modal (average)
    harga_jual_saat_ini BIGINT DEFAULT 0,  -- Harga jual
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES tb_categories(id),
    CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES tb_brands(id)
);
```

### **tb_stock_in** (Header - Updated):

```sql
CREATE TABLE tb_stock_in (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(100) NOT NULL UNIQUE,
    supplier_id INT NOT NULL,  -- FK ke tb_suppliers
    user_id INT NOT NULL,
    tanggal_masuk DATETIME NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stockin_supplier FOREIGN KEY (supplier_id) REFERENCES tb_suppliers(id),
    CONSTRAINT fk_stockin_user FOREIGN KEY (user_id) REFERENCES tb_users(id)
);
```

### **tb_stock_in_items** (Detail - Updated):

```sql
CREATE TABLE tb_stock_in_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_in_id INT NOT NULL,
    product_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_beli_satuan BIGINT NOT NULL,  -- Historical: dari harga_beli_saat_ini
    CONSTRAINT fk_stockinitems_header FOREIGN KEY (stock_in_id) REFERENCES tb_stock_in(id) ON DELETE CASCADE,
    CONSTRAINT fk_stockinitems_product FOREIGN KEY (product_id) REFERENCES tb_products(id)
);
```

### **tb_stock_out** (Header - Updated):

```sql
CREATE TABLE tb_stock_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(100) NOT NULL UNIQUE,
    nomor_invoice VARCHAR(100) UNIQUE,
    client_id INT NOT NULL,  -- FK ke tb_clients
    user_id INT NOT NULL,
    tanggal_keluar DATETIME NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stockout_client FOREIGN KEY (client_id) REFERENCES tb_clients(id),
    CONSTRAINT fk_stockout_user FOREIGN KEY (user_id) REFERENCES tb_users(id)
);
```

### **tb_stock_out_items** (Detail - Updated):

```sql
CREATE TABLE tb_stock_out_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_out_id INT NOT NULL,
    product_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_beli_satuan BIGINT NOT NULL,  -- Historical: dari harga_beli_saat_ini (untuk HPP)
    harga_jual_satuan BIGINT NOT NULL,  -- Historical: dari harga_jual_saat_ini (untuk revenue)
    CONSTRAINT fk_stockoutitems_header FOREIGN KEY (stock_out_id) REFERENCES tb_stock_out(id) ON DELETE CASCADE,
    CONSTRAINT fk_stockoutitems_product FOREIGN KEY (product_id) REFERENCES tb_products(id)
);
```

---

## 🎯 ROLE-BASED ACCESS CONTROL:

### **1. ADMIN (Full Access)**

**Halaman Product:**

- ✅ View: Semua kolom termasuk harga
- ✅ Create: Input harga_beli_saat_ini & harga_jual_saat_ini
- ✅ Update: Edit harga_beli_saat_ini & harga_jual_saat_ini
- ✅ Delete: Hapus product

**Halaman Stock In/Out:**

- ✅ View: Semua transaksi
- ✅ Create: Buat transaksi (tanpa input harga manual)
- ✅ Scan: Produk otomatis ambil harga dari tb_products

**Halaman Report:**

- ✅ View: Semua laporan termasuk nilai rupiah
- ✅ Export: Semua format (Excel, PDF, Print)
- ✅ Profit/Loss: Lihat laba rugi per transaksi

### **2. STAFF (Limited Access)**

**Halaman Product:**

- ✅ View: Semua kolom **KECUALI** harga (hide harga_beli & harga_jual)
- ❌ Create: **TIDAK BISA** (atau bisa tapi tanpa input harga)
- ❌ Update: **TIDAK BISA** (atau bisa tapi tanpa edit harga)
- ❌ Delete: **TIDAK BISA**

**Halaman Stock In/Out:**

- ✅ View: Semua transaksi (tanpa nilai rupiah)
- ✅ Create: Buat transaksi (tanpa lihat/input harga)
- ✅ Scan: Produk otomatis ambil harga dari tb_products (hidden)

**Halaman Report:**

- ❌ **TIDAK BISA AKSES** (atau bisa akses tapi tanpa kolom harga/nilai)
- ❌ Profit/Loss: **TIDAK BISA LIHAT**

---

## 🔧 IMPLEMENTASI DETAIL:

### **A. Product Page (Admin Only Pricing)**

#### **View (app/Views/Product/index.php):**

```php
<!-- Tabel Product -->
<thead>
    <tr>
        <th>No</th>
        <th>Code</th>
        <th>Name</th>
        <th>Category</th>
        <th>Brand</th>
        <th>Unit</th>
        <th>Stock</th>
        <th>Min Stock</th>
        <?php if (session()->get('role') === 'admin'): ?>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
        <?php endif; ?>
        <th>Actions</th>
    </tr>
</thead>
```

#### **Modal Add/Edit:**

```php
<!-- Hanya tampil untuk Admin -->
<?php if (session()->get('role') === 'admin'): ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="add_harga_beli_saat_ini">Harga Beli (Modal)</label>
            <input type="number" class="form-control" id="add_harga_beli_saat_ini"
                   name="harga_beli_saat_ini" min="0" placeholder="Rupiah">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="add_harga_jual_saat_ini">Harga Jual</label>
            <input type="number" class="form-control" id="add_harga_jual_saat_ini"
                   name="harga_jual_saat_ini" min="0" placeholder="Rupiah">
        </div>
    </div>
</div>
<?php endif; ?>
```

#### **Controller (app/Controllers/ProductController.php):**

```php
public function store()
{
    // ... validation ...

    $data = [
        'kode_barang' => $this->request->getPost('kode_barang'),
        'nama_barang' => $this->request->getPost('nama_barang'),
        // ... other fields ...
    ];

    // Only admin can set prices
    if (session()->get('role') === 'admin') {
        $data['harga_beli_saat_ini'] = $this->request->getPost('harga_beli_saat_ini') ?? 0;
        $data['harga_jual_saat_ini'] = $this->request->getPost('harga_jual_saat_ini') ?? 0;
    }

    // ... save ...
}
```

---

### **B. Stock In Page (Simplified - No Price Input)**

#### **View (app/Views/Purchase/index.php):**

```html
<!-- Form Header -->
<div class="row">
  <div class="col-md-6">
    <label>Supplier <span class="text-danger">*</span></label>
    <div class="input-group">
      <select id="supplier_id" class="form-control" required>
        <option value="">Pilih Supplier</option>
      </select>
      <div class="input-group-append">
        <button
          type="button"
          class="btn btn-success"
          onclick="quickAddSupplier()"
        >
          <i class="ri-add-line"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <label>Tanggal Masuk</label>
    <input
      type="datetime-local"
      id="tanggal_masuk"
      class="form-control"
      required
    />
  </div>
</div>

<!-- Scan Product -->
<div class="row mt-3">
  <div class="col-md-12">
    <label>Scan Barcode / Kode Barang</label>
    <input
      type="text"
      id="scan_barcode"
      class="form-control"
      placeholder="Scan atau ketik kode barang"
      autofocus
    />
  </div>
</div>

<!-- Cart Table (NO PRICE COLUMN) -->
<table id="cartTable" class="table">
  <thead>
    <tr>
      <th>No</th>
      <th>Kode</th>
      <th>Nama Barang</th>
      <th>Satuan</th>
      <th>Qty</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody id="cartBody">
    <!-- Items here -->
  </tbody>
</table>

<!-- NO TOTAL DISPLAY -->
<button type="button" class="btn btn-primary" onclick="checkout()">
  <i class="ri-save-line"></i> Simpan Transaksi
</button>
```

#### **JavaScript (Simplified):**

```javascript
// Scan product - NO PRICE PROMPT
$('#scan_barcode').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        var barcode = $(this).val();

        $.ajax({
            url: '<?= base_url('product/findProductByCode') ?>',
            data: { code: barcode },
            success: function(response) {
                if (response.status === 'success') {
                    var product = response.data;

                    // Prompt QTY only (NO PRICE)
                    var qty = prompt('Masukkan jumlah untuk ' + product.nama_barang + ':', '1');

                    if (qty && qty > 0) {
                        addToCart({
                            product_id: product.id,
                            kode_barang: product.kode_barang,
                            nama_barang: product.nama_barang,
                            satuan: product.satuan,
                            jumlah: qty
                            // NO PRICE in cart
                        });
                    }
                }
            }
        });

        $(this).val('');
    }
});

// Checkout - NO PRICE CALCULATION
function checkout() {
    var data = {
        supplier_id: $('#supplier_id').val(),
        tanggal_masuk: $('#tanggal_masuk').val(),
        items: cartItems // Array of {product_id, jumlah}
    };

    $.ajax({
        url: '<?= base_url('purchase/store') ?>',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            // Success handling
        }
    });
}
```

#### **Controller (app/Controllers/PurchaseController.php):**

```php
public function store()
{
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $input = $this->request->getJSON();

        // 1. Save header
        $headerData = [
            'nomor_transaksi' => $this->generateTransactionNumber('IN'),
            'supplier_id' => $input->supplier_id,
            'user_id' => session()->get('user_id'),
            'tanggal_masuk' => $input->tanggal_masuk,
            'catatan' => $input->catatan ?? null
        ];

        $this->stockInModel->insert($headerData);
        $stockInId = $this->stockInModel->getInsertID();

        // 2. Process items
        foreach ($input->items as $item) {
            // GET PRODUCT DATA (including current prices)
            $product = $this->productModel->find($item->product_id);

            // Save detail with HISTORICAL PRICE from product
            $detailData = [
                'stock_in_id' => $stockInId,
                'product_id' => $item->product_id,
                'jumlah' => $item->jumlah,
                'harga_beli_satuan' => $product['harga_beli_saat_ini'] // AUTO from product
            ];

            $this->stockInItemModel->insert($detailData);

            // 3. Update stock (NO PRICE UPDATE - Admin handles that)
            $newStock = $product['stok_saat_ini'] + $item->jumlah;

            $this->productModel->update($item->product_id, [
                'stok_saat_ini' => $newStock
            ]);

            // 4. Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'CREATE',
                'module' => 'Stock In',
                'log_message' => "Barang masuk: {$product['nama_barang']} ({$item->jumlah} {$product['satuan']})"
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi gagal'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Transaksi berhasil disimpan',
            'nomor_transaksi' => $headerData['nomor_transaksi']
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
```

---

### **C. Stock Out Page (Simplified - No Price Input)**

#### **View (app/Views/Sale/index.php):**

```html
<!-- Similar to Stock In, but with Client instead of Supplier -->
<div class="row">
  <div class="col-md-6">
    <label>Client <span class="text-danger">*</span></label>
    <div class="input-group">
      <select id="client_id" class="form-control" required>
        <option value="">Pilih Client</option>
      </select>
      <div class="input-group-append">
        <button
          type="button"
          class="btn btn-success"
          onclick="quickAddClient()"
        >
          <i class="ri-add-line"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <label>Tanggal Keluar</label>
    <input
      type="datetime-local"
      id="tanggal_keluar"
      class="form-control"
      required
    />
  </div>
</div>

<!-- Cart Table (NO PRICE COLUMN) -->
<table id="cartTable" class="table">
  <thead>
    <tr>
      <th>No</th>
      <th>Kode</th>
      <th>Nama Barang</th>
      <th>Satuan</th>
      <th>Qty</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody id="cartBody"></tbody>
</table>
```

#### **Controller (app/Controllers/SaleController.php):**

```php
public function store()
{
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $input = $this->request->getJSON();

        // 1. Generate invoice number
        $invoiceNumber = $this->generateInvoiceNumber();

        // 2. Save header
        $headerData = [
            'nomor_transaksi' => $this->generateTransactionNumber('OUT'),
            'nomor_invoice' => $invoiceNumber,
            'client_id' => $input->client_id,
            'user_id' => session()->get('user_id'),
            'tanggal_keluar' => $input->tanggal_keluar,
            'catatan' => $input->catatan ?? null
        ];

        $this->stockOutModel->insert($headerData);
        $stockOutId = $this->stockOutModel->getInsertID();

        // 3. Process items
        foreach ($input->items as $item) {
            // GET PRODUCT DATA (including current prices)
            $product = $this->productModel->find($item->product_id);

            // Check stock
            if ($product['stok_saat_ini'] < $item->jumlah) {
                throw new \Exception("Stok {$product['nama_barang']} tidak cukup");
            }

            // Save detail with HISTORICAL PRICES from product
            $detailData = [
                'stock_out_id' => $stockOutId,
                'product_id' => $item->product_id,
                'jumlah' => $item->jumlah,
                'harga_beli_satuan' => $product['harga_beli_saat_ini'], // AUTO from product (HPP)
                'harga_jual_satuan' => $product['harga_jual_saat_ini']  // AUTO from product (Revenue)
            ];

            $this->stockOutItemModel->insert($detailData);

            // 4. Update stock
            $newStock = $product['stok_saat_ini'] - $item->jumlah;

            $this->productModel->update($item->product_id, [
                'stok_saat_ini' => $newStock
            ]);

            // 5. Log activity
            $this->logModel->insert([
                'user_id' => session()->get('user_id'),
                'action' => 'CREATE',
                'module' => 'Stock Out',
                'log_message' => "Barang keluar: {$product['nama_barang']} ({$item->jumlah} {$product['satuan']})"
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi gagal'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Transaksi berhasil disimpan',
            'nomor_transaksi' => $headerData['nomor_transaksi'],
            'nomor_invoice' => $invoiceNumber
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

private function generateInvoiceNumber()
{
    // Format: INV/YYYYMMDD/XXXX
    $date = date('Ymd');
    $prefix = "INV/{$date}/";

    // Get last invoice today
    $lastInvoice = $this->stockOutModel
        ->like('nomor_invoice', $prefix)
        ->orderBy('id', 'DESC')
        ->first();

    if ($lastInvoice) {
        $lastNumber = (int) substr($lastInvoice['nomor_invoice'], -4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}
```

---

### **D. Report Pages (Admin Only - With Prices)**

#### **Filter di Controller:**

```php
// app/Controllers/ReportController.php

public function purchaseReport()
{
    // Check if admin
    if (session()->get('role') !== 'admin') {
        return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
    }

    return view('Report/purchase');
}

public function fetchPurchaseItemReport()
{
    // Check if admin
    if (session()->get('role') !== 'admin') {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Akses ditolak'
        ]);
    }

    // ... fetch data with prices ...
}
```

#### **View dengan Harga (Admin Only):**

```php
<!-- app/Views/Report/purchase_item.php -->
<table id="reportTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Transaksi</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Harga Satuan</th> <!-- Admin only -->
            <th>Total</th>         <!-- Admin only -->
        </tr>
    </thead>
</table>
```

---

## 📋 SUMMARY PERUBAHAN:

### **1. Product Page:**

- ✅ Admin: Bisa input/edit `harga_beli_saat_ini` & `harga_jual_saat_ini`
- ✅ Staff: Tidak bisa lihat/edit harga (kolom hidden)

### **2. Stock In/Out Page:**

- ✅ Form: Hanya input Supplier/Client, Scan Produk, Qty
- ✅ Cart: Tidak ada kolom harga
- ✅ Checkout: Tidak ada total rupiah
- ✅ Backend: Auto-ambil harga dari `tb_products` saat save

### **3. Historical Pricing:**

- ✅ `tb_stock_in_items.harga_beli_satuan` = Auto dari `tb_products.harga_beli_saat_ini`
- ✅ `tb_stock_out_items.harga_beli_satuan` = Auto dari `tb_products.harga_beli_saat_ini` (HPP)
- ✅ `tb_stock_out_items.harga_jual_satuan` = Auto dari `tb_products.harga_jual_saat_ini` (Revenue)

### **4. Reports:**

- ✅ Admin: Bisa akses semua report dengan nilai rupiah
- ✅ Staff: Tidak bisa akses report (atau akses tanpa kolom harga)

### **5. Average Price Calculation:**

- ❌ **TIDAK DIGUNAKAN** (karena harga dikelola manual oleh Admin di Product page)
- ✅ Admin update harga di Product page kapan saja
- ✅ Transaksi hanya update stok, tidak update harga

---

## 🎯 NEXT STEPS IMPLEMENTATION:

### **Priority 1: Update Models**

1. Update `ProductModel` - Add `harga_beli_saat_ini`, `harga_jual_saat_ini` to `$allowedFields`
2. Update `StockInModel` - Change `supplier` to `supplier_id` in `$allowedFields`
3. Update `StockInItemModel` - Add `harga_beli_satuan` to `$allowedFields`
4. Update `StockOutModel` - Change `penerima` to `client_id`, add `nomor_invoice` to `$allowedFields`
5. Update `StockOutItemModel` - Add `harga_beli_satuan`, `harga_jual_satuan` to `$allowedFields`

### **Priority 2: Update Product Page**

1. Add price fields to Add/Edit modal (Admin only)
2. Add price columns to table (Admin only)
3. Update `ProductController` to handle price input (Admin only)
4. Hide price fields for Staff role

### **Priority 3: Update Purchase Page**

1. Replace `supplier` text input with `supplier_id` dropdown
2. Add Quick Add Supplier button
3. Remove price input from cart
4. Update `PurchaseController` to auto-capture price from product

### **Priority 4: Update Sale Page**

1. Replace `penerima` text input with `client_id` dropdown
2. Add Quick Add Client button
3. Remove price input from cart
4. Add invoice number generation
5. Update `SaleController` to auto-capture prices from product

### **Priority 5: Update Reports**

1. Add Admin filter to all report controllers
2. Add price columns to report views (Admin only)
3. Update report queries to include historical prices

### **Priority 6: Testing**

1. Test Admin access to all features
2. Test Staff limited access
3. Test price auto-capture in transactions
4. Test historical pricing in reports
5. Test invoice generation

---

## ✅ CHECKLIST:

- [ ] Update 5 Models (allowedFields)
- [ ] Update ProductController (Admin-only pricing)
- [ ] Update Product View (role-based display)
- [ ] Update PurchaseController (auto-capture price)
- [ ] Update Purchase View (simplified form)
- [ ] Update SaleController (auto-capture prices + invoice)
- [ ] Update Sale View (simplified form)
- [ ] Update ReportController (Admin filter)
- [ ] Update Report Views (price columns)
- [ ] Add AdminFilter to routes
- [ ] Test all features with Admin role
- [ ] Test all features with Staff role

---

**Konteks ini menggantikan logika Average Price dengan Manual Pricing + Auto-Capture!** 🎯
