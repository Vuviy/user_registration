<?php

namespace App;

final class Response
{
    public function __construct(
        private string $content,
        private int $status = 200,
        private array $headers = []
    ) {}

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->content;
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self(
            content: '',
            status: $status,
            headers: ['Location' => $location]
        );
    }
}