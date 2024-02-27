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
use Exception;

class TesoreriaAPI
{
    const URL_COMPROBANTE_PAGO = 'https://www.tesoreria.cl/portal/comprobantePago/goListaPagos.do';
    private $rut;
    private $dv;
    private $form;
    private $folio;

    function __construct(int $rut, string $dv, int $form, int $folio)
    {
        $this->rut = $rut;
        $this->dv = $dv;
        $this->form = $form;
        $this->folio = $folio;
    }

    /**
     * Verifica si el comprobante de pago de Tributos Aduaneros ha sido pagado
     *
     * @return bool
     */
    public function isTesoreriaPagada():bool
    {            
        $response = $this->getRawOutputResponse();
    
        return $this->isFolioPresentInResponse($response);
    }
    
    /**
     * Genera el comprobante de pago de Tributos Aduaneros en formato PDF
     *
     * @param string|null $file_name
     * @return string
     * @throws ComprobanteNotFoundException
     */
    public function generaComprobantePago(string $filename = null):string
    {
        if (empty(trim($filename))) $filename =  "CTES-" .  $this->folio . ".pdf";

        $content = $this->getRawComprobantePDF();
        
        file_put_contents($filename, $content);
    
        return $filename;
    }

    /**
     * Obtiene el comprobante de pago de Tributos Aduaneros en formato PDF
     * 
     * @throws ComprobanteNotFoundException
     * @return string
     */
    public function getRawComprobantePDF():string
    {
        $rawOutput = $this->getRawOutputResponse();

        if (!$this->isFolioPresentInResponse($rawOutput)) throw new ComprobanteNotFoundException();

        $output = (new DOM($rawOutput))->prepareDOM();

        $dompdf = new Dompdf();

        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true); // Habilita la carga de recursos remotos (CSS, imágenes, etc.)
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
     * @throws RequestErrorException
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

        if (curl_errno($ch)) throw new RequestErrorException(curl_error($ch));

        curl_close($ch);

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

    /**
     * Verifica si el folio del comprobante se encuentra en el response
     *
     * @param string $response
     * @return bool
     */
    private function isFolioPresentInResponse(string $response):bool
    {
        $existe_comprobante = [];
        
        preg_match('/(' . $this->folio . ')/', $response, $existe_comprobante);

        return !empty($existe_comprobante);
    }

}