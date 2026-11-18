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
                    code VARCHAR(50) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                    UNIQUE(school_id, code)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

                // Subjects
                "CREATE TABLE IF NOT EXISTS subjects (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    code VARCHAR(50) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                    UNIQUE(school_id, code)
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
                    UNIQUE KEY uq_years_school_year (school_id, year),
                    INDEX idx_years_school_status (school_id, status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                // Terms Table
                "CREATE TABLE IF NOT EXISTS terms (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    year_id BIGINT UNSIGNED NOT NULL,
                    name VARCHAR(50) NOT NULL,
                    status ENUM('active', 'closed', 'upcoming') NOT NULL DEFAULT 'upcoming',
                    start_date DATE NULL,
                    end_date DATE NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_terms_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                    CONSTRAINT fk_terms_year FOREIGN KEY (year_id) REFERENCES years(id) ON DELETE CASCADE,
                    UNIQUE KEY uq_terms_school_year_name (school_id, year_id, name),
                    INDEX idx_terms_school_status (school_id, status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

                // Allocations Table (Teacher-Subject Handover Management)
                "CREATE TABLE IF NOT EXISTS allocations (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    school_id BIGINT UNSIGNED NOT NULL,
                    year_id BIGINT UNSIGNED NOT NULL,
                    class_id BIGINT UNSIGNED NOT NULL,
                    subject_id BIGINT UNSIGNED NOT NULL,
                    teacher_id BIGINT UNSIGNED NOT NULL,
                    from_date DATE NOT NULL,
                    to_date DATE NULL DEFAULT NULL,
                    status ENUM('active', 'closed') NOT NULL DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                    INDEX idx_allocations_school_year (school_id, year_id),
                    INDEX idx_allocations_active_lookup (school_id, year_id, class_id, subject_id, status),
                    INDEX idx_allocations_teacher (school_id, teacher_id, status),

                    CONSTRAINT fk_allocations_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
                    CONSTRAINT fk_allocations_year FOREIGN KEY (year_id) REFERENCES years(id) ON DELETE CASCADE,
                    CONSTRAINT fk_allocations_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
                    CONSTRAINT fk_allocations_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
                    CONSTRAINT fk_allocations_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
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
