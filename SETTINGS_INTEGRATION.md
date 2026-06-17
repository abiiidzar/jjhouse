# Integrasi Settings dengan Proses Pembelian Customer

Dokumentasi ini menjelaskan bagaimana admin settings terhubung dengan proses checkout dan pembayaran customer.

## 📋 Ringkasan Integrasi

Settings admin yang didefinisikan di `/admin/settings/*` sekarang terintegrasi secara otomatis dengan proses pembelian customer di `checkout` dan payment flow.

---

## 🔧 1. General Settings → Checkout Page

**File:** `/admin/settings/general`

### Pengaturan yang Digunakan:

| Setting | Cache Key | Digunakan Di | Fungsi |
|---------|-----------|-------------|--------|
| Store Name | `setting_store_name` | Checkout page | Menampilkan nama toko |
| Store Email | `setting_store_email` | Order confirmation | Email untuk customer service |
| Store Phone | `setting_store_phone` | Checkout page | Nomor telepon customer service |
| Store Description | `setting_store_description` | Checkout page | Deskripsi toko di halaman checkout |

### Cara Kerja:

1. Admin mengisi info di `/admin/settings/general`
2. Data disimpan ke cache dengan prefix `setting_*`
3. CheckoutController mengambil data dari cache saat render checkout page
4. Data ditampilkan di halaman checkout (`resources/views/checkout/index.blade.php`)

### Contoh Penggunaan di Blade:

```blade
<!-- Tampilkan informasi toko di checkout -->
<div class="store-info">
    <h3>{{ $storeInfo['name'] }}</h3>
    <p>{{ $storeInfo['description'] }}</p>
    <p>📧 {{ $storeInfo['email'] }}</p>
    <p>📱 {{ $storeInfo['phone'] }}</p>
</div>
```

---

## 💳 2. Payment Settings → Midtrans Integration

**File:** `/admin/settings/payment`

### Pengaturan yang Digunakan:

| Setting | Cache Key | Service | Fungsi |
|---------|-----------|---------|--------|
| Midtrans Server Key | `payment_midtrans_server_key` | MidtransService | Backend authentication |
| Midtrans Client Key | `payment_midtrans_client_key` | MidtransService | Frontend payment UI |
| Environment | `payment_midtrans_environment` | MidtransService | sandbox atau production |

### Cara Kerja:

1. Admin mengisi Midtrans credentials di `/admin/settings/payment`
2. Data disimpan ke cache dengan prefix `payment_*`
3. **MidtransService** membaca dari cache terlebih dahulu:
   ```php
   $serverKey = Cache::get('payment_midtrans_server_key') 
                ?? config('services.midtrans.server_key');
   ```
4. Jika cache kosong, fallback ke `config/services.php`
5. Payment gateway diinisialisasi dengan credentials yang benar

### Flow Pembayaran:

```
Customer klik "Bayar dengan Midtrans"
    ↓
CheckoutController::process()
    ↓
MidtransService::createTransaction()
    ↓
Membaca payment settings dari cache
    ↓
Generate Snap Token
    ↓
Redirect ke Midtrans payment page
    ↓
Payment callback → Order status updated
```

### Testing Payment Settings:

1. Pastikan Server Key dan Client Key sudah diisi
2. Pilih environment yang sesuai (sandbox untuk testing)
3. Test transaksi di checkout page
4. Jika error, cek credentials di `/admin/settings/payment`

---

## 🚚 3. Shipping Settings → RajaOngkir Integration

**File:** `/admin/settings/shipping`

### Pengaturan yang Digunakan:

| Setting | Cache Key | Service | Fungsi |
|---------|-----------|---------|--------|
| RajaOngkir API Key | `shipping_rajaongkir_api_key` | RajaOngkirService | Authentication ke RajaOngkir API |
| Default Origin City | `shipping_default_origin_city` | RajaOngkirService | Kota asal pengiriman (default: 152 = Jakarta Selatan) |
| Free Shipping Threshold | `shipping_free_shipping_threshold` | RajaOngkirService | Minimal pembelian untuk gratis ongkir |

### Cara Kerja:

1. Admin mengisi RajaOngkir API Key di `/admin/settings/shipping`
2. Data disimpan ke cache dengan prefix `shipping_*`
3. **RajaOngkirService** membaca dari cache terlebih dahulu:
   ```php
   $this->apiKey = Cache::get('shipping_rajaongkir_api_key') 
                   ?? config('services.rajaongkir.api_key');
   ```
4. Saat customer memilih alamat pengiriman, RajaOngkir API dipanggil untuk hitung biaya
5. Jika subtotal ≥ free shipping threshold, ongkir gratis

### Method Baru di RajaOngkirService:

