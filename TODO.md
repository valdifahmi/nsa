# TODO List for Inventory Management System Setup

## 1. Update .env Configuration

- [x] Uncomment and set app.baseURL to 'http://localhost/nsa'
- [x] Ensure database settings: hostname=localhost, database=db_nsa, username=root, password=, DBDriver=MySQLi, port=3306

## 2. Create 8 Model Files in app/Models/

- [x] UserModel.php: table='tb_users', primaryKey='id', allowedFields=['username', 'password', 'nama_lengkap', 'role', 'created_at', 'updated_at']
- [x] BrandModel.php: table='tb_brands', primaryKey='id', allowedFields=['nama_brand', 'created_at']
- [x] CategoryModel.php: table='tb_categories', primaryKey='id', allowedFields=['nama_kategori', 'deskripsi', 'image', 'created_at', 'updated_at']
- [x] ProductModel.php: table='tb_products', primaryKey='id', allowedFields=['category_id', 'brand_id', 'kode_barang', 'nama_barang', 'deskripsi', 'image', 'satuan', 'stok_saat_ini', 'min_stok', 'created_at', 'updated_at']
- [x] StockInModel.php: table='tb_stock_in', primaryKey='id', allowedFields=['nomor_transaksi', 'user_id', 'tanggal_masuk', 'supplier', 'catatan', 'created_at']
- [x] StockInItemModel.php: table='tb_stock_in_items', primaryKey='id', allowedFields=['stock_in_id', 'product_id', 'jumlah']
- [x] StockOutModel.php: table='tb_stock_out', primaryKey='id', allowedFields=['nomor_transaksi', 'user_id', 'tanggal_keluar', 'penerima', 'catatan', 'created_at']
- [x] StockOutItemModel.php: table='tb_stock_out_items', primaryKey='id', allowedFields=['stock_out_id', 'product_id', 'jumlah']

## 3. Create UserSeeder

- [x] app/Database/Seeds/UserSeeder.php: Insert admin user with hashed password

## 4. Create AuthController

- [x] app/Controllers/AuthController.php: Methods login(), processLogin(), logout()

## 5. Create AuthFilter

- [x] app/Filters/AuthFilter.php: Check session for authentication

## 6. Update Filters Config

- [x] app/Config/Filters.php: Register AuthFilter

## 7. Followup Steps

- [x] Run seeder: php spark db:seed UserSeeder
- [x] Test login functionality

## Summary of Implementation

### Configuration Files Updated:

1. **env** - Set app.baseURL to 'http://localhost/nsa' and database settings
2. **app/Config/Filters.php** - Registered AuthFilter with exception for login routes
3. **app/Config/Routes.php** - Added auth routes (login, processLogin, logout)

### Models Created (8 files):

1. **UserModel.php** - Manages tb_users table
2. **BrandModel.php** - Manages tb_brands table
3. **CategoryModel.php** - Manages tb_categories table (includes image field)
4. **ProductModel.php** - Manages tb_products table (includes image and brand_id fields)
5. **StockInModel.php** - Manages tb_stock_in table
6. **StockInItemModel.php** - Manages tb_stock_in_items table
7. **StockOutModel.php** - Manages tb_stock_out table
8. **StockOutItemModel.php** - Manages tb_stock_out_items table

### Authentication System:

1. **UserSeeder.php** - Seeds admin user (username: admin, password: password123)
2. **AuthController.php** - Handles login, processLogin, logout with validation
3. **AuthFilter.php** - Protects routes, redirects to login if not authenticated
4. **login.php** - Updated with proper form action and username/password fields

### Testing Instructions:

1. Start server: `php spark serve`
2. Visit: http://localhost:8080/auth/login
3. Login with: username=admin, password=password123
4. After login, you'll be redirected to home page
5. Try accessing any protected route - should redirect to login if not authenticated
6. Logout via: http://localhost:8080/auth/logout
