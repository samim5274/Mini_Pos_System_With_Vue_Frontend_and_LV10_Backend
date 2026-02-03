<template>
    <aside class="lg:col-span-3">
        <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-4">
        <p class="text-xs font-semibold text-slate-500 mb-3">MAIN MENU</p>

        <nav class="space-y-1">
            
            <router-link
                to="/dashboard"
                class="nav-item"
                active-class="nav-active"
                >
                <span class="icon">🏠</span>
                Dashboard
            </router-link>


            <router-link
                to="/products"
                class="nav-item"
                active-class="nav-active"
                >
                <span class="icon">📦</span>
                Products
            </router-link>


            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <span class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">🧾</span>
            Orders
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <span class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">👥</span>
            Customers
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <span class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">📊</span>
            Reports
            </a>

            <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <span class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">⚙️</span>
            Settings
            </a>
            <a href="#" @click.prevent="logout" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <span class="h-9 w-9 rounded-xl bg-slate-100 flex items-center justify-center">⚙️</span>
            Logout
            </a>
        </nav>

        <div class="mt-4 border-t pt-4">
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
            <p class="text-sm font-semibold text-slate-900">Quick Tip</p>
            <p class="text-xs text-slate-600 mt-1">
                Keep low stock threshold updated to avoid out-of-stock.
            </p>
            <button class="mt-3 w-full rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Add Product
            </button>
            </div>
        </div>
        </div>
    </aside>
</template>

<script></script>

