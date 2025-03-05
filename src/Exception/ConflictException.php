<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ConflictException extends RegisterUserException
{
    public function __construct(string $message, int $code = Response::HTTP_CONFLICT)
    {
        parent::__construct($message, $code);
    }
}
