<?php

/**
 * 
 * Utilidad para automatizar: 
 * 
 * - Comprobación de Pago en linea de Tributos Aduaneros
 * - Generación de PDF del comprobante de pago de Tributos Aduaneros
 * 
 * Autor: Javier Sánchez 
 * GitHub: @jsan5709
 * 
 */

class TesoreriaAPI
{
    const URL_COMPROBANTE_PAGO = 'https://www.tesoreria.cl/portal/comprobantePago/goListaPagos.do';
    private $rut;
    private $dv;
    private $form;
    private $folio;

    function __construct(int $_rut, string $_dv, int $_form, int $_folio)
    {
        $this->rut = $_rut;
        $this->dv = $_dv;
        $this->form = $_form;
        $this->folio = $_folio;
    }

    public function isTesoreriaPagada() 
    {
        $existe_comprobante = [];

        $params = "rut=" . $this->rut . "&dv=" . $this->dv . "&formulario=" . $this->form . "&folio=" . $this->folio;
        
        $ch = curl_init( self::URL_COMPROBANTE_PAGO );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    
        $response = curl_exec( $ch );
        //var_dump($response);
        preg_match('/(' . $this->folio . ')/', $response, $existe_comprobante);
    //	var_dump($existe_comprobante);
    
        return (count($existe_comprobante)) ? true : false;
    }
    
    public function generaComprobantePago(string $output = NULL) 
    {
        if (!$output) {
            $output =  "CTES-" .  $this->folio . ".pdf";
        }
        
        $command = "xvfb-run wkhtmltopdf --post folio ". $this->folio ." --post rut " . $this->rut . " --post dv '" . $this->dv . "' --post formulario " . $this->form ." ". self::URL_COMPROBANTE_PAGO ." ". $output;
        echo $command;
        exec($command);
    
        return $output;
    }
}