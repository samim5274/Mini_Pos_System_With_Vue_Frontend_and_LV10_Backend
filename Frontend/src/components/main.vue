<template>
    <main class="lg:col-span-9 space-y-6">

        <!-- Header row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Overview</h1>
            <p class="text-sm text-slate-600">Track sales, orders, and inventory at a glance.</p>
        </div>

        <div class="flex gap-2">
            <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Export
            </button>
            <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
            Create Order
            </button>
        </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500">Today Sales</p>
            <div class="mt-2 flex items-end justify-between">
            <h2 class="text-2xl font-bold text-slate-900">৳ 12,450</h2>
            <span class="text-xs font-semibold text-green-600 bg-green-50 border border-green-100 px-2 py-1 rounded-lg">+12%</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Compared to yesterday</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500">Orders</p>
            <div class="mt-2 flex items-end justify-between">
            <h2 class="text-2xl font-bold text-slate-900">38</h2>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 px-2 py-1 rounded-lg">+5</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">New orders today</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500">Customers</p>
            <div class="mt-2 flex items-end justify-between">
            <h2 class="text-2xl font-bold text-slate-900">124</h2>
            <span class="text-xs font-semibold text-purple-600 bg-purple-50 border border-purple-100 px-2 py-1 rounded-lg">+9</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Active this week</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-500">Low Stock</p>
            <div class="mt-2 flex items-end justify-between">
            <h2 class="text-2xl font-bold text-slate-900">7</h2>
            <span class="text-xs font-semibold text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded-lg">Alert</span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Need restock soon</p>
        </div>
        </div>

        <!-- Grid: Table + Right panel -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

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

        <!-- Right Panel -->
        <section class="xl:col-span-4 space-y-4">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-slate-900">Quick Actions</h3>
            <div class="mt-3 space-y-2">
                <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Add New Product</button>
                <button class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Create Invoice</button>
                <button class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View Report</button>
            </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-slate-900">Low Stock Items</h3>
            <div class="mt-3 space-y-3">
                <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Paracetamol</p>
                    <p class="text-xs text-slate-500">Remaining: 4</p>
                </div>
                <span class="text-xs font-semibold text-red-700 bg-red-50 border border-red-100 px-2 py-1 rounded-lg">Restock</span>
                </div>
                <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Vitamin C</p>
                    <p class="text-xs text-slate-500">Remaining: 6</p>
                </div>
                <span class="text-xs font-semibold text-red-700 bg-red-50 border border-red-100 px-2 py-1 rounded-lg">Restock</span>
                </div>
                <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Hand Sanitizer</p>
                    <p class="text-xs text-slate-500">Remaining: 3</p>
                </div>
                <span class="text-xs font-semibold text-red-700 bg-red-50 border border-red-100 px-2 py-1 rounded-lg">Restock</span>
                </div>
            </div>
            </div>
        </section>

        </div>
    </main>
</template>

<script></script>

<style></style>