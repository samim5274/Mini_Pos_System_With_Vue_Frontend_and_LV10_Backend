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
                            <h1 class="text-2xl font-bold text-slate-900">Expense Details</h1>
                            <p class="text-sm text-slate-600">Track account, Expense and finance at a glance.</p>
                        </div>
                        <div class="flex gap-2">                            
                            <button @click="router.back()" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                <i class="fa-solid fa-arrow-left-long me-2"></i> Back
                            </button>
                        </div>
                    </div>

                    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                        <!-- Top Bar -->
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-6 py-5 bg-slate-50 border-b border-slate-200">
                            <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500 tracking-widest uppercase">Expense Details</p>

                            <h2 class="mt-1 text-lg font-bold text-slate-900 truncate">
                                {{ expenseDetails?.title || "Expense Title" }}
                            </h2>

                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                <i class="fa-regular fa-calendar"></i>
                                {{ expenseDetails?.date ? formatDate(expenseDetails.date) : "-" }}
                                </span>

                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                <i class="fa-solid fa-layer-group"></i>
                                {{ expenseDetails?.category?.name || "-" }}
                                </span>

                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                <i class="fa-solid fa-tag"></i>
                                {{ expenseDetails?.subcategory?.name || "-" }}
                                </span>
                            </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 sm:justify-end">
                            <button
                                type="button"
                                @click="printExpense(expenseDetails.id)"
                                class="h-10 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200">
                                <i class="fa-solid fa-print"></i>
                                Print
                            </button>

                            <!-- <button
                                type="button"
                                @click="editExpense()"
                                class="h-10 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </button> -->

                            <button
                                type="button"
                                @click="deleteExpense()"
                                class="h-10 inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 text-xs font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-200">
                                <i class="fa-solid fa-trash"></i>
                                Delete
                            </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-6">

                            <!-- Amount Highlight -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Amount</p>
                                    <p class="mt-1 text-sm text-slate-600">This amount is recorded in your expense book.</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-slate-500">BDT</p>
                                    <p class="text-3xl font-extrabold text-red-600 leading-none">
                                    ৳ {{ expenseDetails?.amount ?? "0.00" }}
                                    </p>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">

                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Info</p>

                                    <div class="space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-slate-500 inline-flex items-center gap-2">
                                        <i class="fa-regular fa-rectangle-list"></i> Category
                                        </span>
                                        <span class="font-semibold text-slate-800 text-right">
                                        {{ expenseDetails?.category?.name || "-" }}
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-slate-500 inline-flex items-center gap-2">
                                        <i class="fa-solid fa-tag"></i> Subcategory
                                        </span>
                                        <span class="font-semibold text-slate-800 text-right">
                                        {{ expenseDetails?.subcategory?.name || "-" }}
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-slate-500 inline-flex items-center gap-2">
                                        <i class="fa-regular fa-calendar"></i> Date
                                        </span>
                                        <span class="font-semibold text-slate-800 text-right">
                                        {{ expenseDetails?.date ? formatDate(expenseDetails.date) : "-" }}
                                        </span>
                                    </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Note</p>

                                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700 leading-relaxed min-h-[96px]">
                                    {{ expenseDetails?.remark || "No remark added." }}
                                    </div>
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
const expenseDetails = ref(null);

function formatDate(dateStr) {
    if (!dateStr) return "-";
    const d = new Date(dateStr);

    // invalid date handle
    if (isNaN(d.getTime())) return "-";

    return d.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}


async function fetchExpense(id){
    try{
        loading.value = true;
        errorMsg.value = "";

        const res = await api.get(`/expense/details/${id}`);
        expenseDetails.value = res.data?.data ?? res.data;

        // console.log("API raw:", res.data);
    } catch (err){
        const msg = err?.response?.data?.message || Object.values(err?.response?.data?.errors || {})?.[0]?.[0] || "Failed";
        errorMsg.value = msg;
    } finally{
        loading.value = false;
    }
}

// function editExpense() {
//   const id = route.params.id;
//   router.push(`/expense-edit/${id}`); // তোমার route অনুযায়ী change করো
// }

async function deleteExpense() {
    const id = route.params.id;
    if (!confirm("Are you sure you want to delete this expense?")) return;

    try {
        loading.value = true;
        await api.delete(`expense/delete/${id}`);
        successMsg.value = "Expense deleted successfully.";
        router.push("/expense"); // list page
    } catch (err) {
        errorMsg.value = err?.response?.data?.message || "Delete failed.";
    } finally {
        loading.value = false;
    }
}

function printExpense(id) {
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
    const id = route.params.id;
    if (id) fetchExpense(id);
});

</script>