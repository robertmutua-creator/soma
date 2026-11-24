<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Strands;
use App\Models\Subjects;

class StrandsController extends Controller
{
    private string $path = '/school/strands';
    private Strands $strands;
    private Subjects $subjects;
    public function __construct()
    {
        $this->auth('school');
        $this->strands = new Strands($this->schoolid());
        $this->subjects = new Subjects($this->schoolid());
    }

    public function index(): void
    {
        $strand = null;
        $hasSubjectFilter = isset($_GET['subject_id']) && trim((string) $_GET['subject_id']) !== '';
        $selectedSubject = $hasSubjectFilter ? trim((string) $_GET['subject_id']) : '';

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false && $id > 0) {
            $strand = $this->strands->find($id);
            if (!$strand) {
                $this->flash('error', 'Strand not found.');
                $this->redirect($this->path);
                return;
            }

            if (!$hasSubjectFilter && isset($strand['subject_id'])) {
                $selectedSubject = (string) $strand['subject_id'];
            }
        }

        if ($selectedSubject !== '') {
            $strands = $this->strands->all(['subject_id' => (int) $selectedSubject]);
        } else {
            $strands = $this->strands->all();
        }

        $this->view([
            'title'           => 'Manage Subject Strands',
            'content'         => 'schools/strands',
            'strand'          => $strand,
            'strands'         => $strands,
            'selectedSubject' => $selectedSubject,
            'token'           => $this->token(),
            'script'          => 'strands',
            'subjects'        => $this->subjects->all(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect($this->path);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);

        if ($name === '' || !$subject_id || $subject_id <= 0) {
            $this->flash('error', 'Subject and Strand Name is required.');
            $this->redirect($this->path);
        }

        $redirectUrl = $this->path . '?subject_id=' . $subject_id;

        $data = [
            'name' => $this->format($name, 'title'),
            'subject_id' => $subject_id,
        ];

        $existing = null;
        if ($id) {
            $existing = $this->strands->find($id);
            if (!$existing) {
                $this->flash('error', 'Strand not found.');
                $this->redirect($redirectUrl);
            }

            if ($this->strands->update($id, $data)) {
                $this->flash('success', 'Strand updated successfully.');
            } else {
                $this->flash('error', 'Failed to update strand.');
            }
            $this->redirect($redirectUrl);
        }

        if ($this->strands->create($data)) {
            $this->flash('success', 'Strand regitered successfully.');
        } else {
            $this->flash('error', 'Failed to register strand.');
        }
        $this->redirect($redirectUrl);
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->path);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid strand ID.');
            $this->redirect($this->path);
        }

        $strand = $this->strands->find($id);

        if (!$strand) {
            $this->flash('error', 'Strand not found');
            $this->redirect($this->path);
        }

        if ($this->strands->delete($id)) {
            $this->flash('success', 'Strand deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete strand');
        }
        $this->redirect($this->path);
    }
}
