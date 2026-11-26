<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\Strands;
use App\Models\Subjects;
use App\Models\Substrands;

class SubstrandsController extends Controller
{
    private string $path = '/school/substrands';
    private Substrands $substrands;
    private Strands $strands;
    private Subjects $subjects;

    public function __construct()
    {
        $this->auth('school');
        $this->substrands = new Substrands($this->schoolid());
        $this->strands = new Strands($this->schoolid());
        $this->subjects = new Subjects($this->schoolid());
    }

    public function index(): void
    {
        $substrand = null;
        $strands = [];
        $substrands = [];

        $hasSubject = isset($_GET['subject_id']) && trim((string) $_GET['subject_id']) !== '';
        $selectedSubject = $hasSubject ? trim((string) $_GET['subject_id']) : '';

        $hasStrand = isset($_GET['strand_id']) && trim((string) $_GET['strand_id']) !== '';
        $selectedStrand = $hasStrand ? trim((string) $_GET['strand_id']) : '';

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false && $id > 0) {
            $substrand = $this->substrands->find($id);
            if (!$substrand) {
                $this->flash('error', 'Substrand not found.');
                $this->redirect($this->path);
                return;
            }

            if (!$hasStrand && isset($substrand['strand_id'])) {
                $selectedStrand = (string) $substrand['strand_id'];
            }

            if (!$hasSubject && $selectedStrand !== '') {
                $parent = $this->strands->find((int) $selectedStrand);
                if ($parent && isset($parent['subject_id'])) {
                    $selectedSubject = (string) $parent['subject_id'];
                }
            }
        }

        if ($selectedSubject !== '') {
            $strands = $this->strands->all(['subject_id' => (int) $selectedSubject]);
        }

        if ($selectedStrand !== '') {
            $substrands = $this->substrands->all(['strand_id' => (int) $selectedStrand]);
        }

        $this->view([
            'title'           => 'Manage Subject Substrands',
            'content'         => 'schools/substrands',
            'substrand'       => $substrand,
            'substrands'      => $substrands,
            'strands'         => $strands,
            'selectedSubject' => $selectedSubject,
            'selectedStrand'  => $selectedStrand,
            'token'           => $this->token(),
            'script'          => 'substrands',
            'subjects'        => $this->subjects->all(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateToken($_POST['_token'] ?? '')) {
            $this->flash('error', 'Invalid security token');
            $this->redirect($this->path);
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $strand_id = filter_input(INPUT_POST, 'strand_id', FILTER_VALIDATE_INT);
        $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);

        if ($name === '' || !$strand_id || $strand_id <= 0) {
            $this->flash('error', 'Strand and Substrand Name is required.');
            $this->redirect($this->path);
            return;
        }

        $redirectUrl = $this->path . '?subject_id=' . ($subject_id ?: '') . '&strand_id=' . $strand_id;

        $data = [
            'name'      => $this->format($name, 'title'),
            'strand_id' => $strand_id,
        ];

        if ($id) {
            $existing = $this->substrands->find($id);
            if (!$existing) {
                $this->flash('error', 'Substrand not found.');
                $this->redirect($redirectUrl);
                return;
            }

            if ($this->substrands->update($id, $data)) {
                $this->flash('success', 'Substrand updated successfully.');
            } else {
                $this->flash('error', 'Failed to update substrand.');
            }
            $this->redirect($redirectUrl);
            return;
        }

        if ($this->substrands->create($data)) {
            $this->flash('success', 'Substrand registered successfully.');
        } else {
            $this->flash('error', 'Failed to register substrand.');
        }
        $this->redirect($redirectUrl);
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
            $this->flash('error', 'Invalid substrand ID.');
            $this->redirect($this->path);
            return;
        }

        $substrand = $this->substrands->find($id);

        if (!$substrand) {
            $this->flash('error', 'Substrand not found');
            $this->redirect($this->path);
            return;
        }

        if ($this->substrands->delete($id)) {
            $this->flash('success', 'Substrand deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete substrand');
        }
        $this->redirect($this->path);
    }
}
