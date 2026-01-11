<?php

namespace App\Services;

use App\Models\Sale;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use ArPHP\I18N\Arabic;
use Exception;
use Illuminate\Support\Facades\Log;

class ReceiptPrinter
{
    protected ?Printer $printer = null;
    protected Arabic $arabic;

    public function __construct()
    {
        try {
            $connector = new WindowsPrintConnector('XprinterXPT371U');
            $this->printer = new Printer($connector);

            // جرب code pages مختلفة (واحد واحد):
            // $this->printer->getPrintConnector()->write("\x1B\x74\x12"); // 18 = CP864
            // $this->printer->getPrintConnector()->write("\x1B\x74\x11"); // 17 = ASMO 708
            $this->printer->getPrintConnector()->write("\x1B\x74\x16"); // 22 = Windows-1256 (الحالي)

            // تهيئة مكتبة ArPHP بالطريقة الصحيحة
            $this->arabic = new Arabic('Glyphs');
        } catch (Exception $e) {
            Log::error('Failed to initialize printer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * معالجة النص العربي - حل بسيط بدون ArPHP
     */
    protected function ar(string $text): string
    {
        try {
            // الحل البسيط: عكس النص مباشرة
            $reversed = strrev($text);

            // تحويل لـ CP1256 (لازم بالصيغة دي في PHP 8.4)
            $converted = mb_convert_encoding($reversed, 'CP1256', 'UTF-8');

            return $converted;
        } catch (Exception $e) {
            Log::error('Arabic conversion error: ' . $e->getMessage());
            return $text;
        }
    }

    /**
     * معالجة النص العربي - بـ ArPHP (مُعطّل حالياً)
     */
    protected function ar_with_arphp(string $text): string
    {
        try {
            // تشكيل الحروف العربية وربطها
            $text = $this->arabic->utf8Glyphs($text);

            // عكس اتجاه النص (من اليمين لليسار)
            $text = strrev($text);

            // تحويل من UTF-8 إلى CP1256
            $text = mb_convert_encoding($text, 'CP1256', 'UTF-8');

            return $text;
        } catch (Exception $e) {
            Log::error('Arabic conversion error: ' . $e->getMessage());
            // fallback: نرجع النص بدون معالجة
            return mb_convert_encoding($text, 'CP1256', 'UTF-8');
        }
    }

    public function printSale(Sale $sale): void
    {
        if (!$this->printer) {
            throw new Exception('Printer not initialized');
        }

        try {
            $p = $this->printer;

            // ===== الهيدر =====
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->setEmphasis(true);
            $p->text($this->ar('هوم وير') . "\n");
            $p->setEmphasis(false);
            $p->text($this->ar('سلكا - المنصورة - الدقهلية') . "\n");
            $p->text($this->ar('تليفون: ') . "01022789042\n");
            $p->text(str_repeat('-', 32) . "\n");

            // ===== بيانات الفاتورة =====
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $p->text($this->ar('رقم الفاتورة: ') . $sale->id . "\n");
            $p->text($this->ar('التاريخ: ') . $sale->date->format('d/m/Y H:i') . "\n");
            $p->text($this->ar('الكاشير: ') . '#' . $sale->cashier_id . "\n");

            $customerName = $sale->customer?->name ?? $this->ar('عميل نقدي');
            $p->text($this->ar('العميل: ') . $customerName . "\n");
            $p->text(str_repeat('-', 32) . "\n");

            // ===== الأصناف =====
            $p->text($this->ar('الصنف') . "        " . $this->ar('الكمية') . " " . $this->ar('السعر') . "  " . $this->ar('الاجمالي') . "\n");
            $p->text(str_repeat('-', 32) . "\n");

            foreach ($sale->items as $item) {
                try {
                    $name = $item->productVariant?->name ?? ('#' . $item->variant_id);
                    $name = mb_substr($name, 0, 12);

                    $qty = (int) $item->qty;
                    $price = number_format($item->price, 2);
                    $lineTotal = number_format($item->qty * $item->price - $item->discount, 2);

                    $p->text(
                        str_pad($this->ar($name), 12) . ' ' .
                        str_pad((string) $qty, 3, ' ', STR_PAD_LEFT) . ' ' .
                        str_pad($price, 6, ' ', STR_PAD_LEFT) . ' ' .
                        str_pad($lineTotal, 7, ' ', STR_PAD_LEFT) . "\n"
                    );

                    if ($item->discount > 0) {
                        $p->text($this->ar('خصم: ') . number_format($item->discount, 2) . "\n");
                    }
                } catch (Exception $e) {
                    Log::error('Error printing item: ' . $e->getMessage());
                }
            }

            $p->text(str_repeat('-', 32) . "\n");

            // ===== الإجماليات =====
            $p->text($this->ar('المجموع الفرعي: ') . number_format($sale->subtotal, 2) . " " . $this->ar('جنيه') . "\n");

            if ($sale->discount > 0) {
                $p->text($this->ar('الخصم: ') . number_format($sale->discount, 2) . " " . $this->ar('جنيه') . "\n");
            }
            if ($sale->tax > 0) {
                $p->text($this->ar('الضريبة: ') . number_format($sale->tax, 2) . " " . $this->ar('جنيه') . "\n");
            }

            $p->setEmphasis(true);
            $p->text($this->ar('الإجمالي: ') . number_format($sale->total, 2) . " " . $this->ar('جنيه') . "\n");
            $p->setEmphasis(false);

            $p->text($this->ar('المدفوع: ') . number_format($sale->paid, 2) . " " . $this->ar('جنيه') . "\n");

            if ($sale->remaining > 0) {
                $p->text($this->ar('المتبقي: ') . number_format($sale->remaining, 2) . " " . $this->ar('جنيه') . "\n");
            } elseif ($sale->remaining < 0) {
                $p->text($this->ar('الباقي للعميل: ') . number_format(abs($sale->remaining), 2) . " " . $this->ar('جنيه') . "\n");
            } else {
                $p->text($this->ar('الرصيد: ') . "0.00 " . $this->ar('جنيه') . "\n");
            }

            $p->text(str_repeat('-', 32) . "\n");

            // ===== طريقة الدفع =====
            $method = match ($sale->payment_method) {
                'cash' => $this->ar('نقدي'),
                'card' => $this->ar('بطاقة'),
                'transfer' => $this->ar('تحويل'),
                default => $sale->payment_method ?? 'cash',
            };
            $p->text($this->ar('طريقة الدفع: ') . $method . "\n");

            // ===== باركود =====
            try {
                $p->feed(1);
                $p->setJustification(Printer::JUSTIFY_CENTER);

                $barcodeData = preg_replace('/[^0-9]/', '', (string) $sale->id);

                if (!empty($barcodeData) && strlen($barcodeData) > 0 && strlen($barcodeData) <= 12) {
                    $p->barcode($barcodeData, Printer::BARCODE_CODE39);
                    $p->feed(1);
                }
            } catch (Exception $e) {
                Log::warning('Barcode error: ' . $e->getMessage());
            }

            // ===== الفوتر =====
            $p->text($this->ar('شكراً لزيارتكم') . "\n");
            $p->text($this->ar('تم الطباعة: ') . now()->format('d/m/Y H:i') . "\n");
            $p->feed(3);
            $p->cut();

        } catch (Exception $e) {
            Log::error('Print error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->closePrinter();
        }
    }

    public function closePrinter(): void
    {
        if ($this->printer !== null) {
            try {
                $this->printer->close();
            } catch (Exception $e) {
                Log::warning('Failed to close printer: ' . $e->getMessage());
            } finally {
                $this->printer = null;
            }
        }
    }

    public function __destruct()
    {
        $this->closePrinter();
    }
}