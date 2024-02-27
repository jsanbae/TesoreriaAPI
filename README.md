# TesoreriaAPI
Utilidad para automatizar procesos desde el sitio de la TGR, tales como: 
- Comprobación de Pago en linea de Tributos Aduaneros en TGR
- Generación de PDF del comprobante de pago de Tributos Aduaneros en TGR

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

## Test

Para correr test usar:

```
 phpunit ./test
```