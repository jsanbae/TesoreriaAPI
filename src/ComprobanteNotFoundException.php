<?php

namespace Jsanbae\TesoreriaAPI;

use Exception;

class ComprobanteNotFoundException extends Exception
{
    public function __construct($message = 'No se encontró el comprobante de pago', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
