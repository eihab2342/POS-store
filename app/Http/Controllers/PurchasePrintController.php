<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Support\Str;

class PurchasePrintController extends Controller
{
    public function printSale(\App\Models\Sale $sale)
    {
        // 1) جهّز النص اللي هيتطبع
        $content = "YAZAN\n";
        $content .= "فاتورة رقم: {$sale->id}\n";
        $content .= "التاريخ: " . $sale->created_at->format('Y-m-d H:i') . "\n";
        $content .= "-------------------------\n";

        foreach ($sale->items as $item) {
            $name = $item->variant->product->name ?? $item->name ?? 'صنف';
            $lineTotal = $item->price * $item->qty;
            $content .= "{$name}\n";
            $content .= "  {$item->qty} x {$item->price} = {$lineTotal}\n";
        }

        $content .= "-------------------------\n";
        $content .= "الإجمالي: {$sale->total}\n";
        $content .= "شكراً لزيارتكم\n";

        // 2) احفظه في ملف مؤقت
        $filename = storage_path('app/print-' . Str::random(6) . '.txt');
        file_put_contents($filename, $content);

        // 3) ابعته للطابعة عن طريق ويندوز
        // غيّر XP-Q371U لاسم طابعتك بالظبط
        $printerName = 'printer';
        $command = 'print /D:"' . $printerName . '" "' . $filename . '"';

        // شغّل الأمر
        $output = shell_exec($command);

        return back()->with('success', 'تم إرسال الفاتورة للطابعة');
    }
    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.variant.product');
        return view('purchases.receipt', compact('purchase'));
    }
}