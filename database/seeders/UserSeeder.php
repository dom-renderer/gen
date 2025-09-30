<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'roles' => [
                    'admin'
                ],
                'user' => [
                    'name' => 'Super Admin',
                    'email' => 'admin@gmail.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 9999999999,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Elizabeth Morgan',
                    'email' => 'elizabeth.morgan@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 8888888888,
                    'password' => 12345678,
                    'added_by' => 1
                ]
                ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Natisha Ward',
                    'email' => 'natisha.ward@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 7777777777,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Kayla Headley',
                    'email' => 'kayla.hreadley@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 7777777777,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Andreanna Pero',
                    'email' => 'andreanna.pero@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 6666666666,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Gavin Brewster',
                    'email' => 'gavin.brewster@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 5555555555,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ],
            [
                'roles' => [
                    'client-service-team'
                ],
                'user' => [
                    'name' => 'Nikita Gibson',
                    'email' => 'nikita.gibson@geneva.com',
                    'dial_code' => 41,
                    'status' => 1,
                    'phone_number' => 4444444444,
                    'password' => 12345678,
                    'added_by' => 1
                ]
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['dial_code' => $user['user']['dial_code'], 'phone_number' => $user['user']['phone_number']], $user['user'])->syncRoles($user['roles']);
        }
    }
}
