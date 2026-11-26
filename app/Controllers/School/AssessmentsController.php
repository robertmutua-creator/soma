<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Assessments;

class AssessmentsController extends Controller
{
    private string $path = '/school/assessments';
    private Assessments $assessments;

    public function __construct()
    {
        $this->auth('school');
        $this->assessments = new Assessments($this->schoolid());
    }

    public function index(): void
    {
        $assessment = null;

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false && $id > 0) {
            $assessment = $this->assessments->find($id);
            if (!$assessment) {
                $this->flash('error', 'Assessment not found.');
                $this->redirect($this->path);
                return;
            }
        }

        $this->view([
            'title'       => 'Manage Assessments',
            'content'     => 'schools/assessments',
            'assessment'  => $assessment,
            'assessments' => $this->assessments->all(),
            'token'       => $this->token(),
            // 'script'      => 'assessments',
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->path);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $type = trim((string) ($_POST['type'] ?? 'formative'));
        $startDate = trim((string) ($_POST['start_date'] ?? ''));
        $endDate = trim((string) ($_POST['end_date'] ?? ''));
        $status = trim((string) ($_POST['status'] ?? 'active'));

        $allowedTypes = ['formative', 'summative'];
        $allowedStatuses = ['active', 'completed'];

        if (!in_array($type, $allowedTypes, true) || !in_array($status, $allowedStatuses, true)) {
            $this->flash('error', 'Invalid assessment type or status.');
            $this->redirect($this->path);
            return;
        }

        if ($startDate === '' || $endDate === '') {
            $this->flash('error', 'Start date and end date are required.');
            $this->redirect($this->path);
            return;
        }

        if ($endDate < $startDate) {
            $this->flash('error', 'End date cannot be earlier than start date.');
            $this->redirect($this->path);
            return;
        }

        $data = [
            'type'       => $type,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => $status,
        ];

        if ($id) {
            $existing = $this->assessments->find($id);
            if (!$existing) {
                $this->flash('error', 'Assessment not found.');
                $this->redirect($this->path);
                return;
            }

            if ($this->assessments->update($id, $data)) {
                $this->flash('success', 'Assessment updated successfully.');
            } else {
                $this->flash('error', 'Failed to update assessment.');
            }
            $this->redirect($this->path);
            return;
        }

        if ($this->assessments->create($data)) {
            $this->flash('success', 'Assessment created successfully.');
        } else {
            $this->flash('error', 'Failed to create assessment.');
        }
        $this->redirect($this->path);
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->path);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $this->flash('error', 'Invalid assessment ID.');
            $this->redirect($this->path);
            return;
        }

        $assessment = $this->assessments->find($id);
        if (!$assessment) {
            $this->flash('error', 'Assessment not found.');
            $this->redirect($this->path);
            return;
        }

        if ($this->assessments->delete($id)) {
            $this->flash('success', 'Assessment deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete assessment.');
        }
        $this->redirect($this->path);
    }
}