```php
// Dapatkan threshold gratis ongkir
$threshold = $rajaongkir->getFreeShippingThreshold(); // Rp

// Dapatkan kota asal default
$originCity = $rajaongkir->getDefaultOriginCity(); // City ID

// Cek apakah eligible untuk gratis ongkir
$isFree = $rajaongkir->isEligibleForFreeShipping($subtotal);
```

### Contoh: Implementasi Free Shipping

Di checkout page atau shipping calculation:

```php
// Di controller
$rajaongkir = new RajaOngkirService();
$subtotal = 500000; // Rp

if ($rajaongkir->isEligibleForFreeShipping($subtotal)) {
    $shippingCost = 0;
    $message = "🎉 Gratis ongkos kirim!";
} else {
    $threshold = $rajaongkir->getFreeShippingThreshold();
    $remaining = $threshold - $subtotal;
    $message = "Belanja Rp " . number_format($remaining) . " lagi untuk gratis ongkir";
}
```

---

## 🔄 Flow Chart Lengkap

```
┌─────────────────────┐
│  ADMIN SETTINGS     │
├─────────────────────┤
│ ⚙️  General          │ → Cache: setting_*
│ 💳 Payment          │ → Cache: payment_*
│ 🚚 Shipping         │ → Cache: shipping_*
└─────────────────────┘
           ↓
        [CACHE]
           ↓
┌─────────────────────────────────────┐
│     CHECKOUT PAGE (Customer)         │
├─────────────────────────────────────┤
│ Tampilkan: Store name, email, phone │
│ Hitung: Shipping cost (RajaOngkir)  │
│ Check: Free shipping eligibility    │
└─────────────────────────────────────┘
           ↓
┌─────────────────────────────────────┐
│     PAYMENT PAGE (Midtrans)          │
├─────────────────────────────────────┤
│ Gunakan: Server Key + Client Key     │
│ Environment: Sandbox / Production    │
│ Generate: Payment link / Snap token  │
└─────────────────────────────────────┘
           ↓
┌─────────────────────────────────────┐
│     ORDER CONFIRMATION              │
├─────────────────────────────────────┤
│ Email: Menggunakan store email      │
│ Contact: Menggunakan store phone    │
│ Tracking: Shipping info dari RO     │
└─────────────────────────────────────┘
```

---

## ✅ Checklist Setup

Untuk memastikan semua settings terhubung dengan benar:

### 1. General Settings
- [ ] Isi Store Name
- [ ] Isi Store Email (untuk notifikasi customer)
- [ ] Isi Store Phone (untuk CS customer)
- [ ] Isi Store Description (opsional)

### 2. Payment Settings
- [ ] Daftar akun Midtrans di https://midtrans.com
- [ ] Copy Server Key dari dashboard Midtrans
- [ ] Copy Client Key dari dashboard Midtrans
- [ ] Paste ke `/admin/settings/payment`
- [ ] Pilih environment yang sesuai (sandbox untuk testing)
- [ ] Test transaksi di checkout page

### 3. Shipping Settings
- [ ] Daftar akun RajaOngkir di https://rajaongkir.com
- [ ] Copy API Key dari dashboard RajaOngkir
- [ ] Paste ke `/admin/settings/shipping`
- [ ] Set default origin city (default: 152 = Jakarta)
- [ ] Set free shipping threshold (misal: Rp 300.000)
- [ ] Test shipping calculation di checkout page

---

## 🐛 Troubleshooting

### Payment tidak bekerja
1. Cek apakah Server Key dan Client Key sudah diisi di `/admin/settings/payment`
2. Verifikasi environment setting (sandbox vs production)
3. Pastikan testing menggunakan sandbox credentials
4. Lihat error log di `storage/logs/laravel.log`

### Shipping cost tidak muncul
1. Cek apakah RajaOngkir API Key sudah diisi di `/admin/settings/shipping`
2. Pastikan API key valid dan masih aktif
3. Cek apakah default origin city sudah diset
4. Verifikasi RajaOngkir API plan (starter, basic, pro)

### Free shipping tidak aktif
1. Cek threshold di `/admin/settings/shipping` (jangan 0 jika ingin aktif)
2. Pastikan subtotal ≥ threshold untuk trigger free shipping
3. Clear cache jika perlu: `php artisan cache:clear`

### Cache tidak update
1. Settings disimpan ke cache, bukan database
2. Jika perlu reset, jalankan: `php artisan cache:clear`
3. Settings akan di-cache ulang saat diakses di admin

---

## 📝 Notes

- Semua settings menggunakan Laravel Cache untuk performa optimal
- Fallback ke `config/services.php` jika cache kosong
- Settings dapat di-update real-time tanpa restart aplikasi
- Untuk production, gunakan persistent cache (Redis, Memcached)

