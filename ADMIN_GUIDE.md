# 📊 Admin Dashboard - Fitur dan Panduan

Sistem admin dashboard JJHouse menyediakan berbagai fitur untuk mengelola toko online Anda secara lengkap.

## 🔐 Akses Admin

- **Email Login**: `admin@jjhouse.com`
- **Password**: `password`
- **URL**: `http://localhost:8000/admin`

> ⚠️ **Penting**: Ubah password admin di production environment!

---

## 📈 Dashboard Utama

### Statistik Utama

Dashboard menampilkan 4 statistik kunci:

1. **Total Penjualan** - Jumlah semua transaksi yang sudah dibayar (blue)
2. **Total HPP** - Total Harga Pokok Penjualan (cost price) dari semua penjualan (red)
3. **Total Keuntungan** - Selisih antara Penjualan dan HPP dengan margin percentage (green)
4. **Penjualan Bulan Ini** - Penjualan spesifik bulan berjalan (purple)

### Grafik dan Chart

#### 📊 Grafik Penjualan 6 Bulan Terakhir
- Menampilkan tren pendapatan total
- Menggunakan Chart.js
- Format mata uang: Rp

#### 📊 Grafik Keuntungan 6 Bulan Terakhir
- Menampilkan keuntungan bersih (Penjualan - HPP)
- Membantu analisis tren profit
- Format mata uang: Rp

#### 📊 Produk Terlaris
- Bar chart horizontal dari top 5 produk paling banyak terjual
- Menampilkan jumlah unit yang terjual

### Ringkasan Kunci
- Total Pesanan
- Total Pelanggan
- Pelanggan Baru (Bulan Ini)
- Pre-order Aktif
- Total Produk

---

## 🏠 Kelola Produk Featured di Home

Fitur khusus untuk mengelola produk mana saja yang ditampilkan di halaman home.

### Cara Menggunakan:

1. **Buka Dashboard Admin** → scroll ke bagian "Kelola Produk Featured di Home"
2. **Cari Produk** menggunakan search box
3. **Pilih Produk** dari kolom "Produk Tersedia" 
   - Maksimal 8 produk dapat ditampilkan
4. **Lihat Pilihan** di bagian "Produk Yang Dipilih"
5. **Simpan Perubahan** dengan klik tombol "Simpan Perubahan"

### Status Indikator:
- ✅ Centang biru = Produk dipilih untuk home featured
- Produk yang dipilih akan ditampilkan dengan border biru
- Jumlah produk yang dipilih ditampilkan di atas: `X/8 Dipilih`

---

## 💰 Manajemen Harga dan HPP

### Memahami HPP (Harga Pokok Penjualan)

**HPP** adalah biaya produksi atau pembelian produk dari supplier.

**Rumus Keuntungan:**
```
Keuntungan = Harga Jual - HPP
Margin % = (Keuntungan / HPP) × 100%
```

### Menambah/Edit Produk dengan HPP

#### Saat Create Produk:
1. Buka **Admin → Products → Add Product**
2. Isi **Harga Jual** di field "Harga Jual"
3. Isi **HPP** di field "HPP (Cost Price)" *(opsional)*
4. Simpan produk
5. Di halaman index, Anda akan melihat keuntungan otomatis terhitung

#### Saat Edit Produk:
1. Buka **Admin → Products → Edit**
2. Update **Harga Jual** dan **HPP**
3. Lihat kalkulasi keuntungan real-time
4. Simpan perubahan

### Kolom di Daftar Produk:
- **Harga Jual** (biru) - Harga yang dijual ke customer
- **HPP** (merah) - Biaya produksi/pembelian
- **Keuntungan** (hijau) - Profit + margin percentage

---

## 📦 Manajemen Produk

### CRUD Produk

**CREATE** (Tambah Produk)
- Menu: Admin → Products → Add Product
- Field yang harus diisi:
  - Product Name *
  - Category *
  - Full Description *
  - Harga Jual *
  - HPP (opsional)
  - Sizes & Stock *
  - Primary Image *
  - Status (Aktif/Tidak Aktif)

**READ** (Lihat Daftar Produk)
- Menu: Admin → Products
- Menampilkan: Gambar, Nama, Kategori, Harga, HPP, Keuntungan, Stock, Status
- Pagination: 15 produk per halaman

**UPDATE** (Edit Produk)
- Klik icon Edit (pensil) di daftar produk
- Update informasi yang diperlukan
- Save changes

**DELETE** (Hapus Produk)
- Klik icon Delete (tempat sampah)
- Konfirmasi: "Hapus produk ini?"
- Produk akan dihapus permanen

