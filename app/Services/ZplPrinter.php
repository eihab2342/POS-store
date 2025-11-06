<?php

namespace App\Services;

class ZplPrinter
{
    public function send(string $host, int $port, string $zpl): void
    {
        $errno = $errstr = null;
        $socket = @fsockopen($host, $port, $errno, $errstr, 5);
        if (!$socket) {
            throw new \RuntimeException("Printer connection failed: $errstr ($errno)");
        }
        fwrite($socket, $zpl); // بعض الطابعات تفضّل إنهاء برسالة فيها \r\n (أضفناه في buildLabel)
        fclose($socket);
    }

    /**
     * @param string      $code   قيمة الكود (مثلاً PV1-000001)
     * @param string      $type   code128|ean13|qr
     * @param string|null $name   اسم المنتج (اختياري)
     * @param string|null $price  السعر (اختياري)
     * @param string|null $sku    الـSKU (مثلاً PNE575)
     * @param string      $encode أيهما يُشفّر داخل الباركود: 'sku' أو 'code' (افتراضي sku)
     * @param bool        $showHri اطبع النص أسفل الباركود (HRI)؟ افتراضي false
     */
    public static function buildLabel(
        string $code,
        string $type = 'code128',
        ?string $name = null,
        ?string $price = null,
        ?string $sku = null,
        string $encode = 'sku',
        bool $showHri = false
    ): string {
        $pw = 600;  // عرض الليبل بالنقاط
        $ll = 400;  // ارتفاع الليبل
        $by = "^BY2,2,60";

        // نحدد أي قيمة هتتشفّر جوّه الباركود
        $payload = ($encode === 'sku' && $sku) ? $sku : $code;

        // تنظيف للعرض/الباركود
        $payload = self::sanitize($payload);
        $nameStr = $name ? "^FO30,30^A0N,28,28^FD" . self::sanitize($name) . "^FS" : "";

        $yBarcode = 70;
        $yAfterBarcodeText = 175;
        $ySku = 180;
        $yPrice = $sku ? 230 : 210;

        // باركود بحسب النوع
        $barcode = match (strtolower($type)) {
            'ean13' => self::buildEan13($payload, $yBarcode, $by, $showHri),
            'qr' => "^FO30,{$yBarcode}^BQN,2,6^FDLA,{$payload}^FS",
            default => self::buildCode128($payload, $yBarcode, $by, $showHri),
        };

        // سطر الـSKU (كنص للعرض فقط)
        $skuLn = $sku
            ? "^FO30,{$ySku}^A0N,24,24^FB540,2,0,L,0^FDSKU: " . self::sanitize($sku) . "^FS"
            : "";

        // سطر السعر
        $priceStr = $price ? "^FO30,{$yPrice}^A0N,24,24^FD" . self::sanitize($price) . " EGP^FS" : "";

        // تجميعة ZPL
        return "^XA^PW{$pw}^LL{$ll}{$nameStr}{$barcode}{$skuLn}{$priceStr}^XZ\r\n";
    }

    protected static function buildCode128(string $payload, int $y, string $by, bool $showHri): string
    {
        // ^BC: ^BCo,h,f,g,m
        // f = HRI تحت الباركود (Y/N)
        $f = $showHri ? 'Y' : 'N';
        return "^FO30,{$y}{$by}^BCN,100,{$f},N,N^FD{$payload}^FS";
    }

    protected static function buildEan13(string $payload, int $y, string $by, bool $showHri): string
    {
        // تأكد إن القيمة رقمية وطولها 12 أو 13
        $digits = preg_replace('/\D+/', '', $payload);
        if (strlen($digits) === 12) {
            $digits .= self::ean13CheckDigit($digits);
        } elseif (strlen($digits) !== 13) {
            throw new \InvalidArgumentException('EAN-13 must be 12 or 13 digits.');
        }
        $f = $showHri ? 'Y' : 'N';
        // ^BE: ^BEo,h,f,g
        return "^FO30,{$y}{$by}^BEN,100,{$f},N^FD{$digits}^FS";
    }

    protected static function ean13CheckDigit(string $digits12): int
    {
        // حساب خانة التحقق
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $n = (int) $digits12[$i];
            $sum += ($i % 2 === 0) ? $n : $n * 3;
        }
        $cd = (10 - ($sum % 10)) % 10;
        return $cd;
    }

    protected static function sanitize(string $s): string
    {
        // ASCII فقط، مع إزالة محارف تحكم قد تكسر ^FD
        $s = preg_replace('/[^\x20-\x7E]/', '', $s) ?? '';
        // يُفضّل تجنّب ^ داخل النص؛ لو عندك حالات خاصة استخدم ^FH وتعامل بالهيكس
        return str_replace('^', '', $s);
    }
}
