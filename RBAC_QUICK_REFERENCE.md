# 🚀 RBAC Quick Start Guide

## Login Credentials

```
┌──────────────┬─────────────────────────┬──────────┬──────────┐
│ Role         │ Email                   │ Password │ URL      │
├──────────────┼─────────────────────────┼──────────┼──────────┤
│ Admin        │ admin@jjhouse.com       │ password │ /admin   │
│ Karyawan     │ anita@jjhouse.com       │ password │ /karyawan│
│ Kasir        │ doni@jjhouse.com        │ password │ /kasir   │
│ Customer 1   │ budi@jjhouse.com        │ password │ /        │
│ Customer 2   │ siti@jjhouse.com        │ password │ /        │
└──────────────┴─────────────────────────┴──────────┴──────────┘
```

## Dashboard URLs

| Role | URL | Deskripsi |
|------|-----|-----------|
| Admin | `/admin` | Analytics, product management, user management |
| Karyawan | `/karyawan` | Stock management, order fulfillment |
| Kasir | `/kasir` | Payment processing, daily reports |
| Customer | `/dashboard` | My orders, wishlist, profile |

## Admin Menu

```
📊 Analytics
├── Total Penjualan
├── Total HPP
├── Total Keuntungan
└── Grafik 6 Bulan

📦 Products
├── List Produk
├── Create Product
├── Edit Product
├── Delete Product
└── Manage Featured

📋 Orders
├── All Orders
└── Order Details

👥 Users
├── Manage Users
├── Assign Roles
└── View Permissions

⚙️ Settings
├── General
├── Payment
└── Shipping

📝 Blog
├── Manage Posts
├── Manage Categories
└── Manage Comments

📅 Pre-orders
└── Monitor Pre-orders
```

## Karyawan Menu

```
📦 Stok
├── View All Products
├── Edit Stock
└── Monitor Stock Levels

📋 Pesanan
├── Pending Orders
├── Processing Orders
├── Ready to Ship
└── Update Status
```

## Kasir Menu

```
💳 Pembayaran
├── Unpaid Orders
├── Process Payment
├── Daily Report
└── Print Receipt
```

## Key Features

### ✅ Role-Based Access
- Setiap user hanya akses fitur sesuai role-nya
- Automatic redirect ke dashboard yang sesuai

### ✅ Permission Management
- 18+ permissions yang dapat dikustomisasi
- Fine-grained access control

### ✅ User Management
- Assign/revoke roles
- View user permissions
- Multiple roles per user (jika diperlukan)

### ✅ Audit Trail Ready
- Track role assignments
- Monitor permission usage

## Common Tasks

### Assign Role ke User

```php
// Via Tinker
$user = User::find(1);
$user->assignRole('admin');
```

### Create New Role

```php
$role = Role::create([
    'name' => 'supervisor',
    'display_name' => 'Supervisor',
    'description' => 'Deskripsi',
]);
```

### Add Permission to Role

```php
$role = Role::find(1); // Admin
$role->assignPermission('manage_users');
```

## Security Features

- ✓ Middleware-based access control
- ✓ Permission validation in controllers
- ✓ Blade directives for UI hiding
- ✓ Database-backed permissions
- ✓ Role inheritance support

---

**Catatan**: Semua default passwords diset ke "password". Ubah di production!
