<script setup>
import { ref, onMounted, computed } from "vue";

const code = ref("");
const items = ref([]);
const subtotal = computed(() =>
    items.value.reduce((s, i) => s + i.qty * i.price, 0)
);

function scan() {
    fetch("/pos/scan", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ code: code.value }),
    })
        .then((r) => r.json())
        .then((d) => {
            console.log("SCAN RESPONSE:", d);
            if (d.error) return alert(d.error);

            const idx = items.value.findIndex((x) => x.variant_id === d.id);
            if (idx > -1) {
                items.value[idx].qty++;
            } else {
                items.value.push({
                    variant_id: d.id,
                    name: d.name,
                    price: Number(d.price),
                    qty: 1,
                });
            }
            code.value = "";
        });
}

onMounted(() => {
    const url = new URL(window.location.href);
    const preset = url.searchParams.get("code");
    if (preset) {
        code.value = preset;
        scan();
    }
});

function checkout() {
    fetch("/pos/checkout", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ items: items.value }),
    })
        .then(async (r) => {
            const text = await r.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("Server response was not JSON:", text);
                throw e;
            }
        })
        .then((d) => {
            if (d.status === "ok") {
                alert("تم إصدار الفاتورة رقم: " + d.sale_id);
                window.open("/receipt/" + d.sale_id, "_blank");
                items.value = [];
            }
        });
}
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center py-10"
    >
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl p-8">
            <h1 class="text-4xl font-extrabold text-center text-green-700 mb-8">
                💳 نظام الكاشير (POS)
            </h1>

            <div class="flex items-center gap-3 mb-8">
                <input
                    v-model="code"
                    @keyup.enter="scan"
                    placeholder="✨ امسح الباركود أو اكتب كود المنتج"
                    class="flex-1 px-5 py-4 border-2 border-gray-300 rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm"
                />
                <button
                    @click="scan"
                    class="px-6 py-4 bg-green-600 text-white text-lg font-bold rounded-xl shadow hover:bg-green-700 transition"
                >
                    ➕ إضافة
                </button>
            </div>

            <div class="overflow-x-auto mb-6">
                <table
                    class="w-full border border-gray-200 shadow rounded-xl overflow-hidden text-lg"
                >
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="p-4 text-right">الصنف</th>
                            <th class="p-4">السعر</th>
                            <th class="p-4">الكمية</th>
                            <th class="p-4">الإجمالي</th>
                            <th class="p-4">❌</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(i, idx) in items"
                            :key="idx"
                            class="border-b hover:bg-gray-50"
                        >
                            <td
                                class="p-4 text-right font-medium text-gray-700"
                            >
                                {{ i.name }}
                            </td>
                            <td class="p-4 text-center text-gray-600">
                                {{ i.price }} ج.م
                            </td>
                            <td class="p-4 text-center">
                                <input
                                    type="number"
                                    v-model.number="i.qty"
                                    min="1"
                                    class="w-20 border border-gray-300 rounded-md px-2 py-1 text-center focus:ring-2 focus:ring-green-400"
                                />
                            </td>
                            <td
                                class="p-4 text-center font-semibold text-gray-800"
                            >
                                {{ i.qty * i.price }} ج.م
                            </td>
                            <td class="p-4 text-center">
                                <button
                                    @click="items.splice(idx, 1)"
                                    class="text-red-600 hover:text-red-800 text-xl"
                                >
                                    ❌
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="3" class="p-4 text-right">الإجمالي</td>
                            <td class="p-4 text-center text-green-700 text-xl">
                                {{ subtotal }} ج.م
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-center">
                <button
                    @click="checkout"
                    class="px-10 py-4 bg-blue-600 text-white text-2xl font-extrabold rounded-2xl shadow-lg hover:bg-blue-700 transition"
                >
                    ✅ تحصيل
                </button>
            </div>
        </div>
    </div>
</template>
