<?php

namespace App\Controllers;

use App\Core\Migrations;
use App\Models\User;

class SetupController
{
    public function migrate(): void
    {
        try {
            Migrations::migrate();
            echo 'Migrations completed successfully.';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'Migration failed.';
        }
    }

    public function admin(): void
    {
        $data = [
            'username' => 'admin@soma',
            'role' => 'admin',
            'password' => password_hash('123', PASSWORD_DEFAULT),
        ];

        try {
            (new User())->create($data);
            echo 'Admin created successfully!';
        } catch (\Throwable $th) {
            error_log('Failed to create admin.' . $th->getMessage());
            echo 'Failed';
        }
    }
}
