<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Allocations;
use App\Models\Classes;
use App\Models\Subjects;
use App\Models\User;
use App\Models\Years;

class AllocationsController extends Controller
{
    protected Allocations $allocationsModel;
    protected string $base = '/school/allocations';
    public function __construct()
    {
        $this->auth('school');
        $this->allocationsModel = new Allocations($this->schoolid());
    }

    public function index(): void
    {
        $years = (new Years($this->schoolid()))->all();
        $activeYear = array_filter($years, fn($y) => $y['status'] === 'active');
        $yearId = $_GET['year_id'] ?? (!empty($activeYear) ? reset($activeYear)['id'] : 0);

        $data = [
            'token' => $this->token(),
            'title' => 'Class Subject Teacher Allocation',
            'allocations' => $yearId ? $this->allocationsModel->allocations((int) $yearId) : [],
            'years' => $years,
            'currentYearId' => $yearId,
            'classes' => (new Classes($this->schoolid()))->all(),
            'subjects' => (new Subjects($this->schoolid()))->all(),
            'teachers' => (new User($this->schoolid()))->all(['role' => 'teacher']),
            'content' => 'schools/allocations',
        ];
        $this->view($data);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
            return;
        }

        $yearId = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT);
        $classId = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
        $subjectId = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
        $teacherId = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);
        $fromDate = $_POST['from_date'] ?? date('Y-m-d');

        if (!$yearId || !$classId || !$subjectId || !$teacherId || empty($fromDate)) {
            $this->flash('error', 'All fields are required.');
            $this->redirect($this->base);
            return;
        }

        $existing = $this->allocationsModel->findActive($yearId, $classId, $subjectId);

        if ($existing && $existing['teacher_id'] === $teacherId) {
            $this->flash('error', 'The teacher is already actively assigned to the same class and subject this year');
            $this->redirect($this->base . '?year_id=' . $yearId);
        }

        $saved = $this->allocationsModel->assign([
            'year_id' => $yearId,
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'from_date' => $fromDate
        ]);

        if ($saved) {
            $this->flash('success', 'Teacher assigned successfully.');
        } else {
            $this->flash('error', 'Failed to assign teacher.');
        }

        $this->redirect($this->base . '?year_id=' . $yearId);
    }

    public function delete(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token.');
            $this->redirect($this->base);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->flash('error', 'Invalid allocation ID.');
            $this->redirect($this->base);
            return;
        }

        $allocation = $this->allocationsModel->find($id);
        if (!$allocation) {
            $this->flash('error', 'Allocation not found.');
            $this->redirect($this->base);
            return;
        }

        if ($this->allocationsModel->delete($id)) {
            $this->flash('success', 'Allocation deleted successfully!');
        } else {
            $this->flash('error', 'Failed to delete allocation.');
        }

        $yearId = filter_input(INPUT_POST, 'year_id', FILTER_VALIDATE_INT) ?: ($allocation['year_id'] ?? null);
        $this->redirect($this->base . ($yearId ? '?year_id=' . $yearId : ''));
    }
}
