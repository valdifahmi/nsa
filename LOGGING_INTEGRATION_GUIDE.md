# Panduan Integrasi LogHelper ke Controller

## ✅ ProductController - SUDAH DIIMPLEMENTASI

### 1. Store (CREATE)

```php
if ($this->productModel->insert($data)) {
    // Get the inserted product ID
    $productId = $this->productModel->getInsertID();

    // Get user info from session
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    // Add log
    LogHelper::add(
        'CREATE',
        'Product',
        $productId,
        "User '{$userName}' menambahkan produk baru: '{$data['nama_barang']}' (Kode: {$data['kode_barang']})"
    );

    return $this->response->setJSON([...]);
}
```

### 2. Update (UPDATE)

```php
if ($this->productModel->update($id, $data)) {
    // Get user info from session
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    // Add log
    LogHelper::add(
        'UPDATE',
        'Product',
        $id,
        "User '{$userName}' mengubah data produk '{$data['nama_barang']}' (Kode: {$data['kode_barang']})"
    );

    return $this->response->setJSON([...]);
}
```

### 3. Delete (DELETE)

```php
public function delete($id)
{
    $product = $this->productModel->find($id);

    if (!$product) {
        return $this->response->setJSON([...]);
    }

    // Store product name BEFORE deletion
    $productName = $product['nama_barang'];
    $productCode = $product['kode_barang'];

    if ($this->productModel->delete($id)) {
        // Get user info from session
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        // Add log
        LogHelper::add(
            'DELETE',
            'Product',
            $id,
            "User '{$userName}' menghapus produk '{$productName}' (Kode: {$productCode})"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

## 📋 Template untuk Controller Lain

### CategoryController

#### Store:

```php
if ($this->categoryModel->insert($data)) {
    $categoryId = $this->categoryModel->getInsertID();
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'CREATE',
        'Category',
        $categoryId,
        "User '{$userName}' menambahkan kategori baru: '{$data['nama_kategori']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Update:

```php
if ($this->categoryModel->update($id, $data)) {
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'UPDATE',
        'Category',
        $id,
        "User '{$userName}' mengubah data kategori '{$data['nama_kategori']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Delete:

```php
public function delete($id)
{
    $category = $this->categoryModel->find($id);

    if (!$category) {
        return $this->response->setJSON([...]);
    }

    // Store name BEFORE deletion
    $categoryName = $category['nama_kategori'];

    if ($this->categoryModel->delete($id)) {
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'DELETE',
            'Category',
            $id,
            "User '{$userName}' menghapus kategori '{$categoryName}'"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

### BrandController

#### Store:

```php
if ($this->brandModel->insert($data)) {
    $brandId = $this->brandModel->getInsertID();
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'CREATE',
        'Brand',
        $brandId,
        "User '{$userName}' menambahkan brand baru: '{$data['nama_brand']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Update:

```php
if ($this->brandModel->update($id, $data)) {
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'UPDATE',
        'Brand',
        $id,
        "User '{$userName}' mengubah data brand '{$data['nama_brand']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Delete:

```php
public function delete($id)
{
    $brand = $this->brandModel->find($id);

    if (!$brand) {
        return $this->response->setJSON([...]);
    }

    // Store name BEFORE deletion
    $brandName = $brand['nama_brand'];

    if ($this->brandModel->delete($id)) {
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'DELETE',
            'Brand',
            $id,
            "User '{$userName}' menghapus brand '{$brandName}'"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

### PeopleController (User Management)

#### Store:

```php
if ($this->userModel->insert($data)) {
    $userId = $this->userModel->getInsertID();
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'CREATE',
        'User',
        $userId,
        "User '{$userName}' menambahkan user baru: '{$data['username']}' dengan role '{$data['role']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Update:

```php
if ($this->userModel->update($id, $data)) {
    $user = session()->get('user');
    $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

    LogHelper::add(
        'UPDATE',
        'User',
        $id,
        "User '{$userName}' mengubah data user '{$data['username']}'"
    );

    return $this->response->setJSON([...]);
}
```

#### Delete:

```php
public function delete($id)
{
    $targetUser = $this->userModel->find($id);

    if (!$targetUser) {
        return $this->response->setJSON([...]);
    }

    // Store name BEFORE deletion
    $targetUsername = $targetUser['username'];
    $targetNama = $targetUser['nama_lengkap'];

    if ($this->userModel->delete($id)) {
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'DELETE',
            'User',
            $id,
            "User '{$userName}' menghapus user '{$targetUsername}' ({$targetNama})"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

### PurchaseController (Stock In)

#### Store (Transaction):

```php
public function store()
{
    $this->db->transBegin();

    try {
        // Insert header
        $headerData = [...];
        $this->stockInModel->insert($headerData);
        $headerId = $this->stockInModel->getInsertID();

        // Insert items & update stock
        $totalItems = 0;
        foreach ($items as $item) {
            $this->stockInItemModel->insert([...]);
            // Update stock...
            $totalItems++;
        }

        $this->db->transCommit();

        // Add log
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'CREATE',
            'Purchase',
            $headerId,
            "User '{$userName}' membuat transaksi barang masuk '{$headerData['nomor_transaksi']}' dengan {$totalItems} item dari supplier '{$headerData['supplier']}'"
        );

        return $this->response->setJSON([...]);

    } catch (\Exception $e) {
        $this->db->transRollback();

        // Log error
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'ERROR',
            'Purchase',
            null,
            "User '{$userName}' gagal membuat transaksi barang masuk: {$e->getMessage()}"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

### SaleController (Stock Out)

#### Store (Transaction):

```php
public function store()
{
    $this->db->transBegin();

    try {
        // Insert header
        $headerData = [...];
        $this->stockOutModel->insert($headerData);
        $headerId = $this->stockOutModel->getInsertID();

        // Insert items & update stock
        $totalItems = 0;
        foreach ($items as $item) {
            $this->stockOutItemModel->insert([...]);
            // Update stock...
            $totalItems++;
        }

        $this->db->transCommit();

        // Add log
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'CREATE',
            'Sale',
            $headerId,
            "User '{$userName}' membuat transaksi barang keluar '{$headerData['nomor_transaksi']}' dengan {$totalItems} item untuk penerima '{$headerData['penerima']}'"
        );

        return $this->response->setJSON([...]);

    } catch (\Exception $e) {
        $this->db->transRollback();

        // Log error
        $user = session()->get('user');
        $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';

        LogHelper::add(
            'ERROR',
            'Sale',
            null,
            "User '{$userName}' gagal membuat transaksi barang keluar: {$e->getMessage()}"
        );

        return $this->response->setJSON([...]);
    }
}
```

---

### AuthController

#### processLogin (LOGIN):

```php
public function processLogin()
{
    // ... validation & authentication ...

    if (password_verify($password, $user['password'])) {
        // Set session
        session()->set('user', $user);

        // Add log
        LogHelper::add(
            'LOGIN',
            'Auth',
            $user['id'],
            "User '{$user['username']}' ({$user['nama_lengkap']}) berhasil login dengan role '{$user['role']}'"
        );

        return redirect()->to('/');
    } else {
        // Log failed login attempt
        LogHelper::add(
            'ERROR',
            'Auth',
            null,
            "Percobaan login gagal untuk username '{$username}'"
        );

        return redirect()->back()->with('error', 'Invalid credentials');
    }
}
```

#### logout (LOGOUT):

```php
public function logout()
{
    $user = session()->get('user');

    // Add log BEFORE destroying session
    if ($user) {
        LogHelper::add(
            'LOGOUT',
            'Auth',
            $user['id'],
            "User '{$user['username']}' ({$user['nama_lengkap']}) logout dari sistem"
        );
    }

    session()->destroy();
    return redirect()->to('/auth/login');
}
```

---

## 🔑 Key Points:

1. **Ambil user info dari session:**

   ```php
   $user = session()->get('user');
   $userName = $user['nama_lengkap'] ?? $user['username'] ?? 'Unknown';
   ```

2. **Untuk DELETE, simpan data SEBELUM dihapus:**

   ```php
   $record = $this->model->find($id);
   $recordName = $record['nama_field'];
   // ... delete ...
   // ... log dengan $recordName ...
   ```

3. **Untuk CREATE, ambil ID setelah insert:**

   ```php
   $this->model->insert($data);
   $recordId = $this->model->getInsertID();
   ```

4. **Untuk Transaction, log di dalam try-catch:**

   ```php
   try {
       // ... transaction logic ...
       $this->db->transCommit();
       LogHelper::add('CREATE', ...);
   } catch (\Exception $e) {
       $this->db->transRollback();
       LogHelper::add('ERROR', ...);
   }
   ```

5. **Format pesan log yang informatif:**
   - Sertakan nama user
   - Sertakan nama/kode record
   - Sertakan detail penting (jumlah item, supplier, penerima, dll)
   - Untuk error, sertakan error message

---

## ⚠️ Note tentang Intelephense Error:

Error "Undefined type 'LogHelper'" adalah warning IDE saja karena LogHelper adalah class global (bukan namespace). Kode akan berfungsi dengan baik karena:

1. LogHelper sudah di-autoload di `app/Config/Autoload.php`
2. LogHelper adalah helper function, bukan class dengan namespace
3. CI4 akan auto-load helper saat aplikasi start

Anda bisa mengabaikan warning ini atau menambahkan PHPDoc comment di atas penggunaan LogHelper untuk mematikan warning.
