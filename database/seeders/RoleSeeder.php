<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'title' => 'Admin',
                'name' => 'admin',
                'is_sytem_role' => 1
            ],
            [
                'title' => 'Policyholder',
                'name' => 'policy-holder',
                'is_sytem_role' => 1,
                'type' => 'customer'
            ],
            [
                'title' => 'Client Service Team',
                'name' => 'client-service-team',
                'is_sytem_role' => 1
            ]
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::updateOrCreate([
                'name' => $role['name']
            ], $role);
        }

        $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();

        if ($adminRole) {
            $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::pluck('id')->toArray());
        }

    }
}
