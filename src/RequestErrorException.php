<?php

namespace Jsanbae\TesoreriaAPI;

class RequestErrorException extends \Exception
{
    public function __construct($message = 'Error en la petición', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
