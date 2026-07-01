<?php
declare(strict_types=1);

namespace App\Controllers;

abstract class Controller
{
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function requestBody(): array
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false || trim($rawBody) === '') {
            return [];
        }

        $body = json_decode($rawBody, true);

        if (!is_array($body)) {
            $this->jsonResponse(['message' => 'JSONの形式が正しくありません'], 400);
            exit;
        }

        return $body;
    }

    protected function requireFields(array $body, array $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $body) || $body[$field] === '') {
                $this->jsonResponse(['message' => "{$field} は必須です"], 422);
                exit;
            }
        }
    }
}
