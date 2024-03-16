<?php

namespace App\exception;

use Exception;
use Throwable;

class MissingCsvException extends Exception
{
    public function __construct($message = "Please provide the required CSV files.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function errorMessage(): string
    {
        return '<strong>Erro: </strong> ' . $this->getMessage();
    }
}
