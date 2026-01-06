# DASHBOARD WORKSHOP DATA - BACKEND IMPLEMENTATION

## 📋 Overview

Implementasi backend untuk menyediakan data workshop dalam format JSON yang terstruktur. Method `fetchWorkshopData` mengembalikan data khusus untuk Section Workshop dengan 5 komponen utama.

---

## ✅ Implementation Complete

### **New Method: `fetchWorkshopData()`**

**Location**: `app/Controllers/DashboardController.php`

**Endpoint**: `POST /dashboard/fetchWorkshopData`

**Purpose**: Mengembalikan data workshop dalam format JSON terstruktur

---

## 📊 Response Structure

```json
{
  "success": true,
  "workshop": {
    "active_wo_count": 5,
    "total_service_done": 25,
    "total_service_nominal": 15000000,
    "average_service_value": 600000,
    "top_services_data": [
      {
        "service_id": 1,
        "service_name": "Ganti Oli",
        "frequency": 45,
        "total_revenue": 4500000,
        "avg_price": 100000
      }
      // ... 9 more services (Top 10)
    ]
  }
}
```

---

## 🔧 Data Components

### 1. **active_wo_count** (Integer)

- **Description**: Count of active work orders (status_work_order = 'Proses')
- **Query**: `COUNT(*)` from `tb_stock_out`
- **Condition**: `status_work_order = 'Proses'`
- **Date Range**: Filtered by `tanggal_keluar` (startDate to endDate)
- **Filters**: Typically NOT filtered by product/brand/category
- **Format**: Integer (e.g., 5)

**SQL Logic**:

```php
$bActiveWO = $this->db->table('tb_stock_out so')
    ->where('so.status_work_order', 'Proses')
    ->where('so.tanggal_keluar >=', $startDT)
    ->where('so.tanggal_keluar <=', $endDT)
    ->countAllResults();
```

---

### 2. **total_service_done** (Integer)

- **Description**: Count of completed workshop services
- **Query**: `COUNT(*)` from `tb_stock_out`
- **Conditions**:
  - `tipe_transaksi = 'Workshop'`
  - `status_work_order = 'Selesai'`
- **Date Range**: Filtered by `tanggal_keluar` (startDate to endDate)
- **Format**: Integer (e.g., 25)

**SQL Logic**:

```php
$bServiceDone = $this->db->table('tb_stock_out so')
    ->where('so.tipe_transaksi', 'Workshop')
    ->where('so.status_work_order', 'Selesai')
    ->where('so.tanggal_keluar >=', $startDT)
    ->where('so.tanggal_keluar <=', $endDT)
    ->countAllResults();
```

---

### 3. **total_service_nominal** (Float)

- **Description**: Total revenue from completed services
- **Query**: `SUM(sos.biaya_jasa * sos.jumlah)` from `tb_stock_out_services`
- **Condition**: `status_work_order = 'Selesai'`
- **Date Range**: Filtered by `tanggal_keluar` (startDate to endDate)
- **Format**: Float (e.g., 15000000.00)

**SQL Logic**:

```php
$bServiceNominal = $this->db->table('tb_stock_out_services sos')
    ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
    ->select('SUM(sos.biaya_jasa * sos.jumlah) AS total_nominal', false)
    ->where('so.status_work_order', 'Selesai')
    ->where('so.tanggal_keluar >=', $startDT)
    ->where('so.tanggal_keluar <=', $endDT)
    ->get()->getRowArray();

$totalServiceNominal = (float) ($rowServiceNominal['total_nominal'] ?? 0);
```

---

### 4. **average_service_value** (Float)

- **Description**: Average revenue per completed service
- **Calculation**: `total_service_nominal / total_service_done`
- **Fallback**: Returns 0 if `total_service_done = 0` (prevent division by zero)
- **Format**: Float (e.g., 600000.00)

**PHP Logic**:

```php
$averageServiceValue = ($totalServiceDone > 0)
    ? ($totalServiceNominal / $totalServiceDone)
    : 0;
```

---

### 5. **top_services_data** (Array of Objects)

- **Description**: Top 10 services by frequency (most used)
- **Query**: Complex query with aggregations
- **Metrics**:
  - `service_id`: ID of the service
  - `service_name`: Name of the service
  - `frequency`: How many times service was used
  - `total_revenue`: Total revenue from this service
  - `avg_price`: Average price per service instance
