<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryPdfController extends Controller
{
    public function generateInventoryPdf(Request $request)
    {

        $products = ProductVariant::orderBy('id')
            ->get();

        return view('pdf.inventory', [
            'products' => $products,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
        ]);
    }
}