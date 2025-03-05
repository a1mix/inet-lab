<?php

namespace App\DTO;

class RegisterUserDTO
{
    public function __construct(
        public ?string $email,
        public ?string $password,
        public ?string $username,
    ) {
    }

    public static function fromRequestData($data): self
    {
        return new self($data['email'] ?? null, $data['password'] ?? null, $data['username'] ?? null);
    }
}
