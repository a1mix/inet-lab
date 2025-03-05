<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ValidationException extends RegisterUserException
{
    public function __construct(string $message, int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $code);
    }
}