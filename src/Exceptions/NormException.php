<?php

declare(strict_types=1);

namespace SamMcDonald\Norm\Exceptions;

use RuntimeException;
use Throwable;

class NormException extends RuntimeException
{
    public static function createConnectionException(string $message = "", int $code = 0, Throwable $previous = null): self
    {
        if ($message === "") {
            $message = "Failed to create nORM connection";
        }

        return new self($message, $code, $previous);
    }

    public static function createEntityOutOfBounds(string $message = "", int $code = 0, Throwable $previous = null): self
    {
        if ($message === "") {
            $message = "Entity not found : nORM Exception";
        }

        return new self($message, $code, $previous);
    }
}
