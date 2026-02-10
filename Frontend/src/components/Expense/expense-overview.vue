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
                            <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                            <i class="fa-solid fa-gear"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Recent Orders -->
                    <section class="xl:col-span-8 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-4 border-b flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Recent Orders</h3>
                            <a href="#" class="text-sm font-semibold text-blue-700 hover:underline">View all</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="text-left font-semibold px-4 py-3">Order</th>
                                    <th class="text-left font-semibold px-4 py-3">Customer</th>
                                    <th class="text-left font-semibold px-4 py-3">Amount</th>
                                    <th class="text-left font-semibold px-4 py-3">Status</th>
                                    <th class="text-right font-semibold px-4 py-3">Action</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">#INV-1021</td>
                                    <td class="px-4 py-3 text-slate-700">Rahim</td>
                                    <td class="px-4 py-3 text-slate-700">৳ 1,250</td>
                                    <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-green-50 border border-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                        Paid
                                    </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                    <a href="#" class="text-blue-700 font-semibold hover:underline">Details</a>
                                    </td>
                                </tr>

                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">#INV-1020</td>
                                    <td class="px-4 py-3 text-slate-700">Karim</td>
                                    <td class="px-4 py-3 text-slate-700">৳ 980</td>
                                    <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-yellow-50 border border-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-700">
                                        Due
                                    </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                    <a href="#" class="text-blue-700 font-semibold hover:underline">Details</a>
                                    </td>
                                </tr>

                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">#INV-1019</td>
                                    <td class="px-4 py-3 text-slate-700">Sadia</td>
                                    <td class="px-4 py-3 text-slate-700">৳ 2,300</td>
                                    <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">
                                        Processing
                                    </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                    <a href="#" class="text-blue-700 font-semibold hover:underline">Details</a>
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
import { useRouter } from 'vue-router'
import api from '../../services/api'
import navbar from '../navbar.vue'
import headerSection from '../header-section.vue'

const router = useRouter()

async function create() {
    router.push('/create-expense');
}

</script>