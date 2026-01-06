# DASHBOARD WAREHOUSE DATA - BACKEND IMPLEMENTATION

## 📋 Overview

Implementasi backend untuk menyediakan data warehouse dalam format JSON yang terstruktur. Method `fetchWarehouseData` mengembalikan data khusus untuk Section Warehouse dengan 5 komponen utama.

---

## ✅ Implementation Complete

### **1. New Method: `fetchWarehouseData()`**

**Location**: `app/Controllers/DashboardController.php`

**Endpoint**: `POST /dashboard/fetchWarehouseData`

**Purpose**: Mengembalikan data warehouse dalam format JSON terstruktur

---

## 📊 Response Structure

```json
{
  "success": true,
  "warehouse": {
    "total_stok": 1500,
    "stock_in_volume": 250,
    "stock_out_volume": 180,
    "active_wo_count": 5,
    "sales_velocity_data": [
      {
        "product_name": "Product A",
        "product_id": 1,
        "avg_days_to_sell": 3.5,
        "total_sold": 100
      }
      // ... 9 more products (Top 10)
    ]
  }
}
```

---

## 🔧 Data Components

### 1. **total_stok** (Integer)

- **Description**: Sum of all product stock (stok_saat_ini)
- **Query**: `SUM(p.stok_saat_ini)` from `tb_products`
- **Filters**: Product, Brand, Category (optional)
- **Format**: Integer (e.g., 1500)

### 2. **stock_in_volume** (Integer)

- **Description**: Total quantity of items received (Stock-IN)
- **Query**: `SUM(sii.jumlah)` from `tb_stock_in_items`
- **Date Range**: Filtered by `tanggal_masuk` (startDate to endDate)
- **Filters**: Product, Brand, Category (optional)
- **Format**: Integer (e.g., 250)

### 3. **stock_out_volume** (Integer)

- **Description**: Total quantity of items sold/issued (Stock-OUT)
- **Query**: `SUM(soi.jumlah)` from `tb_stock_out_items`
- **Date Range**: Filtered by `tanggal_keluar` (startDate to endDate)
- **Filters**: Product, Brand, Category (optional)
- **Format**: Integer (e.g., 180)

### 4. **active_wo_count** (Integer)

- **Description**: Count of active work orders (status_work_order = 'Proses')
- **Query**: `COUNT(*)` from `tb_stock_out`
- **Condition**: `status_work_order = 'Proses'`
- **Date Range**: Filtered by `tanggal_keluar` (startDate to endDate)
- **Note**: Typically NOT filtered by product/brand/category
- **Format**: Integer (e.g., 5)

### 5. **sales_velocity_data** (Array of Objects)

- **Description**: Top 10 products by sales velocity (fastest selling)
- **Query**: Complex query with DATEDIFF calculation
- **Metrics**:
  - `product_name`: Name of the product
  - `product_id`: ID of the product
  - `avg_days_to_sell`: Average days from stock-in to stock-out
  - `total_sold`: Total quantity sold in the period
- **Sorting**: ASC by `avg_days_to_sell` (fastest first)
- **Limit**: **Top 10 products** (changed from 5)
- **Format**: Array of objects

---

## 📥 Request Parameters

All parameters are sent via POST:

| Parameter    | Type           | Required | Default     | Description                |
| ------------ | -------------- | -------- | ----------- | -------------------------- |
| `startDate`  | string (Y-m-d) | No       | 30 days ago | Start date for filtering   |
| `endDate`    | string (Y-m-d) | No       | Today       | End date for filtering     |
| `productId`  | string/int     | No       | -           | Filter by specific product |
| `brandId`    | string/int     | No       | -           | Filter by brand            |
| `categoryId` | string/int     | No       | -           | Filter by category         |

**Default Date Range**: Last 30 days if not provided

---

## 🔍 Key Features

### 1. **Dynamic Filtering**

- Supports filtering by Product, Brand, and Category
- Filters apply to: `total_stok`, `stock_in_volume`, `stock_out_volume`, `sales_velocity_data`
- `active_wo_count` typically NOT filtered (can be enabled if needed)

### 2. **Date Range Filtering**

- Applies to: `stock_in_volume`, `stock_out_volume`, `active_wo_count`, `sales_velocity_data`
- Does NOT apply to: `total_stok` (current stock is not date-dependent)

### 3. **Sales Velocity Calculation**

```sql
AVG(DATEDIFF(so.tanggal_keluar, si.tanggal_masuk))
```

- Calculates average days between stock-in and stock-out
- Lower value = faster selling product
- Includes `total_sold` for additional context

### 4. **Top 10 Limit**

- Sales Velocity now returns **Top 10 products** (updated from 5)
- Sorted by fastest selling (lowest avg_days_to_sell)

---

## 🛠️ Implementation Details

### Method Signature

```php
public function fetchWarehouseData()
```

### Helper Function

```php
$applyProductFilter = function ($builder, $productAlias, $joinProductIfNeeded)
    use ($productId, $brandId, $categoryId) {
    // Applies product/brand/category filters dynamically
};
```

