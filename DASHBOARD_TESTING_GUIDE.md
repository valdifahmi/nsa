# DASHBOARD 3-SECTION - TESTING GUIDE

## 🎯 Quick Start Testing

### 1. Akses Dashboard

```
URL: http://localhost/nsa/dashboard
atau: http://localhost/xampp/htdocs/nsa/dashboard
```

### 2. Verifikasi Visual

Pastikan Anda melihat 3 section dengan warna berbeda:

- ✅ **WAREHOUSE INSIGHT** - Header biru dengan 2 tombol (Stock-In, Stock-Out)
- ✅ **WORKSHOP INSIGHT** - Header cyan dengan 1 tombol (Active Work Orders)
- ✅ **FINANCIAL INSIGHT** - Header hijau dengan 1 tombol (Daftar Invoice)

---

## 📊 Testing Checklist

### A. WAREHOUSE SECTION (Blue)

#### Cards:

- [ ] Stock-IN menampilkan angka (format: 1.500)
- [ ] Stock-Out menampilkan angka (format: 1.500)
- [ ] Total Stock menampilkan angka (format: 1.500)

#### Charts:

- [ ] Stock Overview chart muncul (area chart biru & merah)
- [ ] Proporsi Stock donut chart muncul
- [ ] Dropdown Product/Brand/Category berfungsi
- [ ] Top Products bar chart muncul (biru)
- [ ] Sales Velocity bar chart muncul (cyan)

#### Widget:

- [ ] "Hampir Habis" menampilkan produk dengan stok rendah
- [ ] Gambar produk muncul
- [ ] Link "View all" berfungsi

#### Quick Actions:

- [ ] Tombol [+] Stock-In → redirect ke `/purchase`
- [ ] Tombol [+] Stock-Out → redirect ke `/sale`

---

### B. WORKSHOP SECTION (Cyan)

#### Cards:

- [ ] Active Work Orders menampilkan angka (format: 15)
- [ ] Total Service Selesai menampilkan angka (format: 120)

#### Charts:

- [ ] Top Services by Frequency bar chart muncul (cyan)
- [ ] Chart menampilkan nama service dan frequency

#### Quick Actions:

- [ ] Tombol [>] Active Work Orders → redirect ke `/sale?status=Proses`

---

### C. FINANCIAL SECTION (Green)

#### Main Cards:

- [ ] Inventory Value menampilkan Rupiah (format: Rp 15.000.000)
- [ ] Purchase Value menampilkan Rupiah (format: Rp 10.000.000)
- [ ] Revenue menampilkan Rupiah (format: Rp 25.000.000)
- [ ] Gross Profit menampilkan Rupiah (format: Rp 5.000.000)

#### Tax Cards:

- [ ] PPN 11% menampilkan Rupiah (format: Rp 2.750.000)
- [ ] PPh 23 (2%) menampilkan Rupiah (format: Rp 100.000)

#### Charts:

- [ ] Parts vs Service Revenue donut chart muncul
- [ ] Profit vs Revenue Trend area chart muncul (hijau & ungu)
- [ ] Revenue by Category donut chart muncul
- [ ] Top 10 High Margin Products bar chart muncul (ungu)

#### Quick Actions:

- [ ] Tombol [i] Daftar Invoice → redirect ke `/invoice`

---

## 🔍 Data Verification

### Tax Calculations:

#### PPN 11% (Output Tax):

```
Example:
Total Revenue = Rp 10.000.000
PPN 11% = Rp 10.000.000 × 0.11 = Rp 1.100.000
Revenue with PPN = Rp 11.100.000
```

#### PPh 23 2% (Withholding Tax):

```
Example:
Service Revenue = Rp 5.000.000
PPh 23 2% = Rp 5.000.000 × 0.02 = Rp 100.000
Net Service Revenue = Rp 4.900.000
```

#### Total Revenue Calculation:

```
Parts Revenue = Rp 6.000.000
Service Revenue = Rp 5.000.000
PPh 23 (2%) = Rp 100.000

Total Revenue = Parts + (Service - PPh23)
Total Revenue = Rp 6.000.000 + Rp 4.900.000
Total Revenue = Rp 10.900.000
```

