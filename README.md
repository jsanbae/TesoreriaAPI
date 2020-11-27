# TesoreriaAPI
Utilidad para automatizar procesos desde el sitio de la TGR, tales como: 
- Comprobación de Pago en linea de Tributos Aduaneros en TGR
- Generación de PDF del comprobante de pago de Tributos Aduaneros en TGR

## Requerimientos
 - wkhtmltopdf (comando necesario para transformar a PDF)
 - xvfb-run (necesario si es que wkhtmltopdf no funciona correctamente)
 
 ## Ejemplo de eso
```
require './TesoreriaAPI.php';

$rut =  99999999;
$dv =  '9';
$form = 15;
$folio = 1110099999;

$tAPI = new TesoreriaAPI($rut, $dv, $form, $folio);
$isPagada = $tAPI->isTesoreriaPagada();
$comprobante_pdf = $tAPI->generaComprobantePago('comprobante.pdf');
```
