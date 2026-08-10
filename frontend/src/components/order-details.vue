<template>
    <div class="min-h-screen bg-slate-100">
        <header class="bg-white border-b border-slate-200">
        <headerSection />
        </header>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <navbar />

            <main class="lg:col-span-9 space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Order Details</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Invoice:
                    <span class="font-semibold text-slate-900">INV-{{ order?.reg || "-" }}</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    Date:
                    <span class="font-semibold text-slate-700">{{ paymentDetails?.date || order?.date || "-" }}</span>
                    <span class="mx-2">•</span>
                    TRX:
                    <span class="font-semibold text-slate-700">{{ paymentDetails?.transaction_id || order?.transaction_id || "-" }}</span>
                </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2">
                <!-- <button
                    @click="printSummary"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 active:scale-[.99] transition"
                >
                    <i class="fa-solid fa-print mr-2"></i> Summary
                </button> -->

                <button
                    @click="printInvoice(order?.reg)"
                    class="rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700 active:scale-[.99] transition"
                >
                    <i class="fa-solid fa-print mr-2"></i> Print Invoice
                </button>

                <button
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 active:scale-[.99] transition"
                    @click="router.back()"
                >
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </button>
                </div>
            </div>

            <!-- Loading -->
            <div
                v-if="loading"
                class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center"
            >
                <div class="inline-flex items-center gap-2 text-slate-700 font-semibold">
                <span class="h-4 w-4 rounded-full border-2 border-slate-300 border-t-slate-700 animate-spin"></span>
                Loading order...
                </div>
                <p class="mt-2 text-xs text-slate-500">Please wait a moment.</p>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700"
            >
                <div class="font-bold">Error</div>
                <div class="text-sm mt-1">{{ error }}</div>
            </div>

            <!-- Not found -->
            <div
                v-else-if="!order"
                class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center text-slate-600"
            >
                Order not found.
            </div>

            <!-- Content -->
            <div v-else class="space-y-6">
                <!-- Top Cards -->
                <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                    <p class="text-xs font-semibold text-slate-500">PAYMENT METHOD</p>
                    <p class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ paymentDetails?.payment_method?.name || "-" }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Currency: {{ paymentDetails?.currency || "BDT" }}</p>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                    <p class="text-xs font-semibold text-slate-500">TOTAL QTY</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ summary?.qty_total ?? 0 }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Total items quantity.</p>
                </div>

                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                    <p class="text-xs font-semibold text-slate-500">TOTAL</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    ৳ {{ formatMoney(paymentDetails?.total ?? order?.total ?? 0) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Before discount/VAT.</p>
                </div>

                <div
                    class="rounded-2xl border shadow-sm p-5"
                    :class="Number(paymentDetails?.due || 0) > 0 ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200'"
                >
                    <p
                    class="text-xs font-semibold"
                    :class="Number(paymentDetails?.due || 0) > 0 ? 'text-red-700' : 'text-emerald-700'"
                    >
                    DUE
                    </p>
                    <p
                    class="mt-2 text-2xl font-extrabold"
                    :class="Number(paymentDetails?.due || 0) > 0 ? 'text-red-800' : 'text-emerald-800'"
                    >
                    ৳ {{ Number(paymentDetails?.due || 0).toFixed(2) }}
                    </p>
                    <p
                    class="mt-1 text-xs"
                    :class="Number(paymentDetails?.due || 0) > 0 ? 'text-red-600' : 'text-emerald-600'"
                    >
                    {{ Number(paymentDetails?.due || 0) > 0 ? 'Pending amount to collect.' : 'Paid & closed.' }}
                    </p>
                </div>
                </section>

                <!-- Payment Summary -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-slate-900">Payment Summary</h2>
                    <span
                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                    :class="statusBadge(order?.status)"
                    >
                    {{ order?.status || "-" }}
                    </span>
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                    <span class="text-slate-600">Total</span>
                    <span class="font-semibold text-slate-900">৳ {{ Number(paymentDetails?.total || 0).toFixed(2) }}</span>
                    </div>

                    <div class="flex justify-between">
                    <span class="text-slate-600">Discount ({{ Number(paymentDetails?.discount_rate || 0).toFixed(2) }}%)</span>
                    <span class="font-semibold text-slate-900">- ৳ {{ Number(paymentDetails?.discount_amount || 0).toFixed(2) }}</span>
                    </div>

                    <div class="flex justify-between">
                    <span class="text-slate-600">VAT ({{ Number(paymentDetails?.vat_rate || 0).toFixed(2) }}%)</span>
                    <span class="font-semibold text-slate-900">৳ {{ Number(paymentDetails?.vat_amount || 0).toFixed(2) }}</span>
                    </div>

                    <div class="border-t pt-3 flex justify-between">
                    <span class="text-slate-900 font-extrabold">Payable</span>
                    <span class="text-slate-900 font-extrabold">৳ {{ Number(paymentDetails?.payable || 0).toFixed(2) }}</span>
                    </div>

                    <div class="flex justify-between">
                    <span class="text-slate-600">Paid</span>
                    <span class="font-semibold text-slate-900">৳ {{ Number(paymentDetails?.pay || 0).toFixed(2) }}</span>
                    </div>

                    <div
                    class="mt-3 rounded-2xl border px-4 py-3 flex justify-between"
                    :class="Number(paymentDetails?.due || 0) > 0 ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50'"
                    >
                    <span
                        class="font-semibold"
                        :class="Number(paymentDetails?.due || 0) > 0 ? 'text-red-700' : 'text-emerald-700'"
                    >
                        Due
                    </span>
                    <span
                        class="font-extrabold"
                        :class="Number(paymentDetails?.due || 0) > 0 ? 'text-red-700' : 'text-emerald-700'"
                    >
                        ৳ {{ Number(paymentDetails?.due || 0).toFixed(2) }}
                    </span>
                    </div>
                </div>
                </section>

                <!-- Items -->
                <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-900">Order Items</h3>
                    <p class="text-xs text-slate-500">
                    Rows: <span class="font-bold text-slate-900">{{ items?.length || 0 }}</span>
                    </p>
                </div>

                <div v-if="!items || items.length === 0" class="p-10 text-center text-slate-600">
                    No items found for this order.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                        <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Product</th>
                        <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Qty</th>
                        <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Price</th>
                        <th class="text-right font-semibold px-4 py-3 whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="it in items" :key="it.id" class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ it?.product?.name || "—" }}</div>
                            <div class="text-xs text-slate-500" v-if="it?.product?.sku">SKU: {{ it.product.sku }}</div>
                        </td>

                        <td class="px-4 py-3 text-slate-700">{{ it.quantity }}</td>

                        <td class="px-4 py-3 text-slate-700">৳ {{ formatMoney(it.price) }}/-</td>

                        <td class="px-4 py-3 text-right font-semibold text-slate-900">
                            ৳ {{ formatMoney(Number(it.quantity || 0) * Number(it.price || 0)) }}/-
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                </section>
            </div>
            </main>
        </div>
        </div>
    </div>
</template>



<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../services/api";

import navbar from './navbar.vue'
import headerSection from './header-section.vue'

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const error = ref("");

const order = ref(null);
const items = ref([]);
const summary = ref(null);
const paymentDetails = ref(null);

function formatMoney(n) {
    return new Intl.NumberFormat("en-US").format(Number(n || 0));
}

function statusBadge(status) {
    const s = String(status || "").toLowerCase();
    if (s === "paid") return "bg-green-50 border-green-200 text-green-700";
    if (s === "pending") return "bg-yellow-50 border-yellow-200 text-yellow-700";
    if (s === "cancelled" || s === "canceled") return "bg-red-50 border-red-200 text-red-700";
    return "bg-slate-50 border-slate-200 text-slate-700";
}

function formateDate(dateStr){
    if(!dateStr) return "-";

    const d = new Date(dateStr);

    return d.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });
}

async function loadOrder() {
    const id = route.params.id;
    if (!id) return;

    loading.value = true;
    error.value = "";

    try {
        const res = await api.post(`/order/details/${id}`);
        order.value = res.data?.data?.order ?? null;
        paymentDetails.value = res.data?.data?.paymentDetails ?? null;
        items.value = res.data?.data?.cartitems ?? [];
        summary.value = res.data?.data?.summary ?? null;        
    } catch (e) {
        error.value = e?.response?.data?.message || "Order load failed.";
    } finally {
        loading.value = false;
    }
}

function printInvoice(reg) {
    const win = window.open("", "_blank");
    if (!win) {
        alert("Popup blocked! Allow popups.");
        return;
    }
    
    const url = `/order/invoice-print/${reg}`;
    console.log("button clicked", url);

    win.location.href = url;
    win.focus();
}


onMounted(loadOrder);

</script>

<style>

</style>