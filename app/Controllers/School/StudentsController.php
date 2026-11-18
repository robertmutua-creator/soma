<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\School;
use App\Models\User;

class StudentsController extends Controller
{
    private User $student;
    private string $base;

    public function __construct()
    {
        $this->auth('school');
        $this->student = new User($this->schoolid());
        $this->base = '/school/students';
    }

    public function index(): void
    {
        $student = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $student = $this->student->find($id);
            if (!$student || $student['role'] !== 'student') {
                $this->flash('error', 'Student not found.');
                $this->redirect($this->base);
            }
        }
        $students = $this->student->all(['role' => 'student']);
        $this->view([
            'title' => 'Manage Students',
            'content' => 'schools/students',
            'student' => $student,
            'students' => $students,
            'token' => $this->token(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $adm = trim($_POST['adm'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $upi = trim($_POST['upi'] ?? null);
        $doa = $_POST['doa'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $guardian = trim($_POST['guardianName'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $schoolCode = ((new School())->find($this->schoolid()))['code'] ?? '';
        $username = $adm . '@' . $schoolCode;

        if (!in_array($status, ['active', 'graduated', 'transferred'], true)) {
            $status = 'active';
        }

        if ($schoolCode === '') {
            $this->flash('error', 'School code not yet set.');
            $this->redirect($this->base);
        }

        if ($name === '' || $adm === '' || $phone === '' || $dob === '' || $doa === '' || $guardian === '') {
            $this->flash('error', 'All fields are required.');
            $this->redirect($this->base);
        }

        $data = [
            'username' => $this->format($username, 'lower'),
            'name' => $this->format($name, 'title'),
            'role' => 'student',
            'password' => password_hash(($this->format($adm, 'upper')), PASSWORD_DEFAULT),
            'phone' => $phone,
            'guardian' => $this->format($guardian, 'title'),
            'dob' => $dob,
            'doa' => $doa,
            'status' => $status,
            'adm' => $this->format($adm, 'upper'),
            'upi' => $upi !== '' ? $this->format($upi, 'upper') : null,
        ];

        $existing = null;
        if ($id) {
            $existing = $this->student->find($id);
            if (!$existing || $existing['role'] !== 'student') {
                $this->flash('error', 'Student not found.');
                $this->redirect($this->base);
            }
            $data['profile_photo'] = $existing['profile_photo'] ?? null;
        }
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if (getimagesize($_FILES['profile_photo']['tmp_name']) === false) {
                $this->flash('error', 'Please upload a valid image.');
                $this->redirect($this->base);
            }

            $profilePath = $this->uploadFile($_FILES['profile_photo'], 'students');
            if ($profilePath === false) {
                $this->flash('error', 'Profile upload failed.');
                $this->redirect($this->base);
            }
            $data['profile_photo'] = $profilePath;
        }

        if ($id) {
            $saved = $this->student->update($id, $data);
            if ($saved) {
                if (isset($profilePath) && !empty($existing['profile_photo'])) {
                    $this->deleteFile($existing['profile_photo']);
                }
                $this->flash('success', 'Student updated successfully.');
            } else {
                if (isset($profilePath)) {
                    $this->deleteFile($profilePath);
                }
                $this->flash('error', 'Failed to update student');
            }
        } else {
            $this->student->create($data);
            $this->flash('success', 'Student registered successfully.');
        }
        $this->redirect($this->base);
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->flash('error', 'Invalid student ID.');
            $this->redirect($this->base);
        }

        $student = $this->student->find($id);
        if (!$student || $student['role'] !== 'student') {
            $this->flash('error', 'Student not found.');
            $this->redirect($this->base);
        }

        if ($this->student->delete($id)) {
            if (!empty($student['profile_photo'])) {
                $this->deleteFile($student['profile_photo']);
            }
            $this->flash('success', 'Student deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete the student.');
        }

        $this->redirect($this->base);
    }

    public function downloadTemplate(): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filament="student_import_template.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['adm', 'name', 'upi', 'dob', 'doa', 'guardian', 'phone', 'status']);
        fputcsv($output, ['ADM001', 'John Kamau', 'UPI1234', '2012-05-10', '2026-01-10', 'Mary Kamau', '072020202', 'active']);

        fclose($output);
        exit;
    }

    public function importBatch(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
        }

        if (!isset($_FILES['student_file']) || $_FILES['student_file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Please upload a CSV file.');
            $this->redirect($this->base);
        }

        $schoolCode = (new School())->find($this->schoolid())['code'] ?? '';
        if ($schoolCode === '') {
            $this->flash('error', 'School code not yet set.');
            $this->redirect($this->base);
            return;
        }

        $handle = fopen($_FILES['student_file']['tmp_name'], 'r');
        fgetcsv($handle);

        $success = 0;
        $failed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            [$adm, $name, $upi, $dob, $doa, $guardian, $phone, $status] = array_pad($row, 8, null);
            $adm = trim($adm ?? '');
            $name = trim($name ?? '');
            $upi = trim($upi ?? '');
            $dob = trim($dob ?? '');
            $doa = trim($doa ?? '');
            $guardian = trim($guardian ?? '');
            $phone = trim($phone ?? '');
            $status = trim($status ?? 'active');

            if (!in_array($status, ['active', 'graduated', 'transferred'], true)) {
                $status = 'active';
            }

            if ($name === '' || $adm === '' || $phone === '' || $dob === '' || $doa === '' || $guardian === '') {
                $failed++;
                continue;
            }

            $username = strtolower($adm . '@' . $schoolCode);

            $data = [
                'username' => $username,
                'name' => $this->format($name, 'title'),
                'role' => 'student',
                'password' => password_hash($this->format($adm, 'upper'), PASSWORD_DEFAULT),
                'phone' => $phone,
                'guardian' => $this->format($guardian, 'title'),
                'dob' => $dob,
                'doa' => $doa,
                'status' => $status,
                'adm' => $this->format($adm, 'upper'),
                'upi' => $upi !== '' ? $this->format($upi, 'upper') : null,
                'profile_photo' => null
            ];

            try {
                $this->student->create($data);
                $success++;
            } catch (\Throwable $e) {
                error_log($e->getMessage());
                $failed++;
            }
        }
        fclose($handle);

        $this->flash('success', "Batch import complete: {$success} registered successfully, {$failed} failed/skipped.");
        $this->redirect($this->base);
    }
}
