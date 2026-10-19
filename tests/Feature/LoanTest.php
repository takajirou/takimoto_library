<?php

use App\Models\Book;
use App\Models\Loan;

test('貸出すると貸出記録が作成され、本が貸出中になる', function () {
    $student = $this->createStudent();
    $book = $this->createBook();

    $result = Loan::borrow($student['id'], $book['id']);

    expect($result['success'])->toBeTrue()
        ->and($result['loan'])->toMatchArray([
            'student_id' => $student['id'],
            'book_id' => $book['id'],
            'status' => 'borrowed',
        ])
        ->and(Book::find($book['id'])['status'])->toBe('borrowed');
});

test('返却すると貸出記録が返却済みになり、本が利用可能に戻る', function () {
    $student = $this->createStudent();
    $book = $this->createBook();
    $loan = Loan::borrow($student['id'], $book['id'])['loan'];

    $result = Loan::returnBook($loan['id']);

    expect($result['success'])->toBeTrue()
        ->and($result['loan']['status'])->toBe('returned')
        ->and($result['loan']['returned_at'])->not->toBeNull()
        ->and(Book::find($book['id'])['status'])->toBe('available');
});

test('存在しない学生または本は貸出できない', function (int $studentId, int $bookId) {
    $result = Loan::borrow($studentId, $bookId);

    expect($result)->toMatchArray([
        'success' => false,
        'status' => 404,
        'message' => '学生または本が見つかりません',
    ]);
})->with([
    '学生が存在しない' => [99999, 99999],
    '本が存在しない' => fn () => [$this->createStudent()['id'], 99999],
]);

test('貸出中の本は貸出できない', function () {
    $firstStudent = $this->createStudent();
    $secondStudent = $this->createStudent();
    $book = $this->createBook();

    Loan::borrow($firstStudent['id'], $book['id']);
    $result = Loan::borrow($secondStudent['id'], $book['id']);

    expect($result)->toMatchArray([
        'success' => false,
        'status' => 409,
        'message' => 'この本は現在貸し出しできません',
    ]);
});

test('学生は同時に4冊以上借りられない', function () {
    $student = $this->createStudent();
    $books = [
        $this->createBook(),
        $this->createBook(),
        $this->createBook(),
        $this->createBook(),
    ];

    foreach (array_slice($books, 0, 3) as $book) {
        expect(Loan::borrow($student['id'], $book['id'])['success'])->toBeTrue();
    }

    $result = Loan::borrow($student['id'], $books[3]['id']);

    expect($result)->toMatchArray([
        'success' => false,
        'status' => 409,
        'message' => '同時に借りられる本は3冊までです',
    ]);
});

test('返却済みの貸出をもう一度返却できない', function () {
    $student = $this->createStudent();
    $book = $this->createBook();
    $loan = Loan::borrow($student['id'], $book['id'])['loan'];

    Loan::returnBook($loan['id']);
    $result = Loan::returnBook($loan['id']);

    expect($result)->toMatchArray([
        'success' => false,
        'status' => 409,
        'message' => 'この本はすでに返却済みです',
    ]);
});
