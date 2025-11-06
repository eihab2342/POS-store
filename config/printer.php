<?php

return [
    'enabled' => env('PRINTER_ENABLED', true),
    'host' => env('PRINTER_HOST', '192.168.1.50'), // غيّرها لعنوان الطابعة على الشبكة
    'port' => (int) env('PRINTER_PORT', 9100),     // غالباً 9100 لطابعات Zebra/Raw
    'barcode_type' => env('PRINTER_BARCODE_TYPE', 'code128'), // code128 | qr | ean13
];
