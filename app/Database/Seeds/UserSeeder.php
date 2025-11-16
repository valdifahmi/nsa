<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username' => 'admin',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Administrator',
            'role' => 'admin',
        ];

        $this->db->table('tb_users')->insert($data);
    }
}
