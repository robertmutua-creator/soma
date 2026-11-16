<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Subjects;

class SubjectsController extends Controller
{
    private Subjects $subject;
    public function __construct()
    {
        $this->auth('school');
        $this->subject = new Subjects($this->schoolid());
    }

    // Display subjects and load subject for editing
    public function index(): void
    {
        $subject = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $subject = $this->subject->find($id);

            if (!$subject) {
                $this->flash('error', 'Subject not found.');
                $this->redirect('/school/subjects');
            }
        }
        $subjects = $this->subject->all();
        $this->view([
            'title' => 'Manage Subjects',
            'content' => 'schools/subjects',
            'subject' => $subject,
            'subjects' => $subjects,
            'token' => $this->token()
        ]);
    }

    // Save/Update Subject
    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/school/subjects');
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');

        if ($code === '' || $name === '') {
            $this->flash('error', 'Both code and name are required.');
            $this->redirect('/school/subjects');
        }

        $data = [
            'code' => $this->format($code, 'upper'),
            'name' => $this->format($name, 'title')
        ];

        $existing = null;
        if ($id) {
            $existing = $this->subject->find($id);
            if (!$existing) {
                $this->flash('error', 'Subject not found.');
                $this->redirect('/school/subjects');
            }

            if ($this->subject->update($id, $data)) {
                $this->flash('success', 'Subject updated successfully.');
            } else {
                $this->flash('error', 'Failed to update subject.');
            }
            $this->redirect('/school/subjects');
        }
        if ($this->subject->create($data)) {
            $this->flash('success', 'Subject registered successfully.');
        } else {
            $this->flash('error', 'Failed to register subject.');
        }
        $this->redirect('/school/subjects');
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/school/subjects');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid subject ID.');
            $this->redirect('/school/subjects');
        }
        $class = $this->subject->find($id);
        if (!$class) {
            $this->flash('error', 'Subject not found.');
            $this->redirect('/school/subjects');
        }

        if ($this->subject->delete($id)) {
            $this->flash('success', 'Subject deleted successful!');
        } else {
            $this->flash('error', 'Failed to delete subject.');
        }
        $this->redirect('/school/subjects');
    }
}
