<?php

namespace App\Services;

use App\Models\ProductVariant;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class ThermalPrintService
{
    /**
     * طباعة استيكرات على طابعة حرارية
     *
     * @param ProductVariant $variant
     * @param int $quantity
     * @param string|null $printerName
     */
    public static function printLabels(ProductVariant $variant, int $quantity = 1, ?string $printerName = null)
    {
        try {
            // اسم الطابعة من الكونفيج إن لم يُمرَّر
            if (empty($printerName)) {
                $printerName = config('printing.thermal_printer', 'Xprinter XP-Q371U');
            }

            $printerName = trim((string) $printerName);
            if ($printerName === '') {
                throw new \RuntimeException('اسم الطابعة غير محدد.');
            }

            // ✅ في حالتك: شغال على Windows وطابعة Shared باسم Xprinter XP-Q371U
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $connector = new WindowsPrintConnector($printerName);
            } else {
                // لو شغّلت من نظام آخر، يطبع في ملف كاختبار بدلاً من null
                $connector = new FilePrintConnector(storage_path('app/print-output.bin'));
            }

            $printer = new Printer($connector);

            // طباعة كل استيكر
            for ($i = 0; $i < $quantity; $i++) {
                self::printSingleLabel($printer, $variant);

                if ($i < $quantity - 1) {
                    $printer->feed(2);
                    $printer->cut();
                }
            }

            // قطع في النهاية
            $printer->cut();
            $printer->close();

            return [
                'success' => true,
                'message' => "تم طباعة {$quantity} استيكر بنجاح"
            ];

        } catch (\Throwable $e) {
            \Log::error('Thermal printing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'فشل الطباعة: ' . $e->getMessage()
            ];
        }
    }

    /**
     * طباعة استيكر واحد
     */
    private static function printSingleLabel(Printer $printer, ProductVariant $variant)
    {
        $sku = trim((string) $variant->sku);

        // 1. اسم المنتج (مع محاولة دعم CP864 للعربي)
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(1, 1);
        $printer->setEmphasis(true);

        $name = self::truncateArabic($variant->name ?? 'منتج', 30);
        $cp864Name = @iconv('UTF-8', 'CP864//IGNORE', $name) ?: $name;
        $printer->text($cp864Name . "\n");

        $printer->setEmphasis(false);
        $printer->feed(1);

        // 2. الباركود Code128 مباشر (بدون DNS1D, بدون صور)
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer->setBarcodeHeight(60);
        $printer->setBarcodeWidth(2);

        // Code128 هنا يتوقع ASCII فقط
        $asciiSku = preg_replace('/[^\x20-\x7E]/', '', $sku);

        if ($asciiSku !== '') {
            // {B = Code Set B (حروف + أرقام)
            $printer->barcode('{B' . $asciiSku, Printer::BARCODE_CODE128);
        } else {
            // لو شيء غلط في الـ SKU، نطبع نص بدل ما نسيب المكان فاضي
            $printer->text("SKU: " . $sku . "\n");
        }

        $printer->feed(1);

        // 3. SKU كنص
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(1, 1);
        $printer->text("SKU: " . $sku . "\n");
        $printer->feed(1);

        // 4. السعر
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->setEmphasis(true);

        $priceText = number_format($variant->price, 2) . " EGP";
        $cp864Price = @iconv('UTF-8', 'CP864//IGNORE', $priceText) ?: $priceText;
        $printer->text($cp864Price . "\n");

        $printer->setEmphasis(false);
        $printer->setTextSize(1, 1);
    }

    /**
     * قص النص العربي
     */
    private static function truncateArabic(string $text, int $maxLength): string
    {
        if (mb_strlen($text, 'UTF-8') <= $maxLength) {
            return $text;
        }

        return mb_substr($text, 0, $maxLength - 3, 'UTF-8') . '...';
    }

    /**
     * جلب قائمة الطابعات (Windows فقط)
     */
    public static function getAvailablePrinters(): array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return [];
        }

        try {
            $output = shell_exec('wmic printer get name');
            $lines = explode("\n", $output);
            $printers = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && $line !== 'Name') {
                    $printers[] = $line;
                }
            }

            return $printers;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
