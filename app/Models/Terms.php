<?php

namespace App\Models;

use App\Core\Model;

class Terms extends Model
{
    protected string $table = 'terms';

    public function current(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, y.year AS year_name 
             FROM {$this->table} t 
             JOIN years y ON t.year_id = y.id 
             WHERE t.school_id = :school_id AND t.status = 'active' AND y.status='active' 
             ORDER BY t.id DESC LIMIT 1"
        );
        $stmt->execute(['school_id' => $this->schoolId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
