<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Classes;
use App\Models\Enrollments;
use App\Models\Years;

class EnrollmentsController extends Controller
{
    private Enrollments $enrollments;
    private Classes $classes;
    private Years $years;
    private string $base = '/school/enrollments';

    public function __construct()
    {
        $this->auth('school');
        $schoolId = $this->schoolid();
        $this->enrollments = new Enrollments($schoolId);
        $this->classes = new Classes($schoolId);
        $this->years = new Years($schoolId);
    }

    public function index(): void
    {
        $enrolled = null;
        $unenrolled = $this->enrollments->unenrolled();
        $classid = trim((string) ($_GET['class_id'] ?? ''));

        $currentYear = $this->years->currentYear();
        $defaultYearId = (string) ($currentYear['id'] ?? '');
        $yearid = trim((string) ($_GET['year_id'] ?? $defaultYearId));
        $selectedYear = $yearid !== '' ? $yearid : $defaultYearId;

        if ($classid !== '' && $selectedYear !== '') {
            $enrolled = $this->enrollments->enrolled((int) $selectedYear, (int) $classid);
        }

        $this->view([
            'title' => 'Enrollments Management',
            'content' => 'schools/enrollments',
            'enrolled' => $enrolled,
            'currentYearId' => $selectedYear,
            'currentClassId' => $classid,
            'unenrolled' => $unenrolled,
            'unenrolledCount' => is_array($unenrolled) ? count($unenrolled) : 0,
            'enrolledCount' => is_array($enrolled) ? count($enrolled) : 0,
            'classes' => $this->classes->all(),
            'years' => $this->years->all(),
            'token' => $this->token(),
            'script' => 'enrollments',
        ]);
    }

    public function store(): void
    {
        $token = (string) ($_POST['_token'] ?? '');
        if (!$this->validateToken($token)) {
            $this->flash('error', 'Invalid or expired security token. Please try again.');
            $this->redirect($this->base);
            return;
        }

        $yearId = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT) ?: 0;
        $classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT) ?: 0;
        $rawIds = $_POST['student_ids'] ?? [];

        if ($yearId <= 0 || $classId <= 0) {
            $this->flash('error', 'Please select both an academic year and a class.');
            $this->redirect($this->base);
            return;
        }

        if (!is_array($rawIds) || empty($rawIds)) {
            $this->flash('error', 'Please select at least one student to enroll.');
            $this->redirect("{$this->base}?year_id={$yearId}&class_id={$classId}");
            return;
        }

        $studentIds = array_values(array_unique(array_filter(
            array_map(static fn($id) => filter_var($id, FILTER_VALIDATE_INT), $rawIds),
            static fn($id) => is_int($id) && $id > 0
        )));

        if (empty($studentIds)) {
            $this->flash('error', 'No valid student selections were provided.');
            $this->redirect("{$this->base}?year_id={$yearId}&class_id={$classId}");
            return;
        }

        $enrolledCount = 0;
        $failedCount = 0;

        foreach ($studentIds as $studentId) {
            $saved = $this->enrollments->enroll([
                'year_id' => $yearId,
                'class_id' => $classId,
                'student_id' => $studentId,
                'enrollment_date' => $_POST['enrollment_date'] ?? date('Y-m-d'),
            ]);

            if ($saved) {
                $enrolledCount++;
            } else {
                error_log('Failed to enroll student ID: ' . $studentId);
                $failedCount++;
            }
        }

        if ($enrolledCount > 0 && $failedCount === 0) {
            $this->flash('success', "{$enrolledCount} student(s) enrolled successfully!");
        } elseif ($enrolledCount > 0) {
            $this->flash('warning', "{$enrolledCount} student(s) enrolled, {$failedCount} could not be saved.");
        } else {
            $this->flash('error', 'Failed to enroll selected students.');
        }

        $this->redirect("{$this->base}?year_id={$yearId}&class_id={$classId}");
    }

    public function status(): void
    {
        $yearId = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT) ?: 0;
        $classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT) ?: 0;

        $query = [];
        if ($yearId > 0) {
            $query['year_id'] = $yearId;
        }
        if ($classId > 0) {
            $query['class_id'] = $classId;
        }

        $redirectUrl = $this->base . (!empty($query) ? '?' . http_build_query($query) : '');
        $token = (string) ($_POST['_token'] ?? '');
        if (!$this->validateToken($token)) {
            $this->flash('error', 'Invalid or expired security token.');
            $this->redirect($redirectUrl);
            return;
        }

        $enrollmentId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $status = trim((string) ($_POST['status'] ?? ''));
        $allowedStatuses = ['active', 'transferred', 'completed', 'dropped'];

        if ($enrollmentId <= 0 || !in_array($status, $allowedStatuses, true)) {
            $this->flash('error', 'Invalid enrollment record or status.');
            $this->redirect($redirectUrl);
            return;
        }

        $updated = $this->enrollments->updateStatus($enrollmentId, $status);

        if ($updated) {
            $this->flash('success', 'Enrollment status updated.');
        } else {
            $this->flash('error', 'Failed to update enrollment status.');
        }

        $this->redirect($redirectUrl);
    }
}
