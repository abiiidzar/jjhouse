# 📋 Dokumentasi Sistem RBAC (Role-Based Access Control)

## Daftar Isi
1. [Pengenalan](#pengenalan)
2. [Struktur Sistem](#struktur-sistem)
3. [User Roles & Permissions](#user-roles--permissions)
4. [Login & Akses](#login--akses)
5. [Fitur per Role](#fitur-per-role)
6. [Implementasi Teknis](#implementasi-teknis)
7. [Troubleshooting](#troubleshooting)

---

## 1. Pengenalan

Sistem RBAC (Role-Based Access Control) memungkinkan pembagian akses sistem berdasarkan peran pengguna. Setiap role memiliki permissions khusus yang menentukan fitur mana yang dapat diakses.

### Manfaat RBAC:
- ✓ Kontrol akses yang lebih ketat
- ✓ Mudah mengelola permission untuk banyak user
- ✓ Dashboard yang customized per role
- ✓ Audit trail yang lebih jelas

---

## 2. Struktur Sistem

### Database Tables
```
users          → Menyimpan data user
├── id
├── name
├── email
├── password
└── ...

roles          → Menyimpan nama-nama role
├── id
├── name (unique)
├── display_name
└── description

permissions    → Menyimpan daftar permissions
├── id
├── name (unique)
├── display_name
└── description

role_user      → Pivot table menghubungkan users & roles
├── user_id
└── role_id

permission_role → Pivot table menghubungkan roles & permissions
├── role_id
└── permission_id
```

### Relationships
```
User → belongs to many Role → belongs to many Permission
```

---

## 3. User Roles & Permissions

### 📊 Permission Matrix

| Permission | Admin | Karyawan | Kasir | User |
|-----------|-------|----------|-------|------|
| view_dashboard | ✓ | ✗ | ✗ | ✗ |
| view_products | ✓ | ✓ | ✓ | ✓ |
| create_product | ✓ | ✗ | ✗ | ✗ |
| edit_product | ✓ | ✗ | ✗ | ✗ |
| delete_product | ✓ | ✗ | ✗ | ✗ |
| update_stock | ✓ | ✓ | ✗ | ✗ |
| view_orders | ✓ | ✓ | ✓ | ✓ |
| edit_order | ✓ | ✓ | ✗ | ✗ |
| process_payment | ✓ | ✗ | ✓ | ✗ |
| manage_categories | ✓ | ✗ | ✗ | ✗ |
| view_users | ✓ | ✗ | ✗ | ✗ |
| manage_users | ✓ | ✗ | ✗ | ✗ |
| assign_roles | ✓ | ✗ | ✗ | ✗ |
| view_reports | ✓ | ✗ | ✗ | ✗ |
| manage_settings | ✓ | ✗ | ✗ | ✗ |
| manage_blog | ✓ | ✗ | ✗ | ✗ |
| manage_preorder | ✓ | ✗ | ✗ | ✗ |
| manage_featured | ✓ | ✗ | ✗ | ✗ |

### 👥 Role Descriptions

#### 1. **Admin** 👨‍💼
- **Akses**: Full system access
- **Deskripsi**: Administrator dengan kontrol penuh terhadap semua fitur sistem
- **Dashboard**: `/admin`
- **Fitur Utama**:
  - Manage products (create, edit, delete)
  - Kelola stok
  - View & manage orders
  - Process payments
  - Manage users & roles
  - Settings & configuration
  - Blog management
  - Pre-order management
  - Analytics & reports

#### 2. **Karyawan** 👷
- **Akses**: Warehouse/Operations only
- **Deskripsi**: Karyawan gudang untuk pengelolaan stok dan fulfillment pesanan
- **Dashboard**: `/karyawan`
- **Fitur Utama**:
  - View products
  - Update stock
  - View orders
  - Update order status (untuk fulfillment)
  - Monitor pending orders

#### 3. **Kasir** 💳
- **Akses**: Payment processing only
- **Deskripsi**: Kasir untuk pemrosesan pembayaran pesanan
- **Dashboard**: `/kasir`
- **Fitur Utama**:
  - View orders (unpaid only)
  - Process payment
  - View product list
  - Daily sales report

#### 4. **User/Customer** 👤
- **Akses**: Customer-facing features only
- **Deskripsi**: Pelanggan biasa dengan akses frontend e-commerce
- **Dashboard**: `/dashboard`
- **Fitur Utama**:
  - View products
  - View own orders
  - Wishlist management
  - Profile management
  - Blog comments

---

## 4. Login & Akses

### Data Login Testing

```
┌─────────────────────────────────────────────────────────┐
│ ROLE      │ EMAIL               │ PASSWORD     │ URL    │
├─────────────────────────────────────────────────────────┤
│ Admin     │ admin@jjhouse.com   │ password     │ /admin │
│ Karyawan  │ anita@jjhouse.com   │ password     │ /kary… │
│ Kasir     │ doni@jjhouse.com    │ password     │ /kasir │
│ Customer  │ budi@jjhouse.com    │ password     │ /dash… │
│ Customer  │ siti@jjhouse.com    │ password     │ /dash… │
└─────────────────────────────────────────────────────────┘
```

### Redirect Logic

Setelah login, user akan diredirect ke dashboard sesuai role-nya:
- **Admin** → `/admin`
- **Karyawan** → `/karyawan`
- **Kasir** → `/kasir`
- **Customer/User** → `/dashboard`

### Access Control

Setiap route dilindungi oleh middleware:
- `auth`: User harus login
- `role:admin`: User harus memiliki role admin
- `role:karyawan`: User harus memiliki role karyawan
- `role:kasir`: User harus memiliki role kasir
- `permission:permission_name`: User harus memiliki permission tertentu

---

## 5. Fitur per Role

### 5.1 Admin Dashboard (`/admin`)

```
┌─────────────────────────────────────────────────────────┐
│                    ADMIN DASHBOARD                      │
├─────────────────────────────────────────────────────────┤
│ • Analytics & Reports (6-month data)                    │
│ • Total Penjualan | Total HPP | Total Keuntungan        │
│ • Manage Products                                        │
│ • Manage Orders                                          │
│ • Manage Users & Assign Roles                           │
│ • System Settings                                        │
│ • Blog Management                                        │
│ • Pre-order Management                                  │
│ • Instagram Integration                                 │
│ • Featured Products Management                          │
└─────────────────────────────────────────────────────────┘
```

**Routes**:
- `GET /admin` - Dashboard
- `GET/POST /admin/products` - CRUD products
- `GET/POST /admin/orders` - Manage orders
- `GET/POST /admin/blog/posts` - Blog management
- `GET/POST /admin/settings/*` - Settings

### 5.2 Karyawan Dashboard (`/karyawan`)

```
┌─────────────────────────────────────────────────────────┐
│                  KARYAWAN DASHBOARD                     │
├─────────────────────────────────────────────────────────┤
│ Stats:                                                   │
│ • Total Stok | Pesanan Tertunda | Diproses | Siap      │
│                                                          │
│ Menu Cepat:                                             │
│ • Kelola Stok Produk                                    │
│ • Pesanan untuk Dipenuhi                                │
│ • Daftar Produk                                         │
└─────────────────────────────────────────────────────────┘
```

**Routes**:
- `GET /karyawan` - Dashboard
- `GET/PATCH /karyawan/stock` - Manage stock
- `GET /karyawan/orders` - View orders for fulfillment
- `PATCH /karyawan/orders/:id/status` - Update order status
- `GET /karyawan/products` - View products

### 5.3 Kasir Dashboard (`/kasir`)

```
┌─────────────────────────────────────────────────────────┐
│                   KASIR DASHBOARD                       │
├─────────────────────────────────────────────────────────┤
│ Stats:                                                   │
│ • Belum Dibayar | Pesanan Hari Ini | Pendapatan        │
│ • Rata-rata Transaksi                                  │
│                                                          │
│ Menu Cepat:                                             │
│ • Proses Pembayaran                                     │
│ • Laporan Harian                                        │
│ • Cetak Struk                                           │
└─────────────────────────────────────────────────────────┘
```

**Routes**:
- `GET /kasir` - Dashboard
- `GET /kasir/orders` - View unpaid orders
- `GET /kasir/orders/:id` - Order detail
- `POST /kasir/orders/:id/payment` - Process payment

### 5.4 Customer Dashboard (`/dashboard`)

**Routes**:
- `GET /dashboard` - My orders
- `GET /dashboard/orders/:id` - Order detail
- `GET /dashboard/wishlist` - Wishlist
- `GET /dashboard/profile` - Edit profile

---

## 6. Implementasi Teknis

### 6.1 Models dengan RBAC

```php
// User Model
$user = User::find(1);

// Check role
$user->hasRole('admin');              // true/false
$user->hasAnyRole(['admin', 'karyawan']); // true/false
$user->hasAllRoles(['admin']);        // true/false

// Get role
$user->getRole();                     // First role
$user->getRoleDisplayName();          // "Admin"
$user->roles;                         // All roles

// Check permission
$user->hasPermission('view_products');
$user->hasAnyPermission(['edit_order', 'process_payment']);
$user->hasAllPermissions(['view_orders', 'edit_order']);

// Assign/Revoke role
$user->assignRole('admin');
$user->revokeRole('admin');
```

### 6.2 Middleware Usage

```php
// In routes/web.php

// Single role
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Only admin
});

// Multiple roles (any)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Admin or Kasir
});

// Permission check
Route::middleware(['auth', 'permission:edit_product'])->group(function () {
    // User dengan permission edit_product
});
```

### 6.3 Blade Template Usage

```php
@auth
    @if(Auth::user()->hasRole('admin'))
        <!-- Admin only content -->
    @endif

    @can('edit_product')
        <!-- User dengan permission edit_product -->
    @endcan
@endauth

<!-- Display role name -->
{{ Auth::user()->getRoleDisplayName() }}
```

### 6.4 Controller Usage

```php
class ProductController extends Controller
{
    public function __construct()
    {
        // Protect route dengan middleware
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function edit(Product $product)
    {
        // Check permission
        if (!auth()->user()->hasPermission('edit_product')) {
            abort(403);
        }
        // ...
    }
}
```

---

## 7. Troubleshooting

### Q: User tidak bisa akses dashboard mereka
**A**: 
1. Pastikan user memiliki role yang benar
   ```php
   $user->assignRole('admin'); // atau role lainnya
   ```
2. Periksa browser cache
3. Clear authentication cache: `php artisan cache:clear`

### Q: Permission tidak bekerja
**A**:
1. Periksa permission sudah dibuat di database
   ```php
   Permission::where('name', 'view_products')->first()
   ```
2. Pastikan role sudah assigned permission
   ```php
   $role->assignPermission('view_products');
   ```
3. Verify user has role assigned

### Q: Middleware error 403
**A**:
1. Cek user role: `Auth::user()->getRoleDisplayName()`
2. Cek middleware definition di `bootstrap/app.php`
3. Clear all cache: `php artisan cache:clear`

### Q: Cara menambah role baru?
**A**:
```php
// Create role
$role = Role::create([
    'name' => 'supervisor',
    'display_name' => 'Supervisor',
    'description' => 'Deskripsi role',
]);

// Assign permissions
$role->assignPermission('view_reports');
$role->assignPermission('edit_order');

// Assign to user
$user->assignRole('supervisor');
```

### Q: Cara menambah permission baru?
**A**:
```php
// Create permission
$permission = Permission::create([
    'name' => 'manage_inventory',
    'display_name' => 'Manage Inventory',
    'description' => 'Deskripsi permission',
]);

// Assign to role
$role->assignPermission($permission);

// Check in code
if (auth()->user()->hasPermission('manage_inventory')) {
    // ...
}
```

---

## 📞 Bantuan

Jika ada pertanyaan atau masalah, hubungi administrator sistem.

---

**Terakhir diupdate**: 24 April 2026
**Versi**: 1.0
