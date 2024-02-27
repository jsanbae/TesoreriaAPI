<?php
    
// require 'vendor/autoload.php';

use Jsanbae\TesoreriaAPI\ComprobanteNotFoundException;
use Jsanbae\TesoreriaAPI\TesoreriaAPI;

use PHPUnit\Framework\TestCase;

class APITest extends TestCase
{
    protected $TesoreriaAPI;

    public function setUp():void
    {
        parent::setUp();

        $rut = 76688700;
        $dv = '7';
        $formulario = 15;
        $folio = 3920140177;
        
        $this->TesoreriaAPI = new TesoreriaAPI($rut, $dv, $formulario, $folio);
    }

    public function test_genera_comprobante_tesoreria()
    {        
        $filename = $this->TesoreriaAPI->generaComprobantePago();

        $this->assertFileExists($filename, 'No se generó el archivo del comprobante de pago');
    }

    public function test_check_if_tesoreria_pagado()
    {
        $is_pagado = $this->TesoreriaAPI->isTesoreriaPagada();

        $this->assertTrue($is_pagado, 'No se encontró el comprobante de pago');
    }

    public function test_check_if_not_tesoreria_pagado()
    {
        $TesoreriaAPI = new TesoreriaAPI(99999999, 0, '15', 99999);
        $is_not_pagado = $TesoreriaAPI->isTesoreriaPagada();

        $this->assertFalse($is_not_pagado, 'Se encontró el comprobante de pago');
    }

    public function test_not_found_tesoreria_comprobante()
    {
        $this->expectException(ComprobanteNotFoundException::class);

        $TesoreriaAPI = new TesoreriaAPI(99999999, 0, '15', 999999);
        $TesoreriaAPI->generaComprobantePago();
    }
}