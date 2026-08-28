<?php

namespace App\Services\AI;

use Exception;

class ModelUnavailableException extends Exception
{
    public function __construct(string $message, public readonly string $model)
    {
        parent::__construct($message);
    }
}
