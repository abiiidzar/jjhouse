<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Admin',
                'description' => 'Administrator dengan akses penuh ke sistem',
            ]
        );

        $karyawan = Role::firstOrCreate(
            ['name' => 'karyawan'],
            [
                'display_name' => 'Karyawan',
                'description' => 'Karyawan gudang/operasional',
            ]
        );

        $kasir = Role::firstOrCreate(
            ['name' => 'kasir'],
            [
                'display_name' => 'Kasir',
                'description' => 'Kasir untuk pemrosesan pembayaran',
            ]
        );

        $user = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Customer',
                'description' => 'User/Pelanggan biasa',
            ]
        );

        // Create Permissions
        // Dashboard Permissions
        $viewDashboard = Permission::firstOrCreate(
            ['name' => 'view_dashboard'],
            [
                'display_name' => 'Lihat Dashboard',
                'description' => 'Dapat melihat dashboard admin',
            ]
        );

        // Product Permissions
        $viewProducts = Permission::firstOrCreate(
            ['name' => 'view_products'],
            [
                'display_name' => 'Lihat Produk',
                'description' => 'Dapat melihat daftar produk',
            ]
        );

        $createProduct = Permission::firstOrCreate(
            ['name' => 'create_product'],
            [
                'display_name' => 'Buat Produk',
                'description' => 'Dapat membuat produk baru',
            ]
        );

        $editProduct = Permission::firstOrCreate(
            ['name' => 'edit_product'],
            [
                'display_name' => 'Edit Produk',
                'description' => 'Dapat mengedit produk',
            ]
        );

        $deleteProduct = Permission::firstOrCreate(
            ['name' => 'delete_product'],
            [
                'display_name' => 'Hapus Produk',
                'description' => 'Dapat menghapus produk',
            ]
        );

        $updateStock = Permission::firstOrCreate(
            ['name' => 'update_stock'],
            [
                'display_name' => 'Update Stok',
                'description' => 'Dapat mengupdate stok produk',
            ]
        );

        // Order Permissions
        $viewOrders = Permission::firstOrCreate(
            ['name' => 'view_orders'],
            [
                'display_name' => 'Lihat Pesanan',
                'description' => 'Dapat melihat daftar pesanan',
            ]
        );

        $editOrder = Permission::firstOrCreate(
            ['name' => 'edit_order'],
            [
                'display_name' => 'Edit Pesanan',
                'description' => 'Dapat mengubah status pesanan',
            ]
        );

        $processPayment = Permission::firstOrCreate(
            ['name' => 'process_payment'],
            [
                'display_name' => 'Proses Pembayaran',
                'description' => 'Dapat memproses pembayaran pesanan',
            ]
        );

        // Category Permissions
        $manageCategories = Permission::firstOrCreate(
            ['name' => 'manage_categories'],
            [
                'display_name' => 'Kelola Kategori',
                'description' => 'Dapat mengelola kategori produk',
            ]
        );

        // User Permissions
        $viewUsers = Permission::firstOrCreate(
            ['name' => 'view_users'],
            [
                'display_name' => 'Lihat Pengguna',
                'description' => 'Dapat melihat daftar pengguna',
            ]
        );

        $manageUsers = Permission::firstOrCreate(
            ['name' => 'manage_users'],
            [
                'display_name' => 'Kelola Pengguna',
                'description' => 'Dapat mengelola pengguna (create, edit, delete)',
            ]
        );

        $assignRoles = Permission::firstOrCreate(
            ['name' => 'assign_roles'],
            [
                'display_name' => 'Assign Roles',
                'description' => 'Dapat memberikan role kepada pengguna',
            ]
        );

        // Reports/Analytics Permissions
        $viewReports = Permission::firstOrCreate(
            ['name' => 'view_reports'],
            [
                'display_name' => 'Lihat Laporan',
                'description' => 'Dapat melihat laporan dan analytics',
            ]
        );

        // Settings Permissions
        $manageSettings = Permission::firstOrCreate(
            ['name' => 'manage_settings'],
            [
                'display_name' => 'Kelola Pengaturan',
                'description' => 'Dapat mengubah pengaturan sistem',
            ]
        );

        // Blog Permissions
        $manageBlog = Permission::firstOrCreate(
            ['name' => 'manage_blog'],
            [
                'display_name' => 'Kelola Blog',
                'description' => 'Dapat mengelola artikel blog',
            ]
        );

        // Pre-order Permissions
        $managePreorder = Permission::firstOrCreate(
            ['name' => 'manage_preorder'],
            [
                'display_name' => 'Kelola Pre-order',
                'description' => 'Dapat mengelola pre-order produk',
            ]
        );

        // Featured Products Permissions
        $manageFeatured = Permission::firstOrCreate(
            ['name' => 'manage_featured'],
            [
                'display_name' => 'Kelola Produk Unggulan',
                'description' => 'Dapat mengelola produk yang ditampilkan di home',
            ]
        );

        // Assign Permissions to Admin Role (Full Access) - Check before assigning
        $this->assignPermissionIfNotExists($admin, $viewDashboard);
        $this->assignPermissionIfNotExists($admin, $viewProducts);
        $this->assignPermissionIfNotExists($admin, $createProduct);
        $this->assignPermissionIfNotExists($admin, $editProduct);
        $this->assignPermissionIfNotExists($admin, $deleteProduct);
        $this->assignPermissionIfNotExists($admin, $updateStock);
        $this->assignPermissionIfNotExists($admin, $viewOrders);
        $this->assignPermissionIfNotExists($admin, $editOrder);
        $this->assignPermissionIfNotExists($admin, $processPayment);
        $this->assignPermissionIfNotExists($admin, $manageCategories);
        $this->assignPermissionIfNotExists($admin, $viewUsers);
        $this->assignPermissionIfNotExists($admin, $manageUsers);
        $this->assignPermissionIfNotExists($admin, $assignRoles);
        $this->assignPermissionIfNotExists($admin, $viewReports);
        $this->assignPermissionIfNotExists($admin, $manageSettings);
        $this->assignPermissionIfNotExists($admin, $manageBlog);
        $this->assignPermissionIfNotExists($admin, $managePreorder);
        $this->assignPermissionIfNotExists($admin, $manageFeatured);

        // Assign Permissions to Karyawan Role (Warehouse/Operations)
        $this->assignPermissionIfNotExists($karyawan, $viewProducts);
        $this->assignPermissionIfNotExists($karyawan, $updateStock);
        $this->assignPermissionIfNotExists($karyawan, $viewOrders);
        $this->assignPermissionIfNotExists($karyawan, $editOrder);

        // Assign Permissions to Kasir Role (Cashier)
        $this->assignPermissionIfNotExists($kasir, $viewOrders);
        $this->assignPermissionIfNotExists($kasir, $processPayment);
        $this->assignPermissionIfNotExists($kasir, $viewProducts);

        // Assign Permissions to User Role (Customer)
        // Users only see their own orders and products via frontend

        echo "Roles and Permissions seeded successfully!\n";
    }

    /**
     * Assign permission to role if it doesn't already exist
     */
    private function assignPermissionIfNotExists($role, $permission): void
    {
        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            $role->assignPermission($permission);
        }
    }
}
