<?php

use App\Models\Loan;
use App\Models\Student;

test('学生を登録して取得できる', function () {
    $student = $this->createStudent([
        'name' => '山田 太郎',
        'grade' => 2,
        'student_id' => 123456,
        'email' => 'yamada@example.test',
    ]);

    expect($student)->toMatchArray([
        'name' => '山田 太郎',
        'grade' => 2,
        'student_id' => 123456,
        'email' => 'yamada@example.test',
    ])
        ->and(Student::find($student['id']))->toEqual($student)
        ->and(Student::all())->toHaveCount(1);
});

test('存在しない学生は取得できない', function () {
    expect(Student::find(99999))->toBeNull();
});

test('学生の貸出中の本の冊数を取得できる', function () {
    $student = $this->createStudent();
    $firstBook = $this->createBook();
    $secondBook = $this->createBook();

    Loan::borrow($student['id'], $firstBook['id']);
    Loan::borrow($student['id'], $secondBook['id']);

    expect(Student::borrowedBookCount($student['id']))->toBe(2);
});

test('同じ学籍番号の学生は登録できない', function () {
    $this->createStudent([
        'student_id' => 123456,
        'email' => 'first@example.test',
    ]);

    expect(fn () => $this->createStudent([
        'student_id' => 123456,
        'email' => 'second@example.test',
    ]))->toThrow(PDOException::class);
});

test('同じメールアドレスの学生は登録できない', function () {
    $this->createStudent([
        'student_id' => 123456,
        'email' => 'duplicate@example.test',
    ]);

    expect(fn () => $this->createStudent([
        'student_id' => 234567,
        'email' => 'duplicate@example.test',
    ]))->toThrow(PDOException::class);
});