- **Sorting**: DESC by `frequency` (most frequent first)
- **Limit**: **Top 10 services**
- **Format**: Array of objects

**SQL Logic**:

```php
$bTopServices = $this->db->table('tb_stock_out_services sos')
    ->join('tb_stock_out so', 'so.id = sos.stock_out_id', 'inner')
    ->join('tb_services s', 's.id = sos.service_id', 'inner')
    ->select('s.id AS service_id')
    ->select('s.nama_service AS service_name')
    ->select('COUNT(sos.id) AS frequency', false)
    ->select('SUM(sos.biaya_jasa * sos.jumlah) AS total_revenue', false)
    ->select('AVG(sos.biaya_jasa) AS avg_price', false)
    ->where('so.tanggal_keluar >=', $startDT)
    ->where('so.tanggal_keluar <=', $endDT)
    ->groupBy('s.id')
    ->orderBy('frequency', 'DESC')
    ->limit(10)
    ->get()->getResultArray();
```

---

## 📥 Request Parameters

All parameters are sent via POST:

| Parameter    | Type           | Required | Default     | Description                                                  |
| ------------ | -------------- | -------- | ----------- | ------------------------------------------------------------ |
| `startDate`  | string (Y-m-d) | No       | 30 days ago | Start date for filtering                                     |
| `endDate`    | string (Y-m-d) | No       | Today       | End date for filtering                                       |
| `productId`  | string/int     | No       | -           | Filter by specific product (not typically used for workshop) |
| `brandId`    | string/int     | No       | -           | Filter by brand (not typically used for workshop)            |
| `categoryId` | string/int     | No       | -           | Filter by category (not typically used for workshop)         |

**Default Date Range**: Last 30 days if not provided

**Note**: Product/Brand/Category filters are available but typically not applied to workshop data since it's service-focused, not product-focused.

---

## 🔍 Key Features

### 1. **Date Range Filtering**

- Applies to ALL workshop metrics
- Uses `tanggal_keluar` from `tb_stock_out`
- Default: Last 30 days

### 2. **Status-Based Filtering**

- **Active WO**: `status_work_order = 'Proses'`
- **Completed Services**: `status_work_order = 'Selesai'`
- **Workshop Type**: `tipe_transaksi = 'Workshop'`

### 3. **Aggregation Calculations**

- **Count**: Active WO, Completed Services
- **Sum**: Total Service Nominal
- **Average**: Average Service Value, Average Price per Service
- **Frequency**: Service usage count

### 4. **Top 10 Services**

- Sorted by frequency (most used first)
- Includes revenue and pricing metrics
- Useful for identifying popular services

---

## 🛠️ Implementation Details

### Method Signature

```php
public function fetchWorkshopData()
```

### Database Tables Used

- `tb_stock_out` - Work order transactions
- `tb_stock_out_services` - Service line items
- `tb_services` - Service master data

### Key Columns

- `tb_stock_out.status_work_order` - Work order status ('Proses', 'Selesai')
- `tb_stock_out.tipe_transaksi` - Transaction type ('Workshop', 'Sales')
- `tb_stock_out.tanggal_keluar` - Transaction date
- `tb_stock_out_services.biaya_jasa` - Service fee
- `tb_stock_out_services.jumlah` - Service quantity

---

## 📝 Usage Example

### JavaScript AJAX Call

```javascript
$.ajax({
    url: '<?= base_url('dashboard/fetchWorkshopData') ?>',
    type: 'POST',
    data: {
        startDate: '2024-01-01',
        endDate: '2024-01-31'
    },
    dataType: 'json',
    success: function(response) {
        if (response.success) {
            const workshop = response.workshop;

            // Update UI
            $('#active-wo-count').text(workshop.active_wo_count);
            $('#total-service-done').text(workshop.total_service_done);
            $('#total-service-nominal').text(formatRupiah(workshop.total_service_nominal));
            $('#average-service-value').text(formatRupiah(workshop.average_service_value));

            // Render Top Services Chart
            renderTopServicesChart(workshop.top_services_data);
        }
    },
    error: function(xhr, status, error) {
        console.error('Error fetching workshop data:', error);
    }
});
```

---

## 🧪 Testing

### Test Endpoint

```bash
# Using curl
curl -X POST http://localhost/nsa/dashboard/fetchWorkshopData \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "startDate=2024-01-01&endDate=2024-01-31"
```

### Expected Response

