<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create specific permissions
        Permission::firstOrCreate(['name' => 'create service ticket', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'execute service ticket', 'guard_name' => 'web']);

        // 2. Assign to Teknisi Servis (ONLY execute service ticket and view inventory)
        /** @var Role|null $teknisiRole */
        $teknisiRole = Role::where('name', 'Teknisi Servis')->first();
        if ($teknisiRole instanceof Role) {
            $teknisiRole->syncPermissions([
                'view inventory',
                'execute service ticket',
            ]);
        }

        // 3. Assign to Warehouse (can create service ticket upon return inspection, CANNOT execute)
        /** @var Role|null $warehouseRole */
        $warehouseRole = Role::where('name', 'Warehouse')->first();
        if ($warehouseRole instanceof Role) {
            $warehouseRole->givePermissionTo('create service ticket');
            $warehouseRole->revokePermissionTo('manage service tickets');
        }

        // 4. Assign to Branch Manager, Owner, Superuser
        /** @var Role|null $branchManagerRole */
        $branchManagerRole = Role::where('name', 'Branch Manager')->first();
        if ($branchManagerRole instanceof Role) {
            $branchManagerRole->givePermissionTo(['create service ticket', 'execute service ticket']);
        }

        /** @var Role|null $ownerRole */
        $ownerRole = Role::where('name', 'Owner')->first();
        if ($ownerRole instanceof Role) {
            $ownerRole->syncPermissions(Permission::all());
        }

        /** @var Role|null $superuserRole */
        $superuserRole = Role::where('name', 'Superuser')->first();
        if ($superuserRole instanceof Role) {
            $superuserRole->syncPermissions(Permission::all());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', ['create service ticket', 'execute service ticket'])->delete();
    }
};
