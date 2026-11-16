<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\User;

class TeachersController extends Controller
{
    private User $teacher;
    private string $base;
    public function __construct()
    {
        $this->auth('school');
        $this->teacher = new User($this->schoolid());
        $this->base = '/school/teachers';
    }

    public function index(): void
    {
        $teacher = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $teacher = $this->teacher->find($id);
            if (!$teacher || $teacher['role'] !== 'teacher') {
                $this->flash('error', 'Teacher not found.');
                $this->redirect($this->base);
            }
        }
        $teachers = $this->teacher->all(['role' => 'teacher']);
        $this->view([
            'title' => 'Manage Teachers',
            'content' => 'schools/teachers',
            'teacher' => $teacher,
            'teachers' => $teachers,
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
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $phone = $_POST['phone'] ?? '';

        if ($name === '' || $username === '' || $phone === '') {
            $this->flash('error', 'Both username, name and phone are required.');
            $this->redirect($this->base);
        }

        $data = [
            'username' => $username,
            'name' => $this->format($name, 'title'),
            'role' => 'teacher',
            'password' => password_hash($username, PASSWORD_DEFAULT),
            'phone' => $phone,
        ];

        $existing = null;
        if ($id) {
            $existing = $this->teacher->find($id);
            if (!$existing || $existing['role'] !== 'teacher') {
                $this->flash('error', 'Teacher not found.');
                $this->redirect($this->base);
            }
            $data['profile_photo'] = $existing['profile_photo'] ?? null;
        }
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if (getimagesize($_FILES['profile_photo']['tmp_name']) === false) {
                $this->flash('error', 'Please upload a valid image.');
                $this->redirect($this->base);
            }

            $profilePath = $this->uploadFile($_FILES['profile_photo'], 'teachers');
            if ($profilePath === false) {
                $this->flash('error', 'Profile upload failed.');
                $this->redirect($this->base);
            }
            $data['profile_photo'] = $profilePath;
        }

        if ($id) {
            $saved = $this->teacher->update($id, $data);
            if ($saved) {
                if (isset($profilePath) && !empty($existing['profile_photo'])) {
                    $this->deleteFile($existing['profile_photo']);
                }
                $this->flash('success', 'Teacher updated successfully.');
            } else {
                if (isset($profilePath)) {
                    $this->deleteFile($profilePath);
                }
                $this->flash('error', 'Failed to update teacher');
            }
        } else {
            $this->teacher->create($data);
            $this->flash('success', 'Teacher registered successfully.');
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
            $this->flash('error', 'Invalid teacher ID.');
            $this->redirect($this->base);
        }

        $teacher = $this->teacher->find($id);
        if (!$teacher || $teacher['role'] !== 'teacher') {
            $this->flash('error', 'Teacher not found.');
            $this->redirect($this->base);
        }

        if ($this->teacher->delete($id)) {
            if (!empty($teacher['profile_photo'])) {
                $this->deleteFile($teacher['profile_photo']);
            }
            $this->flash('success', 'Teacher deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete the teacher.');
        }

        $this->redirect($this->base);
    }
}
