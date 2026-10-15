<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
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
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create new permissions
        Permission::firstOrCreate(['name' => 'manage service tickets', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);

        // 2. Create Teknisi Servis Role
        /** @var Role $teknisiRole */
        $teknisiRole = Role::firstOrCreate(['name' => 'Teknisi Servis', 'guard_name' => 'web']);
        if ($teknisiRole instanceof Role) {
            $teknisiRole->syncPermissions([
                'view inventory',
                'manage service tickets',
            ]);
        }

        // 3. Grant permissions to existing roles
        /** @var Role|null $superuserRole */
        $superuserRole = Role::where('name', 'Superuser')->first();
        if ($superuserRole instanceof Role) {
            $superuserRole->syncPermissions(Permission::all());
        }

        /** @var Role|null $ownerRole */
        $ownerRole = Role::where('name', 'Owner')->first();
        if ($ownerRole instanceof Role) {
            $ownerRole->syncPermissions(Permission::all());
        }

        /** @var Role|null $branchManagerRole */
        $branchManagerRole = Role::where('name', 'Branch Manager')->first();
        if ($branchManagerRole instanceof Role) {
            $branchManagerRole->givePermissionTo('manage service tickets');
        }

        /** @var Role|null $warehouseRole */
        $warehouseRole = Role::where('name', 'Warehouse')->first();
        if ($warehouseRole instanceof Role) {
            $warehouseRole->givePermissionTo('manage service tickets');
        }

        // 4. Create default Teknisi user for testing & demonstration
        /** @var User $teknisiUser */
        $teknisiUser = User::firstOrCreate(
            ['email' => 'teknisi@omahvisual.com'],
            [
                'name' => 'Teknisi Workshop',
                'password' => Hash::make('password123'),
                'phone' => '081234567894',
            ]
        );
        if ($teknisiUser instanceof User && $teknisiRole instanceof Role) {
            $teknisiUser->syncRoles([$teknisiRole]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /** @var User|null $teknisiUser */
        $teknisiUser = User::where('email', 'teknisi@omahvisual.com')->first();
        if ($teknisiUser instanceof User) {
            $teknisiUser->delete();
        }

        /** @var Role|null $teknisiRole */
        $teknisiRole = Role::where('name', 'Teknisi Servis')->first();
        if ($teknisiRole instanceof Role) {
            $teknisiRole->delete();
        }

        Permission::whereIn('name', ['manage service tickets', 'manage users'])->delete();
    }
};
