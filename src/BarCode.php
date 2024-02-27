<?php

namespace Jsanbae\TesoreriaAPI;

use Picqer\Barcode\BarcodeGeneratorHTML;

class BarCode
{
    private $barcode_number;

    public function __construct(string $barcode_number)
    {
        $this->barcode_number = $barcode_number;
    }
    public function __invoke():string
    {
        $generator = new BarcodeGeneratorHTML();
        $bar_code = $generator->getBarcode($this->barcode_number, $generator::TYPE_CODE_128, 2, 65);

        return $bar_code;
    }
}
