<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\School;

class SchoolController extends Controller
{
    private School $school;
    public function __construct()
    {
        $this->auth('admin');
        $this->school = new School();
    }

    // Display schools and load school for editing
    public function index(): void
    {
        $school = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $school = $this->school->find($id);

            if (!$school) {
                $this->flash('error', 'School not found.');
                $this->redirect('/schools');
            }
        }
        $schools = $this->school->all();

        $this->view([
            'title' => 'Manage Schools',
            'content' => 'admin/schools',
            'school' => $school,
            'schools' => $schools,
            'token' => $this->token(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/schools');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if ($code === '' || $name === '' || $address === '') {
            $this->flash('error', 'School code, name and address are required.');
            $this->redirect('/schools');
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $this->flash('error', 'Invalid school status.');
            $this->redirect('/schools');
        }

        $data = [
            'code'    => $this->format($code, 'upper'),
            'name'    => $this->format($name, 'title'),
            'address' => $this->format($address, 'upper'),
            'status'  => $this->format($status, 'lower'),
        ];

        $existing = null;
        if ($id) {
            $existing = $this->school->find($id);
            if (!$existing) {
                $this->flash('error', 'School not found.');
                $this->redirect('/schools');
            }
            $data['logo'] = $existing['logo'] ?? null;
        }

        // Process new logo upload if supplied
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if (getimagesize($_FILES['logo']['tmp_name']) === false) {
                $this->flash('error', 'Please upload a valid image.');
                $this->redirect('/schools');
            }

            $logo = $this->uploadFile($_FILES['logo'], 'schools');
            if ($logo === false) {
                $this->flash('error', 'Logo upload failed.');
                $this->redirect('/schools');
            }

            $data['logo'] = $logo;
        }

        // Save (Update or Create)
        if ($id) {
            $saved = $this->school->update($id, $data);
            if ($saved) {
                if (isset($logo) && !empty($existing['logo'])) {
                    $this->deleteFile($existing['logo']);
                }
                $this->flash('success', 'School updated successfully.');
            } else {
                if (isset($logo)) {
                    $this->deleteFile($logo);
                }
                $this->flash('error', 'Failed to update school.');
            }
        } else {
            $this->school->create($data);
            $this->flash('success', 'School created successfully.');
        }

        $this->redirect('/schools');
    }

    // Delete a school
    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'])) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/schools');
        }

        $id = filter_var(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid school ID.');
            $this->redirect('/schools');
        }

        $school = $this->school->find($id);
        if (!$school) {
            $this->flash('error', 'School not found.');
            $this->redirect('/schools');
        }

        if ($this->school->delete($id)) {
            if (!empty($school['logo'])) {
                $this->deleteFile($school['logo']);
            }
            $this->flash('success', 'School deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete school');
        }
        $this->redirect('/schools');
    }
}
