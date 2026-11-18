<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Classes;
use App\Models\Enrollments;
use App\Models\Years;
use Exception;

class EnrollmentsController extends Controller
{
    protected string $base = '/school/enrollments';
    protected Enrollments $enrollmentsModel;
    public function __construct()
    {
        $this->enrollmentsModel = new Enrollments($this->schoolid());
    }

    public function index(): void
    {
        $schoolId = $this->schoolid();

        $years = (new Years($this->schoolid()))->all();
        $activeYear = array_filter($years, fn($y) => $y['status'] === 'active');
        $currentYearId = $_GET['year_id'] ?? (!empty($activeYear) ? reset($activeYear)['id'] : null);

        $classes = (new Classes($schoolId))->all();

        $unenrolledStudents = $this->enrollmentsModel->unenrolledStudents();

        $data = [
            'token'              => $this->token(),
            'unenrolledStudents' => $unenrolledStudents,
            'years'              => $years,
            'currentYearId'      => $currentYearId,
            'classes'            => $classes,
            'content'           => 'schools/enrollments',
            'script' => 'enrollments',
            'title' => 'Enroll Students',
        ];

        $this->view($data);
    }

    public function store(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
            return;
        }

        $studentIds = $_POST['student_ids'] ?? [];
        $classId    = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
        $yearId     = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT);
        $enrollDate = $_POST['enrollment_date'] ?? date('Y-m-d');

        if (empty($studentIds) || !is_array($studentIds) || !$classId || !$yearId) {
            $this->flash('error', 'Please select at least one student, a target class, and an academic year.');
            $this->redirect($this->base . ($yearId ? '?year_id=' . $yearId : ''));
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($studentIds as $studentId) {
            $sanitizedStudentId = filter_var($studentId, FILTER_VALIDATE_INT);
            if (!$sanitizedStudentId) {
                $failCount++;
                continue;
            }

            $payload = [
                'student_id'      => $sanitizedStudentId,
                'class_id'        => $classId,
                'year_id'         => $yearId,
                'enrollment_date' => $enrollDate
            ];

            try {
                if ($this->enrollmentsModel->enrollStudent($payload)) {
                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (Exception $e) {
                error_log("Batch enrollment error for student {$sanitizedStudentId}: " . $e->getMessage());
                $failCount++;
            }
        }

        if ($successCount > 0) {
            $this->flash('success', "Successfully enrolled {$successCount} student(s)!");
        }
        if ($failCount > 0) {
            $this->flash('error', "Failed to enroll {$failCount} student(s).");
        }

        $this->redirect($this->base . '?year_id=' . $yearId);
    }
}
