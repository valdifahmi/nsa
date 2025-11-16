# TODO List - CRUD Brand (Modal + AJAX)

## 1. Create BrandController

- [x] app/Controllers/BrandController.php
  - [x] index() - Load view Brand/index.php
  - [x] fetchAll() - AJAX get all brands
  - [x] store() - AJAX create new brand with validation
  - [x] edit($id) - AJAX get brand by id
  - [x] update($id) - AJAX update brand with validation
  - [x] delete($id) - AJAX delete brand (check if used in products)

## 2. Create View

- [x] app/Views/Brand/index.php
  - [x] Table list brands (No, Nama Brand, Created At, Action)
  - [x] Modal Add Brand
  - [x] Modal Edit Brand
  - [x] AJAX CRUD operations
  - [x] Validation error display

## 3. Update Routes

- [x] app/Config/Routes.php - Add brand routes with 'auth' filter

## 4. Update Template Sidebar

- [x] app/Views/Layout/template.php - Add Brand link (after Categories)

## 5. Testing

- [ ] Test fetch all brands
- [ ] Test add new brand
- [ ] Test edit brand
- [ ] Test delete brand
- [ ] Test validation (required, unique, min/max length)
- [ ] Test delete brand yang digunakan di produk (harus error)