```json
{
  "success": true,
  "workshop": {
    "active_wo_count": 5,
    "total_service_done": 25,
    "total_service_nominal": 15000000,
    "average_service_value": 600000,
    "top_services_data": [
      {
        "service_id": 1,
        "service_name": "Ganti Oli",
        "frequency": 45,
        "total_revenue": 4500000,
        "avg_price": 100000
      }
    ]
  }
}
```

### Test Cases

1. ✅ **Default Date Range**: No dates provided → should use last 30 days
2. ✅ **Custom Date Range**: Specific dates → should filter correctly
3. ✅ **No Active WO**: Should return 0
4. ✅ **No Completed Services**: Should return 0 for all service metrics
5. ✅ **Division by Zero**: average_service_value should be 0 if no services done
6. ✅ **Empty Top Services**: Should return empty array if no services
7. ✅ **Multiple Services**: Should return up to 10 services sorted by frequency

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

## 📊 Business Logic

### Work Order Status Flow

```
New WO Created
    ↓
status_work_order = 'Proses'  ← Counted in active_wo_count
    ↓
Work Completed
    ↓
status_work_order = 'Selesai'  ← Counted in total_service_done
    ↓
Included in total_service_nominal
```

### Service Revenue Calculation

```
Service Fee (biaya_jasa) × Quantity (jumlah) = Service Revenue
    ↓
SUM(all services) = total_service_nominal
    ↓
total_service_nominal / total_service_done = average_service_value
```

### Top Services Ranking

```
For each service:
    COUNT(usage) = frequency
    SUM(revenue) = total_revenue
    AVG(price) = avg_price
    ↓
Sort by frequency DESC
    ↓
Take Top 10
```

---

## 💡 Use Cases

### 1. **Workshop Performance Monitoring**

- Track active work orders
- Monitor completion rate
- Analyze service revenue

### 2. **Service Popularity Analysis**

- Identify most requested services
- Understand customer preferences
- Plan resource allocation

### 3. **Revenue Insights**

- Total service revenue
- Average revenue per service
- Revenue by service type

### 4. **Operational Metrics**

- Work order throughput
- Service completion tracking
- Workload management

---

## 📁 Files Modified

1. ✅ **app/Controllers/DashboardController.php**

   - Added `fetchWorkshopData()` method (lines 643-739)

2. ✅ **app/Config/Routes.php**
   - Added route: `POST dashboard/fetchWorkshopData`

---

## ✅ Checklist

- [x] Method `fetchWorkshopData()` created
- [x] Route added to Routes.php
- [x] All 5 data components implemented
- [x] Active WO count query (status_work_order = 'Proses')
- [x] Total service done query (tipe 'Workshop' + status 'Selesai')
- [x] Total service nominal calculation
- [x] Average service value calculation (with division by zero protection)
- [x] Top 10 services by frequency
- [x] Date range filtering implemented
- [x] Response structure documented
- [x] Usage examples provided
- [x] Testing guide created

---

## 🎯 Summary

**Backend implementation COMPLETE!**

The `fetchWorkshopData` endpoint is now ready to provide structured workshop data in JSON format. The endpoint returns:

1. ✅ `active_wo_count` - Active work orders (status = 'Proses')
2. ✅ `total_service_done` - Completed workshop services
3. ✅ `total_service_nominal` - Total service revenue
4. ✅ `average_service_value` - Average revenue per service
5. ✅ `top_services_data` - Top 10 most frequent services

**Endpoint**: `POST /dashboard/fetchWorkshopData`

**Ready for frontend integration!** 🚀

---

## 📚 Related Documentation

- **DASHBOARD_WAREHOUSE_DATA_BACKEND.md** - Warehouse data backend guide
- **DASHBOARD_WAREHOUSE_UI_FRONTEND.md** - Warehouse UI frontend guide
- **DASHBOARD_3SECTION_ROLLBACK.md** - Previous rollback report

---

## 🚀 Next Steps

### Frontend Integration (Part 2)

1. Create Workshop section UI in dashboard
2. Add Quick Action button ([>] Active Work Orders)
3. Create AJAX call to `fetchWorkshopData` endpoint
4. Display workshop cards (Active WO, Total Services, etc.)
5. Render Top Services chart
6. Add loading states and error handling
7. Format numbers appropriately (integers for counts, Rupiah for money)

**Workshop backend is ready! Proceed to frontend implementation.** ✅
