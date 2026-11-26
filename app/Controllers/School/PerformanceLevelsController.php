<?php

namespace App\Controllers\School;

use App\Core\Controller;
use App\Models\PerformanceLevels;

class PerformanceLevelsController extends Controller
{
    private string $path = '/school/performance-levels';
    private PerformanceLevels $levels;

    public function __construct()
    {
        $this->auth('school');
        $this->levels = new PerformanceLevels($this->schoolid());
    }

    public function index(): void
    {
        $level = null;

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false && $id > 0) {
            $level = $this->levels->find($id);
            if (!$level) {
                $this->flash('error', 'Performance level not found.');
                $this->redirect($this->path);
                return;
            }
        }

        $this->view([
            'title'  => 'Manage Performance Levels',
            'content' => 'schools/performance_levels',
            'level'  => $level,
            'levels' => $this->levels->all(),
            'token'  => $this->token(),
            'script' => 'performance_levels',
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
        $code = strtoupper(trim((string) ($_POST['code'] ?? '')));
        $label = trim((string) ($_POST['label'] ?? ''));
        $minMark = filter_var($_POST['min_mark'] ?? '', FILTER_VALIDATE_FLOAT);
        $maxMark = filter_var($_POST['max_mark'] ?? '', FILTER_VALIDATE_FLOAT);

        if ($code === '' || $label === '' || $minMark === false || $maxMark === false) {
            $this->flash('error', 'Code, label, minimum mark, and maximum mark are required.');
            $this->redirect($this->path);
            return;
        }

        if ($minMark < 0 || $maxMark < 0 || $maxMark < $minMark) {
            $this->flash('error', 'Enter a valid mark range where max mark is at least min mark.');
            $this->redirect($this->path);
            return;
        }

        $data = [
            'code'     => $code,
            'label'    => $this->format($label, 'title'),
            'min_mark' => $minMark,
            'max_mark' => $maxMark,
        ];

        try {
            if ($id) {
                $existing = $this->levels->find($id);
                if (!$existing) {
                    $this->flash('error', 'Performance level not found.');
                    $this->redirect($this->path);
                    return;
                }

                if ($this->levels->update($id, $data)) {
                    $this->flash('success', 'Performance level updated successfully.');
                } else {
                    $this->flash('error', 'Failed to update performance level.');
                }
                $this->redirect($this->path);
                return;
            }

            if ($this->levels->create($data)) {
                $this->flash('success', 'Performance level created successfully.');
            } else {
                $this->flash('error', 'Failed to create performance level.');
            }
            $this->redirect($this->path);
        } catch (\PDOException $e) {
            error_log($e->getMessage());

            $mysqlCode = $e->errorInfo[1] ?? null;
            if ($e->getCode() === '22003' || $mysqlCode === 1264) {
                $this->flash('error', 'Mark value exceeds the maximum allowed column size.');
            } elseif ($e->getCode() === '23000' || $mysqlCode === 1062) {
                $this->flash('error', 'A performance level with that code already exists.');
            } else {
                $this->flash('error', 'A database error occurred while saving the performance level.');
            }

            $this->redirect($this->path);
        }
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
            $this->flash('error', 'Invalid performance level ID.');
            $this->redirect($this->path);
            return;
        }

        $level = $this->levels->find($id);
        if (!$level) {
            $this->flash('error', 'Performance level not found.');
            $this->redirect($this->path);
            return;
        }

        if ($this->levels->delete($id)) {
            $this->flash('success', 'Performance level deleted successfully.');
        } else {
            $this->flash('error', 'Failed to delete performance level.');
        }
        $this->redirect($this->path);
    }
}