### Fitur Produk Lanjutan

**Pre-order Management**
- Centang "This is a pre-order product"
- Set Pre-order End Date
- Set Estimated Days (estimasi hari pengiriman)

**Sizes & Stock**
- Tambah multiple sizes (XS, S, M, L, XL, XXL)
- Set stock untuk setiap size
- Total stock otomatis terhitung

**Product Images**
- Primary Image: Gambar utama produk
- Additional Images: Gambar tambahan
- Lihat preview di halaman edit

**Product Status**
- ✅ Aktif = Terlihat di toko
- ✅ Featured = Produk unggulan
- 📌 Featured Home = Ditampilkan di halaman home (dikelola via dashboard)

---

## 📋 Manajemen Pesanan

**Menu**: Admin → Orders

### Fitur:
- Lihat semua pesanan
- Filter berdasarkan status
- Lihat detail pesanan
- Update status pesanan
- Track pembayaran

### Status Pesanan:
- 🟡 Menunggu Pembayaran - Pesanan dibuat, menunggu pembayaran
- 🔵 Dibayar - Pembayaran berhasil
- 🔷 Diproses - Sedang dipacking
- 🟣 Dikirim - Pesanan dikirim
- 🟢 Selesai - Pesanan diterima
- 🔴 Dibatalkan - Pesanan dibatalkan

### Status Pembayaran:
- 🔴 Belum Dibayar (Unpaid)
- 🟡 DP 50% - Down Payment 50%
- 🟢 Lunas - Fully Paid
- 🟠 Dikembalikan - Refunded
- 🔴 Gagal - Failed

---

## 📊 Analytics & Reports

### Data yang Tersedia di Dashboard:

1. **Total Sales** - Sum dari semua order dengan payment_status = 'paid'
2. **Total Cost** - Sum dari (product cost_price × quantity) untuk semua items
3. **Total Profit** - Total Sales - Total Cost
4. **Monthly Sales** - Penjualan bulan berjalan
5. **Monthly Cost** - HPP bulan berjalan
6. **Monthly Profit** - Keuntungan bulan berjalan
7. **Recent Orders** - 10 pesanan terbaru

### Grafik Data:
- Last 6 months sales trend
- Last 6 months profit trend
- Top 5 best-selling products

---

## 🎨 Fitur Tambahan

### Pre-order Management
- Kelola pre-order aktif
- Set estimasi pengiriman
- Monitor pembayaran DP/Lunas

### Blog & Artikel
- Kelola blog posts
- Manage categories
- Moderate comments

### Instagram Feed
- Manage Instagram integration
- Display feed otomatis

### Settings
- Konfigurasi toko
- Manage payment methods
- Shipping settings

---

## 💡 Tips & Best Practices

1. **Selalu Set HPP** untuk mengetahui profit margin produk Anda
2. **Update Featured Products** secara rutin untuk strategi marketing
3. **Monitor Dashboard** setiap hari untuk melihat performa penjualan
4. **Check Pre-orders** untuk memastikan estimasi pengiriman terpenuhi
5. **Backup Data** secara berkala

---

## 🔧 Technical Details

### Database Columns:

**Products Table:**
```sql
- cost_price (decimal 12,2) - HPP Produk
- is_featured_home (boolean) - Featured di Home
```

**Models Used:**
- `App\Models\Product` - Product model dengan profit calculations
- `App\Models\Order` - Order model
- `App\Models\OrderItem` - Order items dengan profit tracking
- `App\Livewire\Admin\ManageFeaturedProducts` - Livewire component

### Key Methods:

**Product Model:**
```php
$product->profit              // Keuntungan (price - cost_price)
$product->profit_margin_percent // Margin %
$product->formatted_profit    // Format Rp
```

---

## 📞 Troubleshooting

### Chart Tidak Tampil?
- Pastikan Chart.js CDN terbuka di browser
- Clear cache browser (Ctrl+Shift+Delete)
- Check console untuk error messages

### HPP Tidak Terhitung?
- Pastikan cost_price sudah diisi saat create/edit produk
- Jika cost_price = 0 atau null, profit = null

### Featured Products Tidak Simpan?
- Check apakah ada error di console browser
- Pastikan Livewire component terupdate
- Reload halaman setelah save

---

## 📚 Resources

- [Chart.js Documentation](https://www.chartjs.org/)
- [Laravel Livewire Docs](https://livewire.laravel.com/)
- [Tailwind CSS](https://tailwindcss.com/)

---

**Last Updated**: 2026-04-24  
**Version**: 1.0.0
