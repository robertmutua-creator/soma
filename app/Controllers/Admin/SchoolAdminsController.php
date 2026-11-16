<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\School;
use App\Models\User;

class SchoolAdminsController extends Controller
{
    private School $school;
    private User $user;
    public function __construct()
    {
        $this->auth('admin');
        $this->school = new School();
        $this->user = new User();
    }

    // Display admins and load admin for editing
    public function index(): void
    {
        $schools = $this->school->all();
        $admins = $this->user->all(['role' => 'school']);
        $admin = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $admin = $this->user->find($id);

            if (!$admin) {
                $this->flash('error', 'Admin not found.');
                $this->redirect('/admins');
            }
        }

        $this->view([
            'title' => 'Manage School Admins',
            'content' => 'admin/admins',
            'admin' => $admin,
            'admins' => $admins,
            'schools' => $schools,
            'token' => $this->token(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/admins');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $school_id = trim($_POST['school_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');

        if ($school_id === '' || $name === '' || $username === '') {
            $this->flash('error', 'School, name and username are required.');
            $this->redirect('/admins');
        }

        $data = [
            'school_id'    => $school_id,
            'name'    => $this->format($name, 'title'),
            'username' => $this->format($username, 'lower'),
            'password'  => password_hash($username, PASSWORD_DEFAULT),
            'role' => 'school'
        ];

        $existing = null;
        if ($id) {
            $existing = $this->user->find($id);
            if (!$existing) {
                $this->flash('error', 'User not found.');
                $this->redirect('/admins');
            }
            $data['profile_photo'] = $existing['profile_photo'] ?? null;
        }

        // Process new profile upload if supplied
        if (isset($_FILES['profile']) && $_FILES['profile']['error'] !== UPLOAD_ERR_NO_FILE) {
            if (getimagesize($_FILES['profile']['tmp_name']) === false) {
                $this->flash('error', 'Please upload a valid image.');
                $this->redirect('/admins');
            }

            $profile = $this->uploadFile($_FILES['profile'], 'schools');
            if ($profile === false) {
                $this->flash('error', 'Profile upload failed.');
                $this->redirect('/admins');
            }

            $data['profile_photo'] = $profile;
        }

        // Save (Update or Create)
        if ($id) {
            $saved = $this->user->update($id, $data);
            if ($saved) {
                if (isset($profile) && !empty($existing['profile_photo'])) {
                    $this->deleteFile($existing['profile_photo']);
                }
                $this->flash('success', 'School Admin Updated Successfully.');
            } else {
                if (isset($profile)) {
                    $this->deleteFile($profile);
                }
                $this->flash('error', 'Failed to update admin.');
            }
        } else {
            $this->user->create($data);
            $this->flash('success', 'School Admin created successfully.');
        }

        $this->redirect('/admins');
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/admins');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid admin ID.');
            $this->redirect('/admins');
        }

        $admin = $this->user->find($id);
        if (!$admin) {
            $this->flash('error', 'Admin not found.');
            $this->redirect('/admins');
        }

        if ($this->user->delete($id)) {
            if (!empty($admin['profile_photo'])) {
                $this->deleteFile($admin['profile_photo']);
            }
            $this->flash('success', 'Admin deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete admin.');
        }

        $this->redirect('/admins');
    }
}
