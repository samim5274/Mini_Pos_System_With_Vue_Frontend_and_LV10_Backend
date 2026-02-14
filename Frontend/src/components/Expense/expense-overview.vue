<template>
    <!-- Dashboard (Tailwind only, No JS) -->
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
                    <Message
                        :successMsg="successMsg"
                        :errorMsg="errorMsg"
                        @update:successMsg="successMsg = $event"
                        @update:errorMsg="errorMsg = $event"
                    />
                    <!-- Header row -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">Expense Overview</h1>
                            <p class="text-sm text-slate-600">Track account, Expense and finance at a glance.</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="create" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <i class="fa-regular fa-pen-to-square me-1"></i> Create
                            </button>
                            <button @click="setting" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                            <i class="fa-solid fa-gear"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Recent Orders -->
                    <section class="xl:col-span-8 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-4 border-b flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Recent Orders</h3>
                            <button 
                                @click="fetchExpense(currentPage)" :class="{ 'opacity-60 pointer-events-none': loading }"
                                class="text-sm font-semibold text-blue-700 hover:underline">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <!-- Top bar (optional) -->
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">Expense List</p>
                                <p class="text-xs text-slate-500">
                                Total: <span class="font-semibold text-slate-700">{{ total }}</span>
                                </p>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <!-- Header -->
                                    <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600">
                                        <tr class="border-b [&>th]:px-4 [&>th]:py-3 [&>th]:text-xs [&>th]:font-semibold [&>th]:uppercase [&>th]:tracking-wide">
                                        <th class="text-left w-[140px]">Date</th>
                                        <th class="text-left">Details</th>
                                        <th class="text-right w-[120px]">Amount</th>
                                        <th class="text-right w-[90px]">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100">
                                        <!-- Empty -->
                                        <tr v-if="!expenseDetails || expenseDetails.length === 0">
                                        <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">
                                            No expenses found.
                                        </td>
                                        </tr>

                                        <!-- Rows -->
                                        <tr
                                        v-for="(val, idx) in expenseDetails"
                                        :key="val.id"
                                        class="transition hover:bg-slate-50"
                                        :class="idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/40'"
                                        >
                                        <!-- DATE -->
                                        <td class="px-4 py-3 whitespace-nowrap text-slate-800 font-medium">
                                            {{ formatDate(val.date) }}
                                        </td>

                                        <!-- DETAILS (clean hierarchy) -->
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-slate-900">
                                            {{ val?.title || '-' }}
                                            </p>

                                            <p class="text-xs text-slate-500 mt-1">
                                            {{ val?.category?.name || '-' }}
                                            <span class="mx-1">•</span>
                                            {{ val?.subcategory?.name || '-' }}
                                            </p>

                                            <p
                                            v-if="val?.remark"
                                            class="text-xs text-slate-400 italic mt-0.5 truncate max-w-[420px]"
                                            >
                                            {{ val.remark }}
                                            </p>
                                        </td>

                                        <!-- AMOUNT -->
                                        <td class="px-4 py-3 text-right font-bold text-slate-900 whitespace-nowrap">
                                            ৳ {{ Number(val?.amount || 0).toLocaleString() }}
                                        </td>

                                        <!-- ACTION -->
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <button
                                                type="button"
                                                @click="PrintExpense(val.id)"                                                
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900"
                                                >
                                                <i class="fa-solid fa-print"></i>
                                            </button>
                                            <button
                                                type="button"
                                                @click="openExpense(val.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900"
                                                >
                                                <i class="fa-solid fa-sliders"></i>
                                                <!-- <i class="fa-solid fa-angle-right text-xs"></i> -->
                                            </button>                                            
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pegination section -->
                            <div class="flex flex-col gap-2 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
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
                                    @click="fetchExpense(1)"
                                    :disabled="currentPage === 1 || loading"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                    >
                                    <i class="fa-solid fa-angles-left"></i>
                                    </button>

                                    <!-- Prev -->
                                    <button
                                    @click="fetchExpense(Math.max(1, currentPage - 1))"
                                    :disabled="currentPage === 1 || loading"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                    >
                                    <i class="fa-solid fa-chevron-left"></i>
                                    </button>

                                    <!-- Pages -->
                                    <button
                                    v-for="page in visiblePages"
                                    :key="String(page)"
                                    :disabled="page === '...' || loading"
                                    @click="page !== '...' && fetchExpense(page)"
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
                                    @click="fetchExpense(Math.min(lastPage, currentPage + 1))"
                                    :disabled="currentPage === lastPage || loading"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                    >
                                    <i class="fa-solid fa-angle-right"></i>
                                    </button>

                                    <!-- Last -->
                                    <button
                                    @click="fetchExpense(lastPage)"
                                    :disabled="currentPage === lastPage || loading"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                    >
                                    <i class="fa-solid fa-angles-right"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </section>
                </main>

            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from 'vue-router'
import api from '../../services/api'

import navbar from '../navbar.vue'
import headerSection from '../header-section.vue'
import Message from '../message.vue'

const router = useRouter();
const route = useRoute()

const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");
const expenseDetails = ref([]); 

async function create() {
    router.push('/create-expense');
}

async function setting() {
    router.push('/expense-setting');
}

// paginate section 
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0)
const perPage = ref(15)

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


// fetch expense details
async function fetchExpense(page = 1) {
    try{
        loading.value = true;
        errorMsg.value = "";

        const res = await api.get(`/expense?page=${page}`);

        const paginated = res.data?.data?.expenseDetails; 
        expenseDetails.value = paginated?.data || [];

        currentPage.value = paginated?.current_page ?? page;
        lastPage.value = paginated?.last_page ?? 1;

        total.value = paginated?.total ?? 0;
        perPage.value = paginated?.per_page ?? 15;

        router.replace({ query: { ...route.query, page: currentPage.value } });
        // console.log(expenseDetails.value);
    } catch (err){        
        const msg =
            err?.response?.data?.message ||
            Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
            "Failed";

        errorMsg.value = msg;
    } finally{
        loading.value = false;
    }
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
        month: "short",
        year: "numeric",
    });
}

function openExpense(id) {
    router.push(`/expense-details/${id}`);
}

function PrintExpense(id){
    if (!id) return;
    
    const win = window.open("about:blank", "_blank");

    if (!win) {
        alert("Popup blocked!");
        return;
    }

    const url = `/expense-print/${id}`;
    console.log("button clicked", url);

    win.location.href = url;
    win.focus();
}

onMounted(() => {
    const page = Number(route.query.page) || 1
    fetchExpense(page)
});

</script>