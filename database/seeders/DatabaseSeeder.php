<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their actions
        $modules = [
            'users'       => ['Users Management', 'fas fa-users'],
            'roles'       => ['Roles Management', 'fas fa-shield-alt'],
            'permissions' => ['Permissions Management', 'fas fa-key'],
            'items'       => ['Items Management', 'fas fa-boxes'],
            'settings'    => ['Site Settings', 'fas fa-cog'],
            'activity'    => ['Activity Logs', 'fas fa-history'],
            'profile'     => ['Profile', 'fas fa-user'],
            'finance.ledgers' => ['Finance Ledgers', 'fas fa-book'],
            'finance.bank' => ['Bank Accounts', 'fas fa-building-columns'],
            'finance.cashflows' => ['Cashflow Planning', 'fas fa-arrow-trend-up'],
            'finance.expenses' => ['Expense Planning', 'fas fa-receipt'],
            'finance.payments' => ['Expense Payments', 'fas fa-money-bill-wave'],
            'finance' => ['Finance Approval', 'fas fa-check-double'],
        ];

        $actions = ['index', 'show', 'create', 'edit', 'delete'];

        // Create permissions
        foreach ($modules as $module => [$label, $icon]) {
            $moduleActions = $module === 'finance' ? ['approve'] : $actions;
            foreach ($moduleActions as $action) {
                Permission::updateOrCreate(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'web'],
                    [
                        'group'       => $action,
                        'module'      => $module,
                        'description' => "{$action} access for " . $label,
                    ]
                );
            }
        }

        // Create Roles
        $superAdmin = Role::create([
            'name'        => 'super-admin',
            'guard_name'  => 'web',
            'description' => 'Super Administrator with full access to everything',
            'color'       => '#dc3545',
            'icon'        => 'fas fa-crown',
            'is_default'  => false,
        ]);

        $admin = Role::create([
            'name'        => 'admin',
            'guard_name'  => 'web',
            'description' => 'Administrator with limited access based on permissions',
            'color'       => '#007bff',
            'icon'        => 'fas fa-user-shield',
            'is_default'  => false,
        ]);

        $manager = Role::create([
            'name'        => 'manager',
            'guard_name'  => 'web',
            'description' => 'Manager with moderate access',
            'color'       => '#28a745',
            'icon'        => 'fas fa-user-tie',
            'is_default'  => false,
        ]);

        $editor = Role::create([
            'name'        => 'editor',
            'guard_name'  => 'web',
            'description' => 'Editor with create/edit access on content',
            'color'       => '#fd7e14',
            'icon'        => 'fas fa-edit',
            'is_default'  => false,
        ]);

        $viewer = Role::create([
            'name'        => 'viewer',
            'guard_name'  => 'web',
            'description' => 'Read-only access to content',
            'color'       => '#6c757d',
            'icon'        => 'fas fa-eye',
            'is_default'  => true,
        ]);

        // Assign permissions to roles
        // Admin gets all item + profile permissions (not role/permission management)
        $adminPermissions = Permission::whereIn('module', [
                'items',
                'profile',
                'activity',
                'finance.ledgers',
                'finance.bank',
                'finance.cashflows',
                'finance.expenses',
                'finance.payments',
                'finance',
            ])
            ->orWhereIn('name', ['users.index', 'users.show', 'users.create', 'users.edit', 'users.delete'])
            ->get();
        $admin->syncPermissions($adminPermissions);

        // Manager
        $managerPermissions = Permission::whereIn('name', [
            'items.index', 'items.show', 'items.create', 'items.edit',
            'users.index', 'users.show',
            'profile.index', 'profile.edit',
            'activity.index',
            'finance.ledgers.index', 'finance.ledgers.show', 'finance.ledgers.create', 'finance.ledgers.edit',
            'finance.bank.index', 'finance.bank.show',
            'finance.cashflows.index', 'finance.cashflows.show', 'finance.cashflows.create',
            'finance.expenses.index', 'finance.expenses.show', 'finance.expenses.create', 'finance.expenses.edit',
            'finance.payments.create',
            'finance.approve',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        // Editor
        $editorPermissions = Permission::whereIn('name', [
            'items.index', 'items.show', 'items.create', 'items.edit',
            'profile.index', 'profile.edit',
            'finance.ledgers.index', 'finance.bank.index', 'finance.cashflows.index', 'finance.expenses.index',
            'finance.cashflows.create', 'finance.expenses.create', 'finance.payments.create',
        ])->get();
        $editor->syncPermissions($editorPermissions);

        // Viewer
        $viewerPermissions = Permission::whereIn('name', [
            'items.index', 'items.show',
            'profile.index', 'profile.edit',
        ])->get();
        $viewer->syncPermissions($viewerPermissions);

        // Create Super Admin User
        $superAdminUser = User::create([
            'name'       => 'Super Administrator',
            'username'   => 'superadmin',
            'email'      => 'superadmin@rbac.com',
            'password'   => bcrypt('password'),
            'phone'      => '+91 9876543210',
            'designation' => 'System Super Administrator',
            'department' => 'IT',
            'bio'        => 'I am the super administrator of this system with full access to everything.',
            'is_active'  => true,
            'created_by' => null,
        ]);
        $superAdminUser->assignRole($superAdmin);

        // Create Admin User (created by super admin)
        $adminUser = User::create([
            'name'       => 'Admin User',
            'username'   => 'admin',
            'email'      => 'admin@rbac.com',
            'password'   => bcrypt('password'),
            'phone'      => '+91 9876543211',
            'designation' => 'System Administrator',
            'department' => 'IT',
            'bio'        => 'I am a system administrator.',
            'is_active'  => true,
            'created_by' => $superAdminUser->id,
        ]);
        $adminUser->assignRole($admin);

        // Create Manager User (created by admin)
        $managerUser = User::create([
            'name'       => 'Manager User',
            'username'   => 'manager',
            'email'      => 'manager@rbac.com',
            'password'   => bcrypt('password'),
            'designation' => 'Operations Manager',
            'department' => 'Operations',
            'is_active'  => true,
            'created_by' => $adminUser->id,
        ]);
        $managerUser->assignRole($manager);

        // Create Editor User (created by admin)
        $editorUser = User::create([
            'name'       => 'Editor User',
            'username'   => 'editor',
            'email'      => 'editor@rbac.com',
            'password'   => bcrypt('password'),
            'designation' => 'Content Editor',
            'department' => 'Content',
            'is_active'  => true,
            'created_by' => $adminUser->id,
        ]);
        $editorUser->assignRole($editor);

        // Create Viewer User (created by admin)
        $viewerUser = User::create([
            'name'       => 'Viewer User',
            'username'   => 'viewer',
            'email'      => 'viewer@rbac.com',
            'password'   => bcrypt('password'),
            'designation' => 'Content Viewer',
            'department' => 'Marketing',
            'is_active'  => true,
            'created_by' => $adminUser->id,
        ]);
        $viewerUser->assignRole($viewer);

        // Seed Demo Items
        $this->seedItems($superAdminUser, $adminUser, $managerUser, $editorUser);

        // Seed Site Settings
        $this->seedSiteSettings();

        $this->command->info('✅ Database seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@rbac.com', 'password'],
                ['Admin', 'admin@rbac.com', 'password'],
                ['Manager', 'manager@rbac.com', 'password'],
                ['Editor', 'editor@rbac.com', 'password'],
                ['Viewer', 'viewer@rbac.com', 'password'],
            ]
        );
    }

    private function seedItems($superAdmin, $admin, $manager, $editor): void
    {
        $categories = ['Electronics', 'Furniture', 'Clothing', 'Books', 'Sports'];

        $items = [
            ['title' => 'Laptop Pro X1', 'category' => 'Electronics', 'price' => 75000, 'status' => 'active', 'created_by' => $superAdmin->id, 'share_with_creator_admin' => false],
            ['title' => 'Office Chair Deluxe', 'category' => 'Furniture', 'price' => 15000, 'status' => 'active', 'created_by' => $admin->id, 'share_with_creator_admin' => false],
            ['title' => 'Wireless Keyboard', 'category' => 'Electronics', 'price' => 2500, 'status' => 'active', 'created_by' => $admin->id, 'share_with_creator_admin' => false],
            ['title' => 'Ergonomic Desk', 'category' => 'Furniture', 'price' => 25000, 'status' => 'inactive', 'created_by' => $manager->id, 'share_with_creator_admin' => true],
            ['title' => 'JavaScript Handbook', 'category' => 'Books', 'price' => 800, 'status' => 'active', 'created_by' => $editor->id, 'share_with_creator_admin' => true],
            ['title' => 'Running Shoes Pro', 'category' => 'Sports', 'price' => 5500, 'status' => 'draft', 'created_by' => $manager->id, 'share_with_creator_admin' => false],
            ['title' => 'Monitor 4K Ultra', 'category' => 'Electronics', 'price' => 35000, 'status' => 'active', 'created_by' => $superAdmin->id, 'share_with_creator_admin' => false],
            ['title' => 'Casual T-Shirt', 'category' => 'Clothing', 'price' => 599, 'status' => 'active', 'created_by' => $editor->id, 'share_with_creator_admin' => false],
        ];

        foreach ($items as $item) {
            \App\Models\Item::create(array_merge($item, ['description' => 'This is a sample item description for demonstration purposes.']));
        }
    }

    private function seedSiteSettings(): void
    {
        foreach (SiteSetting::getDefaultSettings() as $setting) {
            SiteSetting::create($setting);
        }
    }
}
