# 🚀 Quick Start Guide - Admin Dashboard JJHouse

## 🔐 Login Admin

**URL**: `http://localhost:8000/admin`  
**Email**: `admin@jjhouse.com`  
**Password**: `password`

---

## 📊 Dashboard Overview

### Apa yang Anda Lihat

```
┌─────────────────────────────────────────────────────────────┐
│                        DASHBOARD ADMIN                      │
└─────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┬──────────────┐
│   💰 Rp      │   🔴 Rp      │   🟢 Rp      │   📊 Rp     │
│  PENJUALAN   │    HPP       │  KEUNTUNGAN  │  BULAN INI   │
│ 1,500,000    │  500,000     │   1,000,000  │  300,000     │
│ Semua trans  │  Biaya prod  │  Margin 200% │  3 Pesanan   │
└──────────────┴──────────────┴──────────────┴──────────────┘

┌─────────────────────────┬──────────────────────────┐
│   GRAFIK PENJUALAN      │   GRAFIK KEUNTUNGAN      │
│   6 BULAN TERAKHIR      │   6 BULAN TERAKHIR       │
│                         │                          │
│   Line Chart ↗️         │   Line Chart ↗️          │
│   (Trending Up)         │   (Trending Up)          │
└─────────────────────────┴──────────────────────────┘

┌──────────────────────┬───────────────────────────┐
│  PRODUK TERLARIS     │   RINGKASAN KUNCI         │
│                      │                           │
│  • Product A - 45    │  📦 Total Pesanan: 150    │
│  • Product B - 32    │  👥 Total Pelanggan: 45   │
│  • Product C - 28    │  ⭐ Baru (Bulan ini): 5   │
│  • Product D - 20    │  ⏳ Pre-order Aktif: 8    │
│  • Product E - 18    │  📈 Total Produk: 25      │
└──────────────────────┴───────────────────────────┘

┌─────────────────────────────────────────────────┐
│  📌 KELOLA PRODUK FEATURED DI HOME               │
│  [Search Box]  [Selected: 4/8]                   │
│  ┌─────────┬─────────┬─────────┬─────────┐      │
│  │ Produk 1│ Produk 2│ Produk 3│ Produk 4│      │
│  │  ✅     │  ✅     │  ✅     │  ✅     │      │
│  └─────────┴─────────┴─────────┴─────────┘      │
│  [Bersihkan Semua] [Simpan Perubahan]           │
└─────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────┐
│  PESANAN TERBARU                                 │
│  ┌────────┬──────────┬────────┬────────┬─────┐  │
│  │ NO OPT │ PELANGGAN│ TOTAL  │ STATUS │ AKSI│  │
│  ├────────┼──────────┼────────┼────────┼─────┤  │
│  │ ORD001 │ Budi     │ Rp 1.5M│ Paid   │View │  │
│  │ ORD002 │ Siti     │ Rp 800K│ Shipped│View │  │
│  │ ORD003 │ Ahmad    │ Rp 2.3M│ Pending│View │  │
│  └────────┴──────────┴────────┴────────┴─────┘  │
└──────────────────────────────────────────────────┘
```

---

## 📦 Kelola Produk

### Menu: Admin → Products

#### List Produk
```
┌───┬──────────────┬──────────┬────────┬──────┬──────────┐
│IMG│ NAMA PRODUK  │ KATEGORI │ HARGA  │ HPP  │ MARGIN   │
├───┼──────────────┼──────────┼────────┼──────┼──────────┤
│[📷]│ Kaos Putih   │ Clothing │ Rp 150K│ 50K  │ Rp 100K  │
│   │              │          │        │      │ (200%)   │
├───┼──────────────┼──────────┼────────┼──────┼──────────┤
│[📷]│ Hoodie       │ Clothing │ Rp 350K│ 120K │ Rp 230K  │
│   │              │          │        │      │ (192%)   │
├───┼──────────────┼──────────┼────────┼──────┼──────────┤
│[📷]│ Jeans        │ Clothing │ Rp 450K│ 150K │ Rp 300K  │
│   │              │          │        │      │ (200%)   │
└───┴──────────────┴──────────┴────────┴──────┴──────────┘
```

### Tambah Produk Baru

