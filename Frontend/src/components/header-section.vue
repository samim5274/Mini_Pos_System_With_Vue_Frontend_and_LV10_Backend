<template>
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Left -->
                <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold">
                    MP
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">MiniPOS Dashboard</p>
                    <p class="text-xs text-slate-500">Manage sales & inventory</p>
                </div>
                </div>

                <!-- Search -->
                <div class="hidden md:block w-full max-w-md">
                <div class="relative">
                    <input
                    type="text"
                    placeholder="Search products, orders..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 pl-10 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    />
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none">
                    <path d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                </div>

                <!-- Right -->
                <div class="flex items-center gap-3">
                <button class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    Online
                </button>

                <button class="relative rounded-xl border border-slate-200 bg-white p-2 hover:bg-slate-50">
                    <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none">
                    <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M9 17a3 3 0 006 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center">3</span>
                </button>

                <div class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2">
                    <div class="h-9 w-9 rounded-full bg-slate-200"></div>
                    <div class="hidden sm:block leading-tight">
                    <p class="text-sm font-semibold text-slate-900">Admin</p>
                    <p class="text-xs text-slate-500">Owner</p>
                    </div>
                </div>
                </div>
            </div>
            </div>
</template>

<script></script>

<style></style>