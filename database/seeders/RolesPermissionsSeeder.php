<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = [];
        foreach (config('collabccu.roles') as $roleName => $canRegister) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }
        foreach (config('collabccu.permissions') as $permissionGroup => $permissions) {
            foreach ($permissions as $permissionName => $allowedRoles) {

                $p = Permission::firstOrCreate([
                    'name' => implode('.', [$permissionGroup, $permissionName]),
                ]);
                if (!empty($allowedRoles)) {
                    $p->syncRoles(
                        array_map(fn ($r) => $roles[$r], $allowedRoles)
                    );
                }
            }
        }
    }
}
