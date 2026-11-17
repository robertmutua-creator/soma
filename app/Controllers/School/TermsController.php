<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Terms;
use App\Models\Years;

class TermsController extends Controller
{
    private Terms $term;
    private Years $year;
    private string $base;

    public function __construct()
    {
        $this->auth('school');
        $this->term = new Terms($this->schoolid());
        $this->year = new Years($this->schoolid());
        $this->base = '/school/terms';
    }

    // Display terms and load term for editing
    public function index(): void
    {
        $term = null;
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $term = $this->term->find($id);

            if (!$term) {
                $this->flash('error', 'Term not found.');
                $this->redirect($this->base);
            }
        }
        $terms = $this->term->all();
        $years = $this->year->all();
        $this->view([
            'title' => 'Manage terms',
            'content' => 'schools/terms',
            'term' => $term,
            'terms' => $terms,
            'years' => $years,
            'token' => $this->token()
        ]);
    }

    // Save/Update terms
    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect($this->base);
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $yearId = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $status = trim($_POST['status'] ?? 'upcoming');
        $startDate = trim($_POST['start_date'] ?? '') ?: null;
        $endDate = trim($_POST['end_date'] ?? '') ?: null;

        if (!$yearId || $name === '') {
            $this->flash('error', 'Academic Year and Term Name are required.');
            $this->redirect($this->base);
        }
        if (!in_array($status, ['active', 'closed', 'upcoming'], true)) {
            $status = 'upcoming';
        }

        $data = [
            'year_id' => $yearId,
            'name' => $this->format($name, 'title'),
            'status' => $this->format($status, 'lower'),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $existing = null;
        if ($id) {
            $existing = $this->term->find($id);
            if (!$existing) {
                $this->flash('error', 'Term not found.');
                $this->redirect($this->base);
            }

            if ($this->term->update($id, $data)) {
                $this->flash('success', 'Term updated successfully.');
            } else {
                $this->flash('error', 'Failed to update term.');
            }
            $this->redirect($this->base);
        }
        if ($this->term->create($data)) {
            $this->flash('success', 'Term registered successfully.');
        } else {
            $this->flash('error', 'Failed to register Term.');
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
            $this->flash('error', 'Invalid term ID.');
            $this->redirect($this->base);
        }
        $term = $this->term->find($id);
        if (!$term) {
            $this->flash('error', 'Term not found.');
            $this->redirect($this->base);
        }

        if ($this->term->delete($id)) {
            $this->flash('success', 'Term deleted successful!');
        } else {
            $this->flash('error', 'Failed to delete Term.');
        }
        $this->redirect($this->base);
    }
}