1. **Klik**: "Add Product"
2. **Isi Form:**
   - Nama Produk: "Kaos Premium Katun"
   - Kategori: "Clothing"
   - Deskripsi: "Kaos premium dari bahan katun 100%..."
   - **Harga Jual**: 200,000
   - **HPP (Cost Price)**: 70,000
   - Sizes: S, M, L, XL
   - Stock per Size: 10, 15, 10, 5
   - Primary Image: [Upload gambar]
   - Status: ✅ Active (Aktif)
3. **Klik**: "Create Product"

**Hasil**: 
- Keuntungan otomatis terhitung: Rp 130,000
- Margin: (130,000 / 70,000) × 100 = 186%
- Produk tampil di list dengan profit info

### Edit Produk

1. **Klik**: Icon Edit (✏️) di daftar
2. **Update**: Harga atau HPP
3. **Lihat**: Keuntungan otomatis update
4. **Klik**: "Update Product"

---

## 🏠 Manage Featured Products di Home

### Langkah-langkah:

#### 1. Buka Dashboard
- URL: `/admin`
- Scroll ke bawah → "📌 Kelola Produk Featured di Home"

#### 2. Cari Produk
```
[🔍 Cari produk...]
    ↓
Hasil pencarian otomatis tampil
```

#### 3. Pilih Produk
- Click pada produk di kolom "Produk Tersedia"
- Produk akan pindah ke "Produk Yang Dipilih"
- Max 8 produk (akan alert jika lebih)

#### 4. Lihat Pilihan
```
PRODUK YANG DIPILIH: 4/8

┌─────────┬─────────┬─────────┬─────────┐
│Kaos 1   │Hoodie   │Jeans    │Jaket    │
│ ✅      │ ✅      │ ✅      │ ✅      │
└─────────┴─────────┴─────────┴─────────┘

Harga: Rp150K  Harga: Rp350K  Harga: Rp450K  Harga: Rp600K
```

#### 5. Simpan
- **Klik**: "Simpan Perubahan" button
- **Result**: Produk akan tampil di halaman home

#### 6. Clear All (Opsional)
- **Klik**: "Bersihkan Semua" untuk remove semua
- **Result**: Semua featured products dihapus

---

## 💰 Memahami HPP & Keuntungan

### Contoh Perhitungan:

**Produk: Kaos Putih**
```
Harga Jual (Selling Price)    : Rp 150,000
HPP (Cost Price)              : Rp 50,000
                                ───────────
Keuntungan (Profit)           : Rp 100,000

Margin Percentage             : (100,000 / 50,000) × 100
                              = 200%
```

**Produk: Hoodie**
```
Harga Jual                    : Rp 350,000
HPP                           : Rp 120,000
                                ───────────
Keuntungan                    : Rp 230,000

Margin                        : (230,000 / 120,000) × 100
                              = 192%
```

### Interpretasi:
- **Margin 200%** = Untuk setiap Rp 1 HPP, dapat Rp 2 keuntungan
- **Margin 192%** = Untuk setiap Rp 1 HPP, dapat Rp 1.92 keuntungan
- **Margin tinggi** = Profit margin bagus
- **Margin rendah** = Perlu review harga

---

## 📊 Analisis Data

### Menggunakan Dashboard untuk Business Insights:

#### 1. Trend Penjualan
- Lihat **Grafik Penjualan 6 Bulan**
- Cek apakah naik atau turun
- Identifikasi bulan terbaik

#### 2. Trend Keuntungan
- Lihat **Grafik Keuntungan 6 Bulan**
- Bandingkan dengan trend penjualan
- Jika keuntungan ↓ tapi penjualan ↑ = mungkin perlu naik harga

#### 3. Produk Terlaris
- Lihat **Chart Produk Terlaris**
- Fokus pada top 5
- Stock produk terlaris jangan sampai habis

#### 4. Performance Bulan Ini
- Bandingkan dengan bulan sebelumnya
- Set target untuk bulan depan
- Monitor pelanggan baru

---

## 📋 Pesanan & Pembayaran

### View Pesanan Terbaru
```
Tabel di dashboard menampilkan:
- Nomor Order
- Nama Pelanggan
- Total (termasuk ongkir)
- Status Order (Pending/Paid/Shipped/Completed)
- Status Pembayaran (Unpaid/DP/Paid/Refunded)
- Tanggal Order
- Button "Lihat" untuk detail
```

