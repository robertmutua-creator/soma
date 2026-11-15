<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    public static function connect(): PDO
    {
        $host = $_ENV['HOST'];
        $dbname = $_ENV['DATABASE'];
        $username = $_ENV['USER'];
        $password = $_ENV['PASSWORD'];
        try {
            return new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw new PDOException('Database connection failed.', 0, $e);
        }
    }
}
