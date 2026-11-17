<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Years;

class YearsController extends Controller
{
    private Years $year;
    private string $base;
    public function __construct()
    {
        $this->auth('school');
        $this->year = new Years($this->schoolid());
        $this->base = '/school/years';
    }

    // Display years and load year for editing
    public function index(): void
    {
        $year = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $year = $this->year->find($id);

            if (!$year) {
                $this->flash('error', 'Year not found.');
                $this->redirect($this->base);
            }
        }
        $years = $this->year->all();
        $this->view([
            'title' => 'Manage years',
            'content' => 'schools/years',
            'year' => $year,
            'years' => $years,
            'token' => $this->token()
        ]);
    }

    // Save/Update years
    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect($this->base);
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $year = trim($_POST['year'] ?? '');
        $status = trim($_POST['status'] ?? 'active');

        if ($year === '') {
            $this->flash('error', 'Academic Year is  required.');
            $this->redirect($this->base);
        }
        if (!in_array($status, ['active', 'closed', 'upcoming'], true)) {
            $status = 'active';
        }

        $data = [
            'year' => $year,
            'status' => $this->format($status, 'lower'),
        ];


        $existing = null;
        if ($id) {
            $existing = $this->year->find($id);
            if (!$existing) {
                $this->flash('error', 'Academic Year not found.');
                $this->redirect($this->base);
            }

            if ($this->year->update($id, $data)) {
                $this->flash('success', 'Year updated successfully.');
            } else {
                $this->flash('error', 'Failed to update year.');
            }
            $this->redirect($this->base);
        }
        if ($this->year->create($data)) {
            $this->flash('success', 'Academic Year registered successfully.');
        } else {
            $this->flash('error', 'Failed to register Academic Year.');
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
            $this->flash('error', 'Invalid academic year ID.');
            $this->redirect($this->base);
        }
        $year = $this->year->find($id);
        if (!$year) {
            $this->flash('error', 'Academic Year not found.');
            $this->redirect($this->base);
        }

        if ($this->year->delete($id)) {
            $this->flash('success', 'Academic Year deleted successful!');
        } else {
            $this->flash('error', 'Failed to delete Academic Year.');
        }
        $this->redirect($this->base);
    }
}
