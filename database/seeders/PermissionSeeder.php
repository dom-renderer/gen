<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resourcePermissionsScaffolding = [
            [
                'title' => ' Listing',
                'name' => '.index'
            ],
            [
                'title' => ' Add',
                'name' => '.create'
            ],
            [
                'title' => ' Save',
                'name' => '.store'
            ],
            [
                'title' => ' Edit',
                'name' => '.edit'
            ],
            [
                'title' => ' Update',
                'name' => '.update'
            ],
            [
                'title' => 'View',
                'name' => '.show'
            ],
            [
                'title' => ' Delete',
                'name' => '.destroy'
            ]
        ];

        $resourcePermissions = [
            [
                'title' => 'Users',
                'name' => 'users'
            ],
            [
                'title' => 'Roles',
                'name' => 'roles'
            ],
            [
                'title' => 'Introducers',
                'name' => 'introducers'
            ]
        ];

        $extraPermissions = [
            [
                'title' => 'Settings View',
                'name' => 'settings.index'
            ],
            [
                'title' => 'Settings Update',
                'name' => 'settings.update'
            ],
            [
                'title' => 'Cases Listing',
                'name' => 'cases.index'
            ],
            [
                'title' => 'Cases Add',
                'name' => 'cases.create'
            ],
            [
                'title' => 'Cases Store',
                'name' => 'cases.submission'
            ],
            [
                'title' => 'Cases Edit',
                'name' => 'cases.edit'
            ],
            [
                'title' => 'Cases View',
                'name' => 'cases.view'
            ],
            [
                'title' => 'Manage Case Manager',
                'name' => 'cases.case-manager'
            ]
        ];

        $permissions = [];

        foreach ($resourcePermissions as $rP) {
            foreach ($resourcePermissionsScaffolding as $scaffold) {
                $permissions[] = [
                    'title' => $rP['title'] . $scaffold['title'],
                    'name' => $rP['name'] . $scaffold['name']
                ];
            }
        }

        $permissions = array_merge($permissions, $extraPermissions);

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}