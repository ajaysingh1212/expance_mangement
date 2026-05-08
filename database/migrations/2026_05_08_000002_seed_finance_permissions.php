<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            'finance.ledgers' => ['index', 'show', 'create', 'edit', 'delete'],
            'finance.bank' => ['index', 'show', 'create', 'edit', 'delete'],
            'finance.cashflows' => ['index', 'show', 'create', 'edit', 'delete'],
            'finance.expenses' => ['index', 'show', 'create', 'edit', 'delete'],
            'finance.payments' => ['index', 'show', 'create', 'edit', 'delete'],
            'finance' => ['approve'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'web'],
                    [
                        'module' => $module,
                        'group' => $action,
                        'description' => "{$action} access for {$module}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $financePermissionIds = DB::table('permissions')
            ->where(function ($query) {
                $query->where('module', 'like', 'finance.%')
                    ->orWhere('module', 'finance');
            })
            ->pluck('id');

        $roleIds = DB::table('roles')
            ->whereIn('name', ['super-admin', 'admin', 'manager'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($financePermissionIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('permissions')->where('module', 'like', 'finance%')->delete();
    }
};
