<?php

use App\Models\Book;

test('本を登録すると利用可能な状態で取得できる', function () {
    $book = $this->createBook([
        'title' => 'リーダブルコード',
        'author' => 'Dustin Boswell',
        'isbn' => '978-4873115658',
    ]);

    expect($book)->toMatchArray([
        'title' => 'リーダブルコード',
        'author' => 'Dustin Boswell',
        'isbn' => '978-4873115658',
        'status' => 'available',
    ])
        ->and(Book::find($book['id']))->toEqual($book);
});

test('本を状態とキーワードで絞り込める', function () {
    $availableBook = $this->createBook([
        'title' => 'PHP入門',
        'author' => '田中 太郎',
    ]);
    $this->createBook([
        'title' => 'JavaScript入門',
        'status' => 'borrowed',
    ]);
    $this->createBook([
        'title' => 'データベース設計',
        'author' => '佐藤 花子',
    ]);

    $availableBooks = Book::all(['status' => 'available']);
    $keywordBooks = Book::all(['keyword' => 'PHP']);

    expect($availableBooks)->toHaveCount(2)
        ->and(array_column($availableBooks, 'status'))->each->toBe('available')
        ->and($keywordBooks)->toHaveCount(1)
        ->and($keywordBooks[0]['id'])->toBe($availableBook['id']);
});

test('本を更新できる', function () {
    $book = $this->createBook();

    $updated = Book::update($book['id'], [
        'title' => '更新後の本',
        'author' => '更新 著者',
        'isbn' => '9780000000000',
        'barcode' => 'UPDATED-001',
        'location' => 'B-2',
        'status' => 'borrowed',
    ]);

    expect($updated)->toMatchArray([
        'id' => $book['id'],
        'title' => '更新後の本',
        'author' => '更新 著者',
        'barcode' => 'UPDATED-001',
        'location' => 'B-2',
        'status' => 'borrowed',
    ]);
});

test('存在しない本は更新できない', function () {
    $updated = Book::update(99999, [
        'title' => '存在しない本',
        'barcode' => 'NOT-FOUND',
        'location' => 'A-1',
        'status' => 'available',
    ]);

    expect($updated)->toBeNull();
});

test('本を削除でき、存在しない本は削除できない', function () {
    $book = $this->createBook();

    expect(Book::delete($book['id']))->toBeTrue()
        ->and(Book::find($book['id']))->toBeNull()
        ->and(Book::delete($book['id']))->toBeFalse();
});

test('同じバーコードの本は登録できない', function () {
    $this->createBook(['barcode' => 'DUPLICATE-001']);

    expect(fn () => $this->createBook(['barcode' => 'DUPLICATE-001']))
        ->toThrow(PDOException::class);
});