### Status Order:
- 🟡 **Menunggu Pembayaran** = Customer belum transfer
- 🔵 **Dibayar** = Pembayaran masuk, siap packing
- 🔷 **Diproses** = Sedang dikemas/disiapkan
- 🟣 **Dikirim** = Sudah kirim ke kurir
- 🟢 **Selesai** = Diterima customer
- 🔴 **Dibatalkan** = Order dibatalkan

---

## 🎯 Best Practices

### 1. Daily Routine
- ✅ Cek dashboard setiap pagi
- ✅ Monitor pesanan baru
- ✅ Lihat stock produk terlaris
- ✅ Update featured products seminggu sekali

### 2. Pricing Strategy
- ✅ Selalu set HPP untuk setiap produk
- ✅ Target margin minimal 150-200%
- ✅ Adjust harga berdasarkan demand
- ✅ Check kompetitor secara berkala

### 3. Product Management
- ✅ Update stock secara real-time
- ✅ Featured products untuk marketing
- ✅ Pre-order untuk produk limited
- ✅ Hapus produk tidak laku

### 4. Featured Strategy
- ✅ Rotation produk setiap minggu
- ✅ Featured produk terlaris
- ✅ Featured new products
- ✅ Featured high-margin products
- ✅ Max 8 di home page

---

## 🆘 Troubleshooting

### Grafik Tidak Muncul?
```
❌ Problem: Chart blank/tidak ada data
✅ Solution:
  1. Refresh halaman (F5)
  2. Clear browser cache (Ctrl+Shift+Del)
  3. Check browser console (F12)
  4. Pastikan Chart.js CDN accessible
```

### HPP Tidak Terhitung?
```
❌ Problem: Keuntungan = "-"
✅ Solution:
  1. Pastikan HPP diisi saat add/edit produk
  2. Reload halaman
  3. Check database migration ran
```

### Featured Products Tidak Simpan?
```
❌ Problem: Produk pilihan hilang setelah simpan
✅ Solution:
  1. Check browser console untuk error
  2. Pastikan Livewire working
  3. Reload dashboard
  4. Try lagi simpan
```

### Database Error?
```
✅ Solution:
  1. Run: php artisan migrate
  2. Run: php artisan cache:clear
  3. Run: php artisan config:cache
```

---

## 📚 Menu Navigation

```
Admin Dashboard (/)
│
├── Products (admin/products)
│   ├── List (index)
│   ├── Create (create)
│   └── Edit (edit/{id})
│
├── Orders (admin/orders)
│   ├── List (index)
│   └── Detail (show/{id})
│
├── Blog (admin/blog)
│   ├── Posts
│   ├── Categories
│   └── Comments
│
├── Pre-orders (admin/preorders)
│
├── Instagram (admin/instagram)
│
└── Settings (admin/settings)
```

---

## 🔑 Key Features Summary

| Feature | Status | Benefit |
|---------|--------|---------|
| HPP Tracking | ✅ | Know exact profit per product |
| Profit Calculation | ✅ | Auto-calculate margin |
| Featured Management | ✅ | Control home page display |
| Sales Analytics | ✅ | 6-month trend analysis |
| Top Products | ✅ | Identify best sellers |
| Monthly Reports | ✅ | Compare performance |
| Order Management | ✅ | Track customer orders |
| Responsive Design | ✅ | Works on mobile/tablet |

---

## ✨ Tips & Tricks

1. **Bulk Update Prices**: Edit products satu-satu (atau develop bulk edit feature)
2. **Monitor Margin**: Jika margin < 150%, pertimbangkan naik harga
3. **Seasonal Strategy**: Update featured products sesuai musim
4. **Stock Management**: Jangan stockout produk terlaris
5. **Customer Segmentation**: Lihat pattern pembelian dari orders
6. **Competitor Analysis**: Update harga jika kompetitor lebih murah
7. **Data Backup**: Backup database secara berkala

---

## 📞 Need Help?

1. **Baca**: ADMIN_GUIDE.md
2. **Check**: Browser console (F12) untuk errors
3. **Verify**: Database migrations ran successfully
4. **Clear**: Cache: `php artisan cache:clear`
5. **Restart**: Development server

---

**Happy Selling! 🚀**

Created: 2026-04-24  
Last Updated: 2026-04-24
