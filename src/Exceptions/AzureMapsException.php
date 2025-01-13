<?php

namespace Sacapsystems\LaravelAzureMaps\Exceptions;

use Exception;

class AzureMapsException extends Exception
{
    public function __construct(
        $message = 'An error occurred while fetching data from Azure Maps',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
