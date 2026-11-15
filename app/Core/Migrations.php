<?php

namespace App\Core;

use PDO;
use Throwable;

class Migrations
{
    public static function migrate(): void
    {
        try {
            $db = Database::connect();

            $sql = [

                // Schools table
                "CREATE TABLE IF NOT EXISTS schools (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    code VARCHAR(50) NOT NULL UNIQUE,
                    name VARCHAR(150) NOT NULL,
                    address TEXT NULL,
                    logo VARCHAR(255) NULL,
                    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                // Users table
                "CREATE TABLE IF NOT EXISTS users (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NULL,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(50) NOT NULL,
                    profile_photo VARCHAR(255) NULL,
                    status ENUM('active', 'graduated', 'transferred') NULL DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                    FOREIGN KEY (school_id) REFERENCES schools(id)
                    ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"

            ];

            foreach ($sql as $query) {
                $db->exec($query);
            }
        } catch (Throwable $e) {
            error_log("Migration failed: " . $e->getMessage());
            throw $e;
        }
    }
}
