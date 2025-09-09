<?php

namespace App\Services;

use App\Models\SupplierPayment;

class SupplierPaymentService
{
    public function pay(int $supplierId, float $amount, string $method = 'cash', ?string $note = null): int
    {
        $p = SupplierPayment::create([
            'supplier_id' => $supplierId,
            'date'        => now(),
            'amount'      => $amount,
            'method'      => $method,
            'note'        => $note,
        ]);
        return $p->id;
    }
}