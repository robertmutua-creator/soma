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

    public function promotions(): void
    {
        $this->base = '/school/promotions';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $highestClass = $this->classes->highestLevelId();

        if ($method === 'GET') {
            try {
                $selectedFromYear  = trim((string) ($_GET['fromYear'] ?? ''));
                $selectedFromClass = trim((string) ($_GET['fromClass'] ?? ''));
                $selectedToYear    = trim((string) ($_GET['toYear'] ?? ''));
                $selectedToClass   = trim((string) ($_GET['toClass'] ?? ''));

                $students = [];
                if ($selectedFromClass !== '' && $selectedFromYear !== '') {
                    $students = $this->enrollments->enrolled(
                        (int) $selectedFromYear,
                        (int) $selectedFromClass
                    );
                }

                $this->view([
                    'title'             => 'Promotions Management',
                    'content'           => 'schools/promotions',
                    'years'             => $this->years->all(),
                    'classes'           => $this->classes->all(),
                    'selectedFromYear'  => $selectedFromYear,
                    'selectedFromClass' => $selectedFromClass,
                    'selectedToYear'    => $selectedToYear,
                    'selectedToClass'   => $selectedToClass,
                    'token'             => $this->token(),
                    'script'            => 'promotions',
                    'students'          => $students,
                    'highestClass'      => $highestClass,
                ]);
            } catch (\Throwable $th) {
                error_log('Promotions GET error: ' . $th->getMessage());
                $this->flash('error', 'Unable to load promotions data.');
                $this->view([
                    'title'             => 'Promotions Management',
                    'content'           => 'schools/promotions',
                    'years'             => [],
                    'classes'           => [],
                    'selectedFromYear'  => '',
                    'selectedFromClass' => '',
                    'selectedToYear'    => '',
                    'selectedToClass'   => '',
                    'token'             => $this->token(),
                    'script'            => 'promotions',
                    'students'          => [],
                    'highestClass'      => null,
                ]);
            }
        }

        if ($method === 'POST') {
            $fromYear  = filter_input(INPUT_POST, 'fromYear', FILTER_VALIDATE_INT) ?: 0;
            $fromClass = filter_input(INPUT_POST, 'fromClass', FILTER_VALIDATE_INT) ?: 0;
            $toYear    = filter_input(INPUT_POST, 'toYear', FILTER_VALIDATE_INT) ?: 0;
            $toClass   = filter_input(INPUT_POST, 'toClass', FILTER_VALIDATE_INT) ?: 0;

            $query = array_filter([
                'fromYear'  => $fromYear > 0 ? $fromYear : null,
                'fromClass' => $fromClass > 0 ? $fromClass : null,
                'toYear'    => $toYear > 0 ? $toYear : null,
                'toClass'   => $toClass > 0 ? $toClass : null,
            ]);

            $redirectUrl = $this->base . (!empty($query) ? '?' . http_build_query($query) : '');

            try {
                $token = (string) ($_POST['_token'] ?? '');
                if (!$this->validateToken($token)) {
                    $this->flash('error', 'Invalid or expired security token. Please try again.');
                    $this->redirect($redirectUrl);
                }

                $isGraduation = $highestClass !== null && $highestClass > 0 && $fromClass === $highestClass;

                if ($isGraduation) {
                    if ($fromYear <= 0) {
                        $this->flash('error', 'Please select From Year for graduation.');
                        $this->redirect($redirectUrl);
                    }

                    $rawIds = $_POST['student_ids'] ?? [];
                    if (!is_array($rawIds) || empty($rawIds)) {
                        $this->flash('error', 'Please select at least one student to graduate.');
                        $this->redirect($redirectUrl);
                    }

                    $studentIds = array_values(array_unique(array_filter(
                        array_map(static fn($id) => filter_var($id, FILTER_VALIDATE_INT), $rawIds),
                        static fn($id) => is_int($id) && $id > 0
                    )));

                    if (empty($studentIds)) {
                        $this->flash('error', 'No valid student selections were provided.');
                        $this->redirect($redirectUrl);
                    }

                    $graduateCount = 0;
                    $failedCount   = 0;

                    foreach ($studentIds as $studentId) {
                        $graduated = $this->enrollments->graduate($fromClass, $fromYear, $studentId);

                        if ($graduated) {
                            $graduateCount++;
                        } else {
                            $failedCount++;
                            error_log("Graduation failed for student ID {$studentId} ({$fromYear}/{$fromClass})");
                        }
                    }

                    if ($graduateCount > 0 && $failedCount === 0) {
                        $this->flash('success', "{$graduateCount} student(s) graduated successfully!");
                    } elseif ($graduateCount > 0) {
                        $this->flash('warning', "{$graduateCount} student(s) graduated, {$failedCount} could not be graduated.");
                    } else {
                        $this->flash('error', 'Failed to graduate the selected students.');
                    }

                    $this->redirect($redirectUrl);
                }

                if ($fromYear <= 0 || $fromClass <= 0 || $toYear <= 0 || $toClass <= 0) {
                    $this->flash('error', 'Please select From Year, From Class, To Year, and To Class.');
                    $this->redirect($redirectUrl);
                }

                if ($fromYear === $toYear || $fromClass === $toClass) {
                    $this->flash('error', 'Promotion requires a different target academic year and class (use class transfer for same-year moves).');
                    $this->redirect($redirectUrl);
                }

                $rawIds = $_POST['student_ids'] ?? [];
                if (!is_array($rawIds) || empty($rawIds)) {
                    $this->flash('error', 'Please select at least one student to promote.');
                    $this->redirect($redirectUrl);
                }

                $studentIds = array_values(array_unique(array_filter(
                    array_map(static fn($id) => filter_var($id, FILTER_VALIDATE_INT), $rawIds),
                    static fn($id) => is_int($id) && $id > 0
                )));

                if (empty($studentIds)) {
                    $this->flash('error', 'No valid student selections were provided.');
                    $this->redirect($redirectUrl);
                }

                $promotedCount = 0;
                $failedCount   = 0;

                foreach ($studentIds as $studentId) {
                    $promoted = $this->enrollments->promote(
                        $fromYear,
                        $fromClass,
                        $toYear,
                        $toClass,
                        $studentId
                    );

                    if ($promoted) {
                        $promotedCount++;
                    } else {
                        $failedCount++;
                        error_log("Promotion failed for student ID {$studentId} ({$fromYear}/{$fromClass} -> {$toYear}/{$toClass})");
                    }
                }

                if ($promotedCount > 0 && $failedCount === 0) {
                    $this->flash('success', "{$promotedCount} student(s) promoted successfully!");
                } elseif ($promotedCount > 0) {
                    $this->flash('warning', "{$promotedCount} student(s) promoted, {$failedCount} could not be promoted.");
                } else {
                    $this->flash('error', 'Failed to promote the selected students.');
                }
            } catch (\Throwable $th) {
                error_log('Promotions POST error: ' . $th->getMessage());
                $this->flash('error', 'An error occurred while processing students.');
            }

            $this->redirect($redirectUrl);
        }
    }
}
