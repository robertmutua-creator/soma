<?php

namespace App\Core;

use App\Models\Classes;
use App\Models\School;
use App\Models\Subjects;
use App\Models\Terms;
use App\Models\User;
use App\Models\Years;

class Controller
{
    // Render a view through the main layout.
    protected function view(array $data = []): void
    {
        if ($this->isLoggedIn()) {
            $data['links'] = $this->getLinks();
            $data['user'] = $_SESSION['user'];
            $data['token'] = $this->token();
        }
        extract($data, EXTR_SKIP);

        require dirname(__DIR__) . '/Views/layout.php';
    }

    // Store a flash message in the session.
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    // Check whether a user is logged in.
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    // Check whether the logged-in user has a specific role.
    protected function checkRole(string $role): bool
    {
        return $this->isLoggedIn()
            && ($_SESSION['user']['role'] ?? null) === $role;
    }

    // Upload a file and return its relative path.
    protected function uploadFile(array $file, string $category): string|false
    {
        $allowed = ['schools', 'teachers', 'students'];

        if (!in_array($category, $allowed, true)) {
            return false;
        }

        if (
            !isset($file['tmp_name'], $file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            return false;
        }

        $directory = dirname(__DIR__, 2) . "/public/uploads/{$category}";

        if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . ($extension ? ".{$extension}" : '');

        if (!move_uploaded_file($file['tmp_name'], "{$directory}/{$filename}")) {
            return false;
        }

        return "{$category}/{$filename}";
    }

    // Delete an uploaded file from the server.
    protected function deleteFile(string $file): bool
    {
        $base = realpath(dirname(__DIR__, 2) . '/public/uploads');

        if ($base === false) {
            return false;
        }

        $path = realpath($base . '/' . $file);

        if (
            $path === false ||
            !str_starts_with($path, $base . DIRECTORY_SEPARATOR) ||
            !is_file($path)
        ) {
            return false;
        }

        return unlink($path);
    }

    // Read and decode incoming JSON requests.
    protected function jrequest(): array
    {
        if (!str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            http_response_code(415);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            http_response_code(400);
            exit;
        }

        return $data;
    }

    // Send a JSON response.
    protected function jresponse(array $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        exit;
    }
    // Redirect to another URL.
    public function redirect(string $url): never
    {
        header("Location:{$url}");
        exit;
    }
    // Require authentication and the specified role.
    protected function auth(string $role): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        if (!$this->checkRole($role)) {
            $this->redirect('/dashboard');
        }
    }

    protected function schoolid(): ?int
    {
        return isset($_SESSION['user']['school_id'])
            ? (int) $_SESSION['user']['school_id']
            : null;
    }
    public function dashboard(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        $role = $_SESSION['user']['role'] ?? 'unknown';

        switch ($role) {
            case 'admin':
                $data = [
                    'title' => 'Admin Dashboard',
                    'totalUsers' => (new User())->count(),
                    'totalSchools' => (new School())->count(),
                    'content' => 'admin/index',
                ];
                $this->view($data);
                break;
            case 'school':
                $currentTerm = (new Terms($this->schoolid()))->current();
                $currentPeriod = $currentTerm
                    ? $currentTerm['name'] . '/' . $currentTerm['year_name']
                    : 'Not Set';
                $data = [
                    'title' => 'School Admin Dashboard',
                    'totalStudents' => new User($this->schoolid())->count(['role' => 'student']),
                    'totalTeachers' => new User($this->schoolid())->count(['role' => 'teacher']),
                    'totalStreams' => new Classes($this->schoolid())->count(),
                    'totalSubjects' => new Subjects($this->schoolid())->count(),
                    'currentPeriod' => $currentPeriod,
                    'content' => 'schools/index',
                ];
                $this->view($data);
                break;

            default:
                http_response_code(403);
                echo "Access Denied: Your user role is not recognized or authorized.";
                break;
        }
    }

    protected function getLinks(): array
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        $role = $_SESSION['user']['role'];

        $links = [
            ['link' => '/dashboard', 'label' => 'Dashboard']
        ];

        switch ($role) {
            case 'admin':
                $links[] = ['link' => '/admin/schools', 'label' => 'Manage Schools'];
                $links[] = ['link' => '/admin/admins', 'label' => 'Manage School Admins'];
                break;

            case 'school':
                // Academic calendar & structure
                $links[] = ['link' => '/school/years', 'label' => 'Manage Academic Years'];
                $links[] = ['link' => '/school/terms', 'label' => 'Manage Terms'];
                $links[] = ['link' => '/school/classes', 'label' => 'Manage Streams'];

                // Curriculum hierarchy
                $links[] = ['link' => '/school/subjects', 'label' => 'Manage Subjects'];
                $links[] = ['link' => '/school/strands', 'label' => 'Subject Strands'];
                $links[] = ['link' => '/school/substrands', 'label' => 'Substrands'];

                // Staff & subject assignments
                $links[] = ['link' => '/school/teachers', 'label' => 'Manage Teachers'];
                $links[] = ['link' => '/school/allocations', 'label' => 'Teacher Subjects Allocations'];

                // Assessments
                $links[] = ['link' => '/school/assessments', 'label' => 'Manage Assessments'];

                // Student records & movement
                $links[] = ['link' => '/school/students', 'label' => 'Manage Students'];
                $links[] = ['link' => '/school/enrollments', 'label' => 'Students Enrollments'];
                $links[] = ['link' => '/school/promotions', 'label' => 'Students Promotions'];
                $links[] = ['link' => '/school/demotions', 'label' => 'Demote Students'];
                break;
        }

        $links[] = ['link' => '/logout', 'label' => 'Logout'];

        return $links;
    }

    // Generate a CSRF token.
    protected function token(): string
    {
        if (!isset($_SESSION['_token']) || !is_string($_SESSION['_token']) || $_SESSION['_token'] === '') {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_token'];
    }

    // Validate a CSRF token.
    protected function validateToken(string $token): bool
    {
        return isset($_SESSION['_token'])
            && is_string($_SESSION['_token'])
            && hash_equals($_SESSION['_token'], $token);
    }

    // Format input
    protected function format(string $text, string $case): string
    {
        return match (strtolower(trim($case))) {
            'upper', 'uppercase' => mb_strtoupper($text, 'UTF-8'),
            'lower', 'lowercase' => mb_strtolower($text, 'UTF-8'),
            'title', 'titlecase' => mb_convert_case($text, MB_CASE_TITLE, 'UTF-8'),
            default => $text,
        };
    }
}