---

## 🧪 Filter Testing

### Test Scenarios:

#### 1. Date Range Filter:

```
1. Set Start Date: 2024-01-01
2. Set End Date: 2024-01-31
3. Click "Apply"
4. Verify all data updates
```

#### 2. Product Filter:

```
1. Select specific product
2. Click "Apply"
3. Verify only that product's data shows
```

#### 3. Brand Filter:

```
1. Select specific brand
2. Click "Apply"
3. Verify only that brand's products show
```

#### 4. Category Filter:

```
1. Select specific category
2. Click "Apply"
3. Verify only that category's products show
```

#### 5. Reset Filter:

```
1. Click "Reset" button
2. Verify filters clear
3. Verify date resets to last 30 days
4. Verify data reloads
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Charts tidak muncul

**Solution:**

- Buka browser console (F12)
- Check for JavaScript errors
- Verify Morris.js dan Raphael.js loaded
- Check network tab for AJAX errors

### Issue 2: Data tidak update

**Solution:**

- Check browser console for AJAX errors
- Verify endpoint `/dashboard/fetchData` returns 200 OK
- Check JSON response structure
- Verify database has data

### Issue 3: Format angka salah

**Solution:**

- Warehouse values harus integer (1.500)
- Financial values harus Rupiah (Rp 1.500.000)
- Check JavaScript functions: `numberFormat()` dan `formatRupiah()`

### Issue 4: Quick Action buttons tidak berfungsi

**Solution:**

- Check href attributes in HTML
- Verify routes exist in `app/Config/Routes.php`
- Check browser console for navigation errors

---

## 📱 Responsive Testing

### Desktop (1920x1080):

- [ ] All 3 sections visible
- [ ] Charts render properly
- [ ] Cards aligned correctly

### Tablet (768x1024):

- [ ] Sections stack vertically
- [ ] Charts resize properly
- [ ] Cards maintain layout

### Mobile (375x667):

- [ ] Sections stack vertically
- [ ] Charts are scrollable
- [ ] Quick Action buttons visible

---

## 🔗 API Endpoint Testing

### Test with cURL:

```bash
curl -X POST http://localhost/nsa/dashboard/fetchData \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "startDate=2024-01-01&endDate=2024-01-31"
```

### Expected Response:

```json
{
  "warehouse": {...},
  "workshop": {...},
  "financial": {...}
}
```

### Verify Response Contains:

- [ ] `warehouse.cards` object
- [ ] `warehouse.overview_chart` array
- [ ] `workshop.cards` object
- [ ] `workshop.top_services_chart` array
- [ ] `financial.cards` object with tax data
- [ ] `financial.parts_vs_service_chart` array

---

## ✅ Success Criteria

Dashboard implementation dianggap berhasil jika:

1. ✅ **Visual**: 3 sections dengan warna berbeda terlihat jelas
2. ✅ **Data**: Semua cards menampilkan data dengan format yang benar
3. ✅ **Charts**: Semua charts render tanpa error
4. ✅ **Navigation**: Quick Action buttons berfungsi dengan benar
5. ✅ **Filters**: Filter date/product/brand/category berfungsi
6. ✅ **Tax**: PPN 11% dan PPh 23 2% dihitung dengan benar
7. ✅ **Responsive**: Layout responsive di berbagai ukuran layar
8. ✅ **Performance**: Dashboard load dalam < 3 detik

---

## 📞 Support

Jika menemukan masalah:

1. Check browser console (F12) untuk JavaScript errors
2. Check network tab untuk AJAX errors
3. Verify database memiliki data untuk testing
4. Check file backups jika perlu rollback:
   - `app/Controllers/DashboardController.php.backup`
   - `app/Views/Dashboard/index.php.backup`

---

## 🎉 Completion

Setelah semua testing checklist di atas passed, implementasi 3-Section Dashboard dianggap **COMPLETE** dan siap untuk production use.

**Happy Testing!** 🚀
