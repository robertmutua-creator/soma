<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Years extends Model
{
    protected string $table = 'years';

    public function currentYear(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT *
                FROM {$this->table}
                WHERE school_id = :school_id
                  AND status ='active'
                LIMIT 1
            ");

            $stmt->execute([
                ':school_id' => $this->schoolId,
            ]);

            $year = $stmt->fetch(PDO::FETCH_ASSOC);

            return $year ?: [];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
