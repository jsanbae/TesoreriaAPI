<?php

namespace Jsanbae\TesoreriaAPI;

use Jsanbae\TesoreriaAPI\BarCode;
class DOM
{
    private string $dom;

    public function __construct(string $dom)
    {
        $this->dom = $dom;
    }

    /**
     * Prepara el DOM para ser renderizado
     *
     * @return string
     */
    public function prepareDOM():string
    {
        $dom = $this->dom;

        $barCodeNumber = $this->extractBarCodeNumberFromDOM($dom);
        $barCodeDOM = (new BarCode($barCodeNumber))();
        
        $preparedDOM = $this->insertBarcodeInDOM($dom, $barCodeDOM, $barCodeNumber);

        $preparedDOM = $this->getDOMResourcesAbsoluteRoutes($preparedDOM);

        return $preparedDOM;
    }

    /**
     * Extrae el número código de barra del DOM
     *
     * @param string $dom
     * @return string
     */
    private static function extractBarCodeNumberFromDOM(string $dom):string
    {
        $pattern = '/<input\s+type="hidden"\s+value="([^"]+)"\s+id="codigoBarra"\s*\/>/';

        preg_match($pattern, $dom, $matches);
        
        if (!isset($matches[1])) throw new \Exception('No se encontró el código de barra en el DOM');

        $codigo_barra = $matches[1];

        return  $codigo_barra;
    }
    
    /**
     * Modifica el DOM para que las rutas de los recursos sean absolutas
     *
     * @param string $dom
     * @param string $barcodeDOM
     * @param string $barcodeNumber
     * @return string
     */
    private function getDOMResourcesAbsoluteRoutes(string $dom):string
    {        
        // Expresión regular para encontrar las etiquetas que contienen 'src' o 'href'
        $regex = '/(src|href)\s*=\s*["\']([^"\']*)["\']/i';

        // Reemplazar las rutas relativas por rutas absolutas
        $html_con_rutas_absolutas = preg_replace_callback($regex, fn ($matches) => $this->convertToAbsoluteRoutes($matches), $dom);

        return $html_con_rutas_absolutas;
    }

    /**
     * Convierte rutas relativas a absolutas
     * 
     * @param array $matches
     * @return string 
     */
    private function convertToAbsoluteRoutes(array $matches):string
    {
        $absolute_url = $matches[0];

        $ruta_rel = $matches[2];
        $base_url = 'https://www.tesoreria.cl'; // Tu base URL aquí

        // Verificar si ya es una URL absoluta
        if (filter_var($ruta_rel, FILTER_VALIDATE_URL) === FALSE) {
            // Si no es absoluta, construir la URL absoluta
            $absolute_url = $matches[1] .'="'. $base_url . $matches[2].'"';
        }

        return $absolute_url;
    }

    /**
     * Extrae el código de barra del DOM
     *
     * @param string $dom
     * @return string
     */
    private function extractBarcodeFromDOM(string $dom):string
    {
        $pattern = '/<input\s+type="hidden"\s+value="([^"]+)"\s+id="codigoBarra"\s*\/>/';

        preg_match($pattern, $dom, $matches);
        
        if (!isset($matches[1])) throw new \Exception('No se encontró el código de barra en el DOM');

        $codigo_barra = $matches[1];

        return  $codigo_barra;
    }


    /**
     * Inserta el código de barra generado en el DOM
     *
     * @param string $dom
     * @param string $barcodeDOM
     * @param string $barcodeNumber
     * @return string
     */
    private function insertBarcodeInDOM(string $dom, string $barcodeDOM, string $barcodeNumber):string   
    {
        $pattern = '/<td\s+id="bcTarget">.*?<\/td>/s'; // Busca el td con id="bcTarget"
        $replacement = '<td id="bcTarget" style="padding: 0px; overflow: auto; width: 396px;">'.$barcodeDOM.'</td>'; // Inserto codigo de barra

        $dom_with_barcode = preg_replace($pattern, $replacement, $dom);

        $pattern = '/(<tr[^>]*>\s*<td[^>]*\s+id="bcTarget".*?<\/td>\s*<\/tr>)/s'; // Patrón para encontrar el tr con id="bcTarget"
        $replacement = '$1' . '<tr><td align="center" height="50px" valign="top">'.$barcodeNumber.'</td></tr>';// Inserto número del código de barra bajo el código de barra
        $dom_with_barcode_with_number = preg_replace($pattern, $replacement, $dom_with_barcode);

        return $dom_with_barcode_with_number;
    }
}
