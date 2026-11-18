<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Allocations extends Model
{
    protected string $table = 'allocations';
    public function allocations(int $yearId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT a.*,c.name as class_name,c.code as class_code,s.name as subject_name,s.code as subject_code,u.name as teacher_name FROM allocations a JOIN classes c ON a.class_id=c.id JOIN subjects s ON a.subject_id=s.id JOIN users u ON a.teacher_id=u.id WHERE a.school_id=:school AND a.year_id=:year ORDER BY FIELD(a.status,'active','closed') ASC, c.name ASC, s.name ASC");
            $stmt->execute([
                ':school' => $this->schoolId,
                ':year' => $yearId
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function assign(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $stmtClose = $this->db->prepare("
            UPDATE allocations 
            SET status = 'closed', to_date = :from_date 
            WHERE school_id = :school 
              AND year_id = :year 
              AND class_id = :class 
              AND subject_id = :subject 
              AND status = 'active'
        ");
            $stmtClose->execute([
                ':from_date' => $data['from_date'],
                ':school' => $this->schoolId,
                ':year' => $data['year_id'],
                ':class' => $data['class_id'],
                ':subject' => $data['subject_id']
            ]);

            $stmtInsert = $this->db->prepare("
            INSERT INTO allocations (school_id, year_id, class_id, subject_id, teacher_id, from_date, status) 
            VALUES (:school, :year, :class, :subject, :teacher, :from_date, 'active')
        ");
            $stmtInsert->execute([
                ':school' => $this->schoolId,
                ':year' => $data['year_id'],
                ':class' => $data['class_id'],
                ':subject' => $data['subject_id'],
                ':teacher' => $data['teacher_id'],
                ':from_date' => $data['from_date']
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function findActive(int $yearId, int $classId, int $subjectId): ?array
    {
        try {
            $stmt = $this->db->prepare("
            SELECT * FROM allocations 
            WHERE school_id = :school 
              AND year_id = :year 
              AND class_id = :class 
              AND subject_id = :subject 
              AND status = 'active'
            LIMIT 1
        ");
            $stmt->execute([
                ':school' => $this->schoolId,
                ':year'   => $yearId,
                ':class'  => $classId,
                ':subject' => $subjectId
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
