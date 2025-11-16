# TODO List - Sistem Logging Terpusat

## 1. Create Database Table

- [x] tb_logs table dengan kolom:
  - [x] id (INT, AUTO_INCREMENT, PRIMARY KEY)
  - [x] user_id (INT, FOREIGN KEY ke tb_users, ON DELETE SET NULL)
  - [x] action (VARCHAR(50) - CREATE, UPDATE, DELETE, LOGIN, LOGOUT, dll)
  - [x] module (VARCHAR(50) - Category, Product, Brand, Purchase, Sale, dll)
  - [x] record_id (INT NULL - ID record yang diubah)
  - [x] log_message (TEXT NULL - Deskripsi detail)
  - [x] created_at (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
  - [x] Indexes untuk performance (user_id, module, action, created_at)
- [x] File: database_tb_logs.sql

## 2. Create LogModel

- [x] app/Models/LogModel.php
  - [x] $table = 'tb_logs'
  - [x] $primaryKey = 'id'
  - [x] $allowedFields = ['user_id', 'action', 'module', 'record_id', 'log_message', 'created_at']
  - [x] $useTimestamps = true
  - [x] $createdField = 'created_at'
  - [x] $updatedField = '' (no update field)
  - [x] $returnType = 'array'

## 3. Create LogHelper

- [x] app/Helpers/LogHelper.php
  - [x] Static class LogHelper
  - [x] Static function add($action, $module, $record_id = null, $log_message = null)
  - [x] Get user_id from session()->get('user')['id']
  - [x] Handle null user_id (for guest/system actions)
  - [x] Insert to tb_logs using LogModel
  - [x] Try-catch error handling (tidak break aplikasi jika gagal)
  - [x] Log error ke CI4 log_message() jika gagal

## 4. Autoload Helper

- [x] app/Config/Autoload.php
  - [x] Add 'LogHelper' to $helpers array
  - [x] Helper akan auto-load di semua controller

## 5. Documentation

- [x] LOGGING_IMPLEMENTATION_EXAMPLES.md
  - [x] Syntax & parameters
  - [x] Contoh implementasi di 8 controller
  - [x] Action types recommended
  - [x] Module names recommended
  - [x] Query examples untuk view logs
  - [x] Notes & best practices

## 6. Implementation (Optional - bisa dilakukan nanti)

- [ ] Category: LogHelper::add('CREATE', 'Category', $id, 'Created category: ' . $name)
- [ ] Product: LogHelper::add('UPDATE', 'Product', $id, 'Updated product: ' . $name)
- [ ] Brand: LogHelper::add('DELETE', 'Brand', $id, 'Deleted brand: ' . $name)
- [ ] Purchase: LogHelper::add('CREATE', 'Purchase', $id, 'Created purchase: ' . $nomor)
- [ ] Sale: LogHelper::add('CREATE', 'Sale', $id, 'Created sale: ' . $nomor)
- [ ] Auth: LogHelper::add('LOGIN', 'Auth', $user_id, 'User logged in')
- [ ] Auth: LogHelper::add('LOGOUT', 'Auth', $user_id, 'User logged out')
- [ ] People: LogHelper::add('CREATE', 'User', $id, 'Created user: ' . $username)

## 7. Testing

- [ ] Run database_tb_logs.sql di phpMyAdmin/MySQL
- [ ] Test LogHelper::add() dari controller
- [ ] Verify log tersimpan di tb_logs
- [ ] Check user_id, action, module, record_id, log_message
- [ ] Check created_at timestamp
- [ ] Test dengan user login vs tanpa login (null user_id)
