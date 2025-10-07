<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_has_permissions')->delete();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $menuMaster = ['master', 'master-user', 'master-role'];
        $menuWebsite = ['website', 'setting', 'beranda', 'pelanggan', 'antar', 'jemput', 'riwayat', 'antar-jemput', 'datapelanggan', 'pendapatan', 'transaksilayanan', 'tambahpelanggan'];
        $menuLayanan = ['layanan', 'jenisitem', 'jenislayanan', 'hargajenislayanan', 'layananprioritas'];
        $permissionsByRole = [
            'admin' => ['dashboard', ...$menuMaster, ...$menuWebsite, ...$menuLayanan],
            'pelanggan' => ['beranda', 'pelanggan', 'antar', 'jemput', 'riwayat'],
        ];

        $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
            ->map(function ($name) {
                $check = Permission::whereName($name)->first();

                if (!$check) {
                    return Permission::create([
                        'name' => $name,
                        'guard_name' => 'api',
                    ])->id;
                }

                return $check->id;
            })
            ->toArray();

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin'),
            // 'pelanggan' => $insertPermissions('pelanggan')
        ];

         foreach ($permissionIdsByRole as $roleName => $permissionIds) {
            // pastikan role ada, kalau tidak akan dibuat otomatis
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'api',
                'full_name' => ucfirst($roleName)]
            );

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn ($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        // foreach ($permissionIdsByRole as $role => $permissionIds) {
        //     $role = Role::whereName($role)->first();

        //     DB::table('role_has_permissions')
        //         ->insert(
        //             collect($permissionIds)->map(fn ($id) => [
        //                 'role_id' => $role->id,
        //                 'permission_id' => $id
        //             ])->toArray()
        //         );
        }
    }
}
