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
                    <h1 class="text-2xl font-bold text-slate-900">Order List</h1>
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
                                <button
                                class="text-blue-700 font-semibold hover:underline"
                                @click="goDetails(order.id)"
                                >
                                INV-{{ order.reg }}
                                </button>
                            </td>

                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                {{ formatDate(order.date) }}
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
                                <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>

                    <!-- paginate -->
                    <div
                        v-if="lastPage > 1"
                        class="flex flex-col gap-2 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500">
                            Showing
                            <span class="font-semibold text-slate-700">{{ fromItem }}</span>
                            –
                            <span class="font-semibold text-slate-700">{{ toItem }}</span>
                            of
                            <span class="font-semibold text-slate-700">{{ total }}</span>
                        </p>

                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <!-- First -->
                            <button
                            @click="fetchOrders(1)"
                            :disabled="currentPage === 1 || loading"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                            >
                            First
                            </button>

                            <!-- Prev -->
                            <button
                            @click="fetchOrders(Math.max(1, currentPage - 1))"
                            :disabled="currentPage === 1 || loading"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                            >
                            Prev
                            </button>

                            <!-- Pages -->
                            <button
                            v-for="page in visiblePages"
                            :key="String(page)"
                            :disabled="page === '...' || loading"
                            @click="page !== '...' && fetchOrders(page)"
                            class="rounded-lg border px-3 py-1.5 text-xs font-semibold"
                            :class="[
                                page === '...'
                                ? 'border-slate-200 bg-white text-slate-400 cursor-default'
                                : currentPage === page
                                    ? 'border-slate-900 bg-slate-900 text-white'
                                    : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                            ]"
                            >
                            {{ page }}
                            </button>

                            <!-- Next -->
                            <button
                            @click="fetchOrders(Math.min(lastPage, currentPage + 1))"
                            :disabled="currentPage === lastPage || loading"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                            >
                            Next
                            </button>

                            <!-- Last -->
                            <button
                            @click="fetchOrders(lastPage)"
                            :disabled="currentPage === lastPage || loading"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                            >
                            Last
                            </button>
                        </div>
                    </div>

                </section>

            </main>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../services/api";

const router = useRouter();
const route = useRoute();

import navbar from './navbar.vue'
import headerSection from './header-section.vue'

const orders = ref([]);
const loading = ref(false);

// pagination meta
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(15);

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
    router.push(`/order/${id}`);
}

// visible pages (1 left + current + 1 right)
const visiblePages = computed(() => {
    const pages = [];
    const last = lastPage.value;
    const cur = currentPage.value;

    if (last <= 5) {
        for (let i = 1; i <= last; i++) pages.push(i);
        return pages;
    }

    pages.push(1);
    if (cur > 3) pages.push("...");

    const start = Math.max(2, cur - 1);
    const end = Math.min(last - 1, cur + 1);
    for (let i = start; i <= end; i++) pages.push(i);

    if (cur < last - 2) pages.push("...");
    pages.push(last);
    return pages;
});

// fetch orders by page
async function fetchOrders(page = 1) {
    if (page < 1) page = 1;
    if (page > lastPage.value) page = lastPage.value;

    loading.value = true;
    try {
        const res = await api.get(`/order?page=${page}`);

        const paginated = res.data?.data ?? res.data; // Laravel paginator object
        orders.value = paginated?.data || [];

        currentPage.value = paginated?.current_page ?? page;
        lastPage.value = paginated?.last_page ?? 1;
        total.value = paginated?.total ?? 0;
        perPage.value = paginated?.per_page ?? 15;

        router.replace({ query: { ...route.query, page: currentPage.value } });
    } finally {
        loading.value = false;
    }
}

function loadOrders() {
    fetchOrders(currentPage.value);
}

const fromItem = computed(() => {
    if (!total.value || total.value === 0) return 0;
    return (currentPage.value - 1) * perPage.value + 1;
});

const toItem = computed(() => {
    return Math.min(currentPage.value * perPage.value, total.value);
});

function formatDate(dateStr) {
    if (!dateStr) return "-";

    const d = new Date(dateStr);

    return d.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
}

onMounted(() => {
    const page = Number(route.query.page) || 1;
    fetchOrders(page);
});

</script>

<style>

</style>