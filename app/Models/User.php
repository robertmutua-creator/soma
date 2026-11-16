<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class User extends Model
{
    protected string $table = 'users';
    // Fetch a user record for authentication purposes
    public function findByUsername(string $username): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username=:username");
            $stmt->execute([
                ':username' => $username,
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getAdmins(): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT u.*, s.name as school FROM users u JOIN schools s ON u.school_id = s.id WHERE u.role = 'school'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $admins ?: false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
