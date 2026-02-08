<template>
    <div class="min-h-screen bg-slate-100">
        <header class="bg-white border-b">
            <headerSection />
        </header>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <navbar />

                <main class="lg:col-span-9 space-y-6">
                    <!-- Header -->                    

                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">Order Details</h1>
                            <p class="text-sm text-slate-600">
                            Invoice:
                            <span class="font-semibold">INV-{{ order?.reg || "-" }}</span>
                            </p>
                        </div>

                        <!-- ✅ Print Buttons -->
                        <div class="flex gap-2">

                            <!-- Print Summary -->
                            <button
                            @click="printSummary"
                            class="rounded-xl border bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                            <i class="fa-solid fa-print"></i> Summary
                            </button>

                            <!-- Print Full -->                             
                            <button
                            @click="printInvoice(order.reg)"
                            class="rounded-xl bg-blue-500 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700"
                            >
                            <i class="fa-solid fa-print"></i> Print Invoice
                            </button>

                            <button
                                class="rounded-xl border bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                @click="router.back()">
                            <i class="fa-solid fa-arrow-left"></i> Back
                            </button>

                        </div>
                    </div>


                    <!-- Loading -->
                    <div
                        v-if="loading"
                        class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center text-slate-600">
                        Loading order...
                    </div>

                    <!-- Error -->
                    <div
                        v-else-if="error"
                        class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                        {{ error }}
                    </div>

                    <!-- Not found -->
                    <div
                        v-else-if="!order"
                        class="rounded-2xl bg-white border border-slate-200 shadow-sm p-10 text-center text-slate-600">
                        Order not found.
                    </div>

                    <!-- Content -->
                    <div v-else class="space-y-6">
                        <!-- ✅ Summary -->
                        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                <p class="text-xs font-semibold text-slate-500">DATE</p>
                                <p class="mt-1 font-semibold text-slate-900">{{formateDate(order.date) }}</p>
                                </div>

                                <div>
                                <p class="text-xs font-semibold text-slate-500">TOTAL</p>
                                <p class="mt-1 font-semibold text-slate-900">৳ {{ formatMoney(order.total) }}/-</p>
                                </div>

                                <div>
                                <p class="text-xs font-semibold text-slate-500">STATUS</p>
                                <span
                                    class="mt-2 inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="statusBadge(order.status)"
                                >
                                    {{ order.status }}
                                </span>
                                </div>

                                <div>
                                <p class="text-xs font-semibold text-slate-500">TRANSACTION</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ order.transaction_id || "-" }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- ✅ Items -->
                        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <div class="p-4 border-b">
                                <h3 class="text-sm font-bold text-slate-900">Order Items</h3>
                            </div>

                            <div v-if="items.length === 0" class="p-10 text-center text-slate-600">
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

                                    <tbody class="divide-y">
                                        <tr v-for="it in items" :key="it.id" class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-semibold text-slate-900">
                                                {{ it?.product?.name || "—" }}
                                                <div class="text-xs font-normal text-slate-500" v-if="it?.product?.sku">
                                                SKU: {{ it.product.sku }}
                                                </div>
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

                        <!-- ✅ Optional Summary from backend -->
                        <section v-if="summary" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                <p class="text-xs font-semibold text-slate-500">TOTAL QTY</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ summary.qty_total }}</p>
                                </div>
                                <div>
                                <p class="text-xs font-semibold text-slate-500">SUBTOTAL</p>
                                <p class="mt-1 font-semibold text-slate-900">৳ {{ formatMoney(summary.subtotal) }}/-</p>
                                </div>
                                <div>
                                <p class="text-xs font-semibold text-slate-500">GRAND TOTAL</p>
                                <p class="mt-1 font-bold text-slate-900">৳ {{ formatMoney(summary.grand_total) }}/-</p>
                                </div>
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
        items.value = res.data?.data?.cartitems ?? [];
        summary.value = res.data?.data?.summary ?? null;
        // console.log(order, items, summary);
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