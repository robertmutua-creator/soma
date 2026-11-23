<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Core\Model;
use App\Models\Classes;

class ClassesController extends Controller
{
    private Classes $class;
    public function __construct()
    {
        $this->auth('school');
        $this->class = new Classes($this->schoolid());
    }

    // Display classes and load class for editing
    public function index(): void
    {
        $class = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $class = $this->class->find($id);

            if (!$class) {
                $this->flash('error', 'Class not found.');
                $this->redirect('/school/classes');
            }
        }
        $classes = $this->class->all();
        $this->view([
            'title' => 'Manage Classes',
            'content' => 'schools/classes',
            'class' => $class,
            'classes' => $classes,
            'token' => $this->token()
        ]);
    }

    // Save/Update Classes
    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/school/classes');
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $level = trim($_POST['level'] ?? '');

        if ($code === '' || $name === '' || $level === '') {
            $this->flash('error', 'All fields are required.');
            $this->redirect('/school/classes');
        }

        $data = [
            'code' => $this->format($code, 'upper'),
            'name' => $this->format($name, 'title'),
            'level' => $level,
        ];

        $existing = null;
        if ($id) {
            $existing = $this->class->find($id);
            if (!$existing) {
                $this->flash('error', 'Class not found.');
                $this->redirect('/school/classes');
            }

            if ($this->class->update($id, $data)) {
                $this->flash('success', 'Class updated successfully.');
            } else {
                $this->flash('error', 'Failed to update class.');
            }
            $this->redirect('/school/classes');
        }
        if ($this->class->create($data)) {
            $this->flash('success', 'Class registered successfully.');
        } else {
            $this->flash('error', 'Failed to register class.');
        }
        $this->redirect('/school/classes');
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect('/school/classes');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid class ID.');
            $this->redirect('/school/classes');
        }
        $class = $this->class->find($id);
        if (!$class) {
            $this->flash('error', 'Class not found.');
            $this->redirect('/school/classes');
        }

        if ($this->class->delete($id)) {
            $this->flash('success', 'Class deleted successful!');
        } else {
            $this->flash('error', 'Failed to delete class.');
        }
        $this->redirect('/school/classes');
    }
}
