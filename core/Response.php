<?php

declare(strict_types = 1);

namespace Core;

final class Response {
    public function status(int $code): self {
        http_response_code($code);
        return $this;
    }

    public function header(string $name, string $value): self {
        header("$name: $value");
        return $this;
    }

    public function redirect(string $to, int $code = 302): void {
        $this->status($code);
        header("Location: $to");
        exit;
    }

    public function text(string $content, int $code = 200): void {
        $this->status($code)->header("Content-Type", "text/plain; charset=utf-8");
        echo $content;
    }

    public function json (array $data, int $code = 200): void {
        $this->status($code)->header("Content-Type", "application/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function html(string $html, int $code = 200): void {
        $this->status($code)->header('Content-Type', 'text/html; charset=utf-8');
        echo $html;
    }

    public function view(string $viewFile, array $data = [], int $code = 200): void {
        $this->status($code)->header("Content-Type", "text/html; charset=utf-8");

        $fullPath = dirname(__DIR__) . "/resources/views/" . ltrim($viewFile, "/");
        if (!str_ends_with($fullPath, ".php")) {
            $fullPath .= ".php";
        }

        if (!file_exists($fullPath)) {
            $this->text("View not found: $viewFile", 500);
            return;
        }

        extract($data, EXTR_SKIP);
        require $fullPath;
    }

    public function successJson(string $message = 'Success', mixed $data = null, int $code = 200): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorJson(string $message = 'Error', mixed $errors = null, int $code = 400): void
    {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function back(int $code = 302): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer, $code);
    }
}