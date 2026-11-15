<?php

namespace App\Controllers;

use App\Core\Migrations;

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
}
