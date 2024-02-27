<?php
namespace Jsanbae\TesoreriaAPI;
/**
 * 
 * Utilidad para automatizar: 
 * 
 * - Comprobación de Pago en linea de Tributos Aduaneros
 * - Generación de PDF del comprobante de pago de Tributos Aduaneros
 * 
 * Autor: Javier Sánchez 
 * GitHub: @jsanbae
 * 
 */

use Jsanbae\TesoreriaAPI\DOM;

use Dompdf\Dompdf;

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

    /**
     * Verifica si el comprobante de pago de Tributos Aduaneros ha sido pagado
     *
     * @return bool
     */
    public function isTesoreriaPagada():bool
    {
        $existe_comprobante = [];
            
        $response = $this->getRawOutputResponse();
    
        preg_match('/(' . $this->folio . ')/', $response, $existe_comprobante);
    
        return (count($existe_comprobante)) ? true : false;
    }
    
    /**
     * Genera el comprobante de pago de Tributos Aduaneros en formato PDF
     *
     * @param string|null $file_name
     * @return string
     */
    public function generaComprobantePago(string $file_name = null):string
    {
        if (!$file_name) $file_name =  "CTES-" .  $this->folio . ".pdf";

        $contenido = $this->getRawComprobantePDF();
        
        file_put_contents($file_name, $contenido);
    
        return $file_name;
    }

    /**
     * Obtiene el comprobante de pago de Tributos Aduaneros en formato PDF
     *
     * @return string
     */
    public function getRawComprobantePDF():string
    {
        $rawOutput = $this->getRawOutputResponse();
        $output = (new DOM($rawOutput))->prepareDOM();

        $dompdf = new Dompdf();

        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true); // Habilita la carga de recursos remotos (CSS, imágenes, etc.
        $dompdf->setOptions($options);

        $dompdf->loadHtml($output);
        $dompdf->render();
        $rawPDF = $dompdf->output();

        return $rawPDF;
    }

    /**
     * Obtiene la respuesta del servidor al solicitar 
     * el comprobante de pago de Tributos Aduaneros
     *
     * @return string
     */
    public function getRawOutputResponse():string 
    {
        $ch = curl_init( self::URL_COMPROBANTE_PAGO );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->getRequestParams());
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec( $ch );

        return $response;
    }

    /**
     * Prepara el string de parámetros para la solicitud
     *
     * @return string
     */
    private function getRequestParams():string
    {
        return "rut=" . $this->rut . "&dv=" . $this->dv . "&formulario=" . $this->form . "&folio=" . $this->folio;
    }

}