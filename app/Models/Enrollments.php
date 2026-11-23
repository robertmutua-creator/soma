<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use Throwable;

class Enrollments extends Model
{
    protected string $table = 'enrollments';

    public function unenrolled(): array
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

    public function enroll(array $data): bool
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

    public function enrolled(int $year, int $class): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    u.*,
                    e.id AS enrollment_id,
                    e.year_id,
                    e.class_id,
                    e.enrollment_date,
                    e.status AS status,
                    e.status AS enrollment_status
                FROM {$this->table} e
                INNER JOIN users u
                    ON u.id = e.student_id
                    AND u.school_id = e.school_id
                WHERE e.school_id = :school
                  AND e.year_id = :year
                  AND e.class_id = :class
                  AND u.role = 'student'
                ORDER BY u.name ASC
            ");

            $stmt->execute([
                ':school' => $this->schoolId,
                ':year'   => $year,
                ':class'  => $class,
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function updateStatus(int $enrollmentId, string $status): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $params = [
                ':status' => $status,
                ':id'     => $enrollmentId,
            ];

            if ($this->schoolId !== null) {
                $sql .= " AND school_id = :school_id";
                $params[':school_id'] = $this->schoolId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function promote(int $fromYear, int $fromClass, int $toYear, int $toClass, int $student): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare("
                UPDATE {$this->table}
                SET status = 'completed'
                WHERE student_id = :student
                  AND year_id = :fromYear
                  AND class_id = :fromClass
                  AND status = 'active'
                  AND school_id = :school
            ");
            $stmt1->execute([
                ':student'   => $student,
                ':fromYear'  => $fromYear,
                ':fromClass' => $fromClass,
                ':school'    => $this->schoolId,
            ]);

            if ($stmt1->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $enrolled = $this->enroll([
                'year_id'    => $toYear,
                'student_id' => $student,
                'class_id'   => $toClass,
            ]);

            if (!$enrolled) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function graduate(int $fromClass, int $fromYear, int $studentId): bool
    {
        if ($fromClass <= 0 || $fromYear <= 0 || $studentId <= 0) {
            return false;
        }

        try {
            $sql = "UPDATE {$this->table}
                    SET status = 'graduated'
                    WHERE student_id = :student_id
                      AND class_id = :class_id
                      AND year_id = :year_id
                      AND status = 'active'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':student_id' => $studentId,
                ':class_id'   => $fromClass,
                ':year_id'    => $fromYear,
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            error_log('Enrollments::graduate error: ' . $e->getMessage());
            return false;
        }
    }

    public function demote(int $fromYear, int $fromClass, int $toYear, int $toClass, int $student): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt1 = $this->db->prepare(
                "DELETE FROM {$this->table}
                 WHERE school_id = :school
                   AND student_id = :student
                   AND class_id = :class
                   AND year_id = :year
                   AND status = 'active'"
            );
            $stmt1->execute([
                ':school'  => $this->schoolId,
                ':student' => $student,
                ':class'   => $fromClass,
                ':year'    => $fromYear,
            ]);

            if ($stmt1->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $stmt2 = $this->db->prepare(
                "UPDATE {$this->table}
                 SET status = 'active'
                 WHERE student_id = :student
                   AND class_id = :class
                   AND year_id = :year
                   AND status = 'completed'
                   AND school_id = :school"
            );
            $stmt2->execute([
                ':student' => $student,
                ':class'   => $toClass,
                ':school'  => $this->schoolId,
                ':year'    => $toYear,
            ]);

            if ($stmt2->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Enrollments::demote error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateEnrollment(int $classId, int $yearId, int $studentId): bool
    {
        if ($this->schoolId <= 0 || $classId <= 0 || $yearId <= 0 || $studentId <= 0) {
            return false;
        }

        try {
            $sql = "UPDATE {$this->table}
                    SET status = 'active'
                    WHERE school_id = :school_id
                      AND class_id = :class_id
                      AND year_id = :year_id
                      AND student_id = :student_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':school_id'  => $this->schoolId,
                ':class_id'   => $classId,
                ':year_id'    => $yearId,
                ':student_id' => $studentId,
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            error_log('Enrollments::updateEnrollment error: ' . $e->getMessage());
            return false;
        }
    }
}
