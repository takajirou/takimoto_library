<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Loan;

class LoanController extends Controller
{
    public function index(array $params = []): void
    {
        $this->jsonResponse([
            'data' => Loan::all(),
        ]);
    }

    public function overdue(array $params = []): void
    {
        $this->jsonResponse([
            'data' => Loan::overdue(),
        ]);
    }

    public function store(array $params = []): void
    {
        $body = $this->requestBody();
        $this->requireFields($body, ['student_id', 'book_id']);

        $result = Loan::borrow((int) $body['student_id'], (int) $body['book_id']);

        if (!$result['success']) {
            $this->jsonResponse([
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ], $result['status']);
            return;
        }

        $this->jsonResponse([
            'message' => '本を貸し出しました',
            'data' => $result['loan'],
        ], 201);
    }

    public function returnBook(array $params): void
    {
        $result = Loan::returnBook((int) $params['id']);

        if (!$result['success']) {
            $this->jsonResponse([
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
            ], $result['status']);
            return;
        }

        $this->jsonResponse([
            'message' => '本を返却しました',
            'data' => $result['loan'],
        ]);
    }
}
