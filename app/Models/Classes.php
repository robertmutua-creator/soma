<?php

namespace App\Models;

use App\Core\Model;

class Classes extends Model
{
    protected string $table = 'classes';

    public function highestLevelId(): ?int
    {
        $sql = "SELECT id
                FROM {$this->table}
                WHERE school_id = :school_id
                ORDER BY level DESC, id DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':school_id' => $this->schoolId,
        ]);

        $id = $stmt->fetchColumn();

        return $id !== false ? (int) $id : null;
    }
}
