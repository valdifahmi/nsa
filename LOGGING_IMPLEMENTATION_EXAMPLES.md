# Contoh Implementasi LogHelper

## Cara Menggunakan LogHelper

LogHelper sudah di-autoload, jadi bisa langsung digunakan di mana saja tanpa perlu load manual.

### Syntax:

```php
LogHelper::add($action, $module, $record_id, $log_message);
```

### Parameters:

- `$action` (string, required): Action yang dilakukan (CREATE, UPDATE, DELETE, LOGIN, LOGOUT, dll)
- `$module` (string, required): Nama module (Category, Product, Brand, Purchase, Sale, dll)
- `$record_id` (int, optional): ID record yang terpengaruh
- `$log_message` (string, optional): Pesan detail log

---

## Contoh Implementasi di Controller

### 1. CategoryController - CREATE

```php
public function store()
{
    // ... validation & save logic ...

    if ($this->categoryModel->insert($data)) {
        $categoryId = $this->categoryModel->getInsertID();

        // Add log
        LogHelper::add('CREATE', 'Category', $categoryId, 'Created category: ' . $data['nama_kategori']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Category berhasil ditambahkan'
        ]);
    }
}
```

### 2. ProductController - UPDATE

```php
public function update($id)
{
    // ... validation & update logic ...

    if ($this->productModel->update($id, $data)) {
        // Add log
        LogHelper::add('UPDATE', 'Product', $id, 'Updated product: ' . $data['nama_barang']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Product berhasil diupdate'
        ]);
    }
}
```

### 3. BrandController - DELETE

```php
public function delete($id)
{
    $brand = $this->brandModel->find($id);

    if ($this->brandModel->delete($id)) {
        // Add log
        LogHelper::add('DELETE', 'Brand', $id, 'Deleted brand: ' . $brand['nama_brand']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Brand berhasil dihapus'
        ]);
    }
}
```

### 4. PurchaseController - CREATE (Transaction)

```php
public function store()
{
    // ... transaction logic ...

    $this->db->transBegin();

    try {
        // Insert header
        $headerId = $this->stockInModel->insert($headerData);

        // Insert items
        foreach ($items as $item) {
            $this->stockInItemModel->insert([
                'stock_in_id' => $headerId,
                'product_id' => $item['product_id'],
                'jumlah' => $item['jumlah']
            ]);
        }

        $this->db->transCommit();

        // Add log
        LogHelper::add('CREATE', 'Purchase', $headerId, 'Created purchase: ' . $headerData['nomor_transaksi'] . ' with ' . count($items) . ' items');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Purchase berhasil disimpan'
        ]);

    } catch (\Exception $e) {
        $this->db->transRollback();

        // Log error
        LogHelper::add('ERROR', 'Purchase', null, 'Failed to create purchase: ' . $e->getMessage());

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menyimpan purchase'
        ]);
    }
}
```

### 5. SaleController - CREATE (Transaction)

```php
public function store()
{
    // ... similar to PurchaseController ...

    if ($this->db->transStatus() === false) {
        $this->db->transRollback();
        LogHelper::add('ERROR', 'Sale', null, 'Failed to create sale transaction');
    } else {
        $this->db->transCommit();
        LogHelper::add('CREATE', 'Sale', $headerId, 'Created sale: ' . $headerData['nomor_transaksi']);
    }
}
```

### 6. AuthController - LOGIN

```php
public function processLogin()
{
    // ... validation & authentication logic ...

    if (password_verify($password, $user['password'])) {
        // Set session
        session()->set('user', $user);

        // Add log
        LogHelper::add('LOGIN', 'Auth', $user['id'], 'User logged in: ' . $user['username']);

        return redirect()->to('/');
    }
}
```

### 7. AuthController - LOGOUT

```php
public function logout()
{
    $user = session()->get('user');

    // Add log before destroying session
    LogHelper::add('LOGOUT', 'Auth', $user['id'], 'User logged out: ' . $user['username']);

    session()->destroy();
    return redirect()->to('/auth/login');
}
```

### 8. PeopleController - CREATE User

```php
public function store()
{
    // ... validation & save logic ...

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    if ($this->userModel->insert($data)) {
        $userId = $this->userModel->getInsertID();

        // Add log
        LogHelper::add('CREATE', 'User', $userId, 'Created user: ' . $data['username'] . ' with role: ' . $data['role']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User berhasil ditambahkan'
        ]);
    }
}
```

---

## Action Types (Recommended)

- `CREATE` - Membuat record baru
- `UPDATE` - Mengupdate record
- `DELETE` - Menghapus record
- `LOGIN` - User login
- `LOGOUT` - User logout
- `VIEW` - Melihat detail (optional)
- `EXPORT` - Export data (optional)
- `IMPORT` - Import data (optional)
- `ERROR` - Error/Exception occurred

## Module Names (Recommended)

- `Category`
- `Brand`
- `Product`
- `Purchase`
- `Sale`
- `User`
- `Auth`
- `Report`

---

## Query Logs (Optional - untuk melihat logs)

### Get all logs:

```php
$logModel = new \App\Models\LogModel();
$logs = $logModel->orderBy('created_at', 'DESC')->findAll();
```

### Get logs by user:

```php
$logs = $logModel->where('user_id', $userId)->findAll();
```

### Get logs by module:

```php
$logs = $logModel->where('module', 'Product')->findAll();
```

### Get logs by action:

```php
$logs = $logModel->where('action', 'DELETE')->findAll();
```

### Get logs with user info (JOIN):

```php
$db = \Config\Database::connect();
$builder = $db->table('tb_logs l');
$builder->select('l.*, u.username, u.nama_lengkap');
$builder->join('tb_users u', 'l.user_id = u.id', 'left');
$builder->orderBy('l.created_at', 'DESC');
$logs = $builder->get()->getResultArray();
```

---

## Notes:

1. **LogHelper sudah auto-load**, tidak perlu `helper('LogHelper')` atau `use`.
2. **User ID otomatis** diambil dari session, tidak perlu pass manual.
3. **Error handling** sudah ada di LogHelper, jika gagal tidak akan break aplikasi.
4. **Timestamp otomatis** menggunakan created_at.
5. **Foreign key** user_id ON DELETE SET NULL, jadi log tetap ada meski user dihapus.
