<?php

namespace App\Filament\Pages;

use App\DTOs\CartItem;
use App\Repositories\ProductVariantRepository;
use App\Services\SalesService;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use App\Models\Sale;

class PosInPanel extends Page implements HasForms
{

    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'عمليات الكاشير';
    protected static ?string $title = 'استخراج فاتورة';
    protected static string $view = 'filament.pages.pos-in-panel';

    public ?string $sku = '';
    public array $cart = [];
    public ?string $phone = '';
    protected function getHeaderActions(): array
    {
        return [
            Action::make('add')
                ->label('إضافة')
                ->icon('heroicon-m-plus')
                ->disabled(fn() => blank($this->sku))
                ->action('addBySku'),

            // -------
            Action::make('checkout')
                ->label('تحصيل')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->disabled(fn() => empty($this->cart))
                ->action('checkout'),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->extraAttributes(['wire:keydown.enter' => 'addBySku'])
                ->live(debounce: 300) // يضمن مزامنة أسرع (اختياري)
                ->suffixAction(
                    Forms\Components\Actions\Action::make('add')
                        ->label('إضافة')
                        ->icon('heroicon-m-plus')
                        ->disabled(fn(Get $get) => blank($get('sku'))) // عطّل لو فاضي
                        ->action(function (Get $get) {
                            $this->sku = (string) $get('sku');
                        })
                ),
            Forms\Components\TextInput::make('phone')
                ->label('هاتف العميل')
                ->tel()
                ->live()
                ->maxLength(20)
                ->columnSpanFull(),

            Forms\Components\Repeater::make('cart')
                ->label('السلة')
                ->columns(12)
                ->deletable(true)
                ->reorderable(false)
                ->schema([
                    Forms\Components\TextInput::make('sku')
                        ->label('معرف الصنف')
                        ->disabled()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('name')
                        ->label('الصنف')
                        ->disabled()
                        ->columnSpan(5.5),

                    Forms\Components\TextInput::make('price')
                        ->label('السعر')
                        ->numeric()
                        ->disabled()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('qty')
                        ->label('الكمية')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->live()
                        ->columnSpan(1.5),

                    Forms\Components\TextInput::make('line_total')
                        ->label('الإجمالي')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(2),
                ])
                ->columnSpanFull(),
        ];
    }

    public function addBySku(ProductVariantRepository $variants): void
    {
        if (! $this->sku) return;

        $dto = $variants->findCartItemBySku($this->sku);
        if (! $dto) {
            Notification::make()->danger()->title('الصنف غير موجود')->send();
            return;
        }

        $idx = collect($this->cart)->search(fn($i) => $i['variant_id'] === $dto->variant_id);
        if ($idx !== false) {
            $this->cart[$idx]['qty']++;
        } else {
            $this->cart[] = [
                'variant_id' => (int)    $dto->variant_id,
                'sku'        => (string) $dto->sku,
                'name'       => (string) $dto->name,
                'price'      => (float)  $dto->price,
                'qty'        => 1,
                'line_total' => (float)  $dto->price,
            ];
        }

        $this->sku = '';
        $this->refreshCart();
        $this->form->fill([
            'sku'  => $this->sku,
            'cart' => $this->cart,
        ]);
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn($i) => (float) ($i['price'] ?? 0) * (int) ($i['qty'] ?? 1));
    }

    public function checkout(SalesService $service)
    {
        try {
            $items = array_map(
                fn($i) => new CartItem(
                    variant_id: (int)   $i['variant_id'],
                    name: (string)$i['name'],
                    price: (float) $i['price'],
                    sku: isset($i['sku']) ? (string)$i['sku'] : null,
                    qty: (int)   $i['qty']
                ),
                $this->cart
            );

            $formData = $this->form->getState();
            $phone = $formData['phone'] ?? null;

            // أنشئ أو حدث العميل حسب رقم الهاتف
            $customer = $phone
                ? \App\Models\Customer::firstOrCreate(
                    ['phone' => $phone],
                    ['name' => 'عميل نقدي']
                )
                : null;

            $saleId = $service->checkout($items, Auth::id(), $customer?->id);

            $this->dispatch('open-url', url: route('receipt.show', $saleId));

            $this->cart = [];
            $this->form->fill(['sku' => '', 'cart' => [], 'phone' => '']);
            // dd($saleId, Sale::find($saleId)->customer_id);

            Notification::make()->success()->title("تم إنشاء الفاتورة #$saleId")->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title('خطأ')->body($e->getMessage())->send();
        }
    }

    private function refreshCart()
    {
        $this->cart = collect($this->cart)->map(function ($i) {
            $i['line_total'] = $i['price'] * $i['qty'];
            return $i;
        })->all();
        $this->form->fill([
            'sku'  => $this->sku,
            'cart' => $this->cart,
        ]);
    }
}