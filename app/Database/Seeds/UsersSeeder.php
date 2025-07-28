<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Test User 1',
                'email'    => 'test1@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ],
            [
                'name'     => 'Test User 2',
                'email'    => 'test2@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
