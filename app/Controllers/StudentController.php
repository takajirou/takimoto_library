<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Student;

class StudentController extends Controller
{
    public function index(array $params = []): void
    {
        $this->jsonResponse([
            'data' => Student::all(),
        ]);
    }

    public function show(array $params): void
    {
        $student = Student::find((int) $params['id']);

        if ($student === null) {
            $this->jsonResponse(['message' => '学生が見つかりません'], 404);
            return;
        }

        $this->jsonResponse(['data' => $student]);
    }

    public function store(array $params = []): void
    {
        $body = $this->requestBody();
        $this->requireFields($body, ['name', 'grade', 'student_id', 'email']);

        $this->jsonResponse([
            'message' => '学生を登録しました',
            'data' => Student::create($body),
        ], 201);
    }
}
