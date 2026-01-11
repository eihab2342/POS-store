
@forelse($variants as $variant)
    <tr class="border-t hover:bg-gray-50">

        <td class="px-4 py-4 text-center">
            <input type="checkbox" name="selected[]" value="{{ $variant->id }}" class="row-checkbox">
        </td>

        <td class="px-6 py-4 font-medium">{{ $variant->sku }}</td>

        <td class="px-6 py-4">{{ $variant->name ?? '—' }}</td>

        <td class="px-6 py-4">
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                {{ $variant->size ?? '—' }}
            </span>
        </td>

<!--
        <td class="px-6 py-4">
            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                {{ $variant->color ?? '—' }}
            </span>
        </td>
-->
        <td class="px-6 py-4">{{ number_format($variant->cost) }} ج.م</td>
        <td class="px-6 py-4">{{ number_format($variant->price) }} ج.م</td>
	<td class="px-6 py-4">{{ number_format($variant->price - $variant->cost) }} ج.م</td>

        <td class="px-6 py-4">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $variant->stock_qty <= 0 ? 'bg-red-100 text-red-800'
            : ($variant->stock_qty < 5 ? 'bg-yellow-100 text-yellow-800'
                : 'bg-green-100 text-green-800') }}">
                {{ $variant->stock_qty }}
            </span>
        </td>

        <td class="px-6 py-4 text-center space-x-2 space-x-reverse">
            <a href="{{ route('variants.print.labels', $variant->id) }}" target="_blank"
                class="text-indigo-600 hover:underline">
                استيكر
            </a>

            <a href="{{ route('variants.edit', $variant) }}" class="text-green-600 hover:underline">
                تعديل
            </a>

            <button type="submit" form="delete-variant-{{ $variant->id }}" onclick="return confirm('متأكد من الحذف؟')"
                class="text-red-600 hover:underline">
                حذف
            </button>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-8 text-gray-500">لا توجد نتائج</td>
    </tr>
@endforelse