### Database Tables Used

- `tb_products` - Product master data
- `tb_stock_in` - Stock-IN transactions
- `tb_stock_in_items` - Stock-IN line items
- `tb_stock_out` - Stock-OUT transactions (including Work Orders)
- `tb_stock_out_items` - Stock-OUT line items

---

## 📝 Usage Example

### JavaScript AJAX Call

```javascript
$.ajax({
    url: '<?= base_url('dashboard/fetchWarehouseData') ?>',
    type: 'POST',
    data: {
        startDate: '2024-01-01',
        endDate: '2024-01-31',
        productId: '',  // optional
        brandId: '',    // optional
        categoryId: ''  // optional
    },
    dataType: 'json',
    success: function(response) {
        if (response.success) {
            const warehouse = response.warehouse;

            // Update UI
            $('#total-stok').text(warehouse.total_stok.toLocaleString());
            $('#stock-in-volume').text(warehouse.stock_in_volume.toLocaleString());
            $('#stock-out-volume').text(warehouse.stock_out_volume.toLocaleString());
            $('#active-wo-count').text(warehouse.active_wo_count);

            // Render Sales Velocity Chart
            renderSalesVelocityChart(warehouse.sales_velocity_data);
        }
    },
    error: function(xhr, status, error) {
        console.error('Error fetching warehouse data:', error);
    }
});
```

---

## 🧪 Testing

### Test Endpoint

```bash
# Using curl
curl -X POST http://localhost/nsa/dashboard/fetchWarehouseData \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "startDate=2024-01-01&endDate=2024-01-31"
```

### Expected Response

```json
{
  "success": true,
  "warehouse": {
    "total_stok": 1500,
    "stock_in_volume": 250,
    "stock_out_volume": 180,
    "active_wo_count": 5,
    "sales_velocity_data": [...]
  }
}
```

### Test Cases

1. ✅ **Default Date Range**: No dates provided → should use last 30 days
2. ✅ **Custom Date Range**: Specific dates → should filter correctly
3. ✅ **Product Filter**: Filter by product ID → should return filtered data
4. ✅ **Brand Filter**: Filter by brand ID → should return filtered data
5. ✅ **Category Filter**: Filter by category ID → should return filtered data
6. ✅ **Combined Filters**: Multiple filters → should apply all filters
7. ✅ **Empty Results**: No data in range → should return zeros/empty arrays

---

## 🔐 Security

### Authentication

- Route protected with `auth` filter
- Only authenticated users can access

### Input Validation

- All inputs cast to appropriate types
- SQL injection prevented by Query Builder
- Date validation via PHP date functions

---

## 📊 Performance Considerations

### Optimizations

1. **Indexed Columns**: Ensure indexes on:

   - `tb_stock_in.tanggal_masuk`
   - `tb_stock_out.tanggal_keluar`
   - `tb_stock_out.status_work_order`
   - `tb_products.brand_id`
   - `tb_products.category_id`

2. **Query Efficiency**:

   - Uses `selectSum()` for aggregations
   - Uses `countAllResults()` for counts
   - Limits results to Top 10 for sales velocity

3. **Caching** (Future Enhancement):
   - Consider caching results for frequently accessed date ranges
   - Cache invalidation on stock transactions

---

## 🚀 Next Steps

### Frontend Integration (Part 2)

1. Create AJAX call to `fetchWarehouseData` endpoint
2. Update UI elements with returned data
3. Render Sales Velocity chart
4. Add loading states and error handling

### Future Enhancements

1. Add more warehouse metrics (turnover rate, stock aging, etc.)
2. Add export functionality (CSV, Excel)
3. Add real-time updates via WebSocket
4. Add caching layer for performance

---

## 📁 Files Modified

1. ✅ **app/Controllers/DashboardController.php**

   - Added `fetchWarehouseData()` method (lines 496-640)

2. ✅ **app/Config/Routes.php**
   - Added route: `POST dashboard/fetchWarehouseData`

---

## ✅ Checklist

- [x] Method `fetchWarehouseData()` created
- [x] Route added to Routes.php
- [x] All 5 data components implemented
- [x] Sales Velocity limit changed to Top 10
- [x] Dynamic filtering implemented
- [x] Date range filtering implemented
- [x] Response structure documented
- [x] Usage examples provided
- [x] Testing guide created

---

## 🎯 Summary

**Backend implementation COMPLETE!**

The `fetchWarehouseData` endpoint is now ready to provide structured warehouse data in JSON format. The endpoint returns:

1. ✅ `total_stok` - Current total stock
2. ✅ `stock_in_volume` - Total items received
3. ✅ `stock_out_volume` - Total items sold/issued
4. ✅ `active_wo_count` - Active work orders count
5. ✅ `sales_velocity_data` - Top 10 fastest selling products

**Endpoint**: `POST /dashboard/fetchWarehouseData`

**Ready for frontend integration!** 🚀
