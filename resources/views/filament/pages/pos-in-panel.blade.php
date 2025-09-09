<x-filament::page>
    <div class="space-y-6" dir="rtl">
        {{ $this->form }}

        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl">
            <div class="text-lg font-bold">
                الإجمالي: <span class="text-primary-600">{{ number_format($this->subtotal, 2) }} ج.م</span>
            </div>
            <x-filament::button wire:click="checkout" color="primary" icon="heroicon-o-check"
                :disabled="empty($this->cart)">
                تحصيل
            </x-filament::button>
        </div>
    </div>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('open-url', ({
            url
        }) => window.open(url, '_blank'));
    });
    </script>
</x-filament::page>