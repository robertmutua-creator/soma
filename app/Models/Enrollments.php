<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Enrollments extends Model
{
    protected string $table = 'enrollments';

    public function unenrolledStudents(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.* 
                FROM users u 
                WHERE u.school_id = :school 
                  AND u.role = 'student'
                  AND u.id NOT IN (
                      SELECT student_id 
                      FROM {$this->table} 
                      WHERE school_id = :school_sub
                  )
                ORDER BY u.name ASC
            ");
            $stmt->execute([
                ':school'     => $this->schoolId,
                ':school_sub' => $this->schoolId
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function enrollStudent(array $data): bool
    {
        try {
            $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (school_id, year_id, student_id, class_id, enrollment_date, status) 
            VALUES (:school_id, :year_id, :student_id, :class_id, :enrollment_date, 'active')
        ");

            return $stmt->execute([
                ':school_id'       => $this->schoolId,
                ':year_id'         => $data['year_id'],
                ':student_id'      => $data['student_id'],
                ':class_id'        => $data['class_id'],
                ':enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d')
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
