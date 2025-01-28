<?php

namespace Sacapsystems\LaravelGoogleMaps\Exceptions;

use Exception;

class GoogleMapsException extends Exception
{
    public function __construct(
        $message = 'An error occurred while fetching data from Google Maps',
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
