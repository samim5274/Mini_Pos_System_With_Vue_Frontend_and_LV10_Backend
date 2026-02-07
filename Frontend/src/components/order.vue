<template>
    <div class="min-h-screen bg-slate-100">

        <!-- Top Navbar -->
        <header class="bg-white border-b">
            <headerSection />
        </header>

        <!-- Page -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Sidebar/navbar -->
            <navbar />

            <!-- Main Content -->
            <main class="lg:col-span-9 space-y-6">

                <!-- Header row -->
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Order Details</h1>
                    <p class="text-sm text-slate-600">Track sales, orders, and inventory at a glance.</p>
                </div>

                <!-- Recent Orders -->
                <section class="xl:col-span-8 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">Recent Orders</h3>

                        <button
                        class="text-sm font-semibold text-blue-700 hover:underline disabled:opacity-50"
                        :disabled="loading"
                        @click="loadOrders"
                        >
                        {{ loading ? "Loading..." : "Refresh" }}
                        </button>
                    </div>

                    <!-- Loading -->
                    <div v-if="loading" class="p-10 text-center text-slate-600">
                        Loading orders...
                    </div>

                    <!-- Empty -->
                    <div v-else-if="orders.length === 0" class="p-10 text-center">
                        <p class="font-semibold text-slate-800">No orders found</p>
                        <p class="text-sm text-slate-500 mt-1">Try placing an order today.</p>
                    </div>

                    <!-- Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                            <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Order</th>
                            <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Date</th>
                            <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Amount</th>
                            <th class="text-left font-semibold px-4 py-3 whitespace-nowrap">Status</th>
                            <th class="text-right font-semibold px-4 py-3 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                {{ order.reg }}
                            </td>

                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                {{ order.date }}
                            </td>

                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                ৳ {{ formatMoney(order.total) }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                :class="statusBadge(order.status)"
                                >
                                {{ order.status }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button
                                class="text-blue-700 font-semibold hover:underline"
                                @click="goDetails(order.id)"
                                >
                                Details
                                </button>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    </section>

            </main>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

import navbar from './navbar.vue'
import headerSection from './header-section.vue'

const orders = ref([]);
const loading = ref(false);

function statusBadge(status) {
    if (status === "Paid")       return "bg-green-50 text-green-700 border-green-200";
    if (status === "Pending")    return "bg-yellow-50 text-yellow-700 border-yellow-200";
    if (status === "processing") return "bg-blue-50 text-blue-700 border-blue-200";
    if (status === "failed")     return "bg-red-50 text-red-700 border-red-200";
    if (status === "Cancelled")  return "bg-slate-50 text-slate-700 border-slate-200";
    return "bg-slate-50 text-slate-700 border-slate-200";
}

function formatMoney(v) {
    const n = Number(v || 0);
    return n.toLocaleString("en-BD");
}

function goDetails(id) {
    // তোমার details route যেভাবে আছে সেই অনুযায়ী change করো
    // router.push(`/order/${id}`);
}

async function loadOrders() {
    loading.value = true;

    try {
        const res = await api.post("/order");
        orders.value = res.data?.data || [];
    } finally {
        loading.value = false;
    }
}

onMounted(loadOrders);

</script>

<style>

</style>