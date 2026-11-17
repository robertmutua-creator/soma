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
                    name VARCHAR(255) NOT NULL,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('admin', 'school', 'teacher', 'student') NOT NULL,
                    profile_photo VARCHAR(255) NULL,

                    -- Student-specific fields (NULL for staff/admins)
                    upi VARCHAR(50) NULL UNIQUE,
                    adm VARCHAR(50) NULL,
                    dob DATE NULL,
                    doa DATE NULL,
                    guardian VARCHAR(255) NULL,
                    phone VARCHAR(30) NULL,
                    status ENUM('active', 'graduated', 'transferred') NULL DEFAULT 'active',

                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                    CONSTRAINT fk_users_school
                        FOREIGN KEY (school_id) REFERENCES schools(id)
                        ON DELETE CASCADE,

                    UNIQUE KEY uq_school_adm (school_id, adm),
                    INDEX idx_school_role (school_id, role)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
                // Classes
                "CREATE TABLE IF NOT EXISTS classes (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    code varchar(50) NOT NULL,
                    name varchar(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (school_id) REFERENCES schools(id),
                    UNIQUE(school_id,code)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                // Subjects
                "CREATE TABLE IF NOT EXISTS subjects (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    code varchar(50) NOT NULL,
                    name varchar(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (school_id) REFERENCES schools(id),
                    UNIQUE(school_id,code)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                // Years Table
                "CREATE TABLE IF NOT EXISTS years (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    year VARCHAR(50) NOT NULL,
                    status ENUM('active', 'closed', 'upcoming') NOT NULL DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_years_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                    UNIQUE KEY uq_years_school_year (school_id, year)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"

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
