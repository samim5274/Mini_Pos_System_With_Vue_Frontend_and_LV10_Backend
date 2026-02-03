<template>
    <!-- Dashboard (Tailwind only, No JS) -->
    <div class="min-h-screen bg-slate-100">

        <!-- Top Navbar -->
        <header class="bg-white border-b">
            <HeaderSection />
        </header>

        <!-- Page -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Sidebar/navbar -->
                <Navbar />

                <!-- Main Content -->
                <main class="lg:col-span-9 space-y-6">
                    <!-- Header row -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">Products Details</h1>
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

                    <div class="xl:grid-cols-12 gap-6">

                        <!-- Recent Orders -->
                        <section class="xl:col-span-8 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <!-- Header -->
                                    <thead class="bg-slate-50 text-slate-600">
                                        <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Image</th>
                                        <th class="px-4 py-3 text-left font-semibold">Name</th>
                                        <th class="px-4 py-3 text-left font-semibold">Stock</th>
                                        <th class="px-4 py-3 text-left font-semibold">Price</th>
                                        <th class="px-4 py-3 text-right font-semibold">Action</th>
                                        </tr>
                                    </thead>

                                    <!-- Body -->
                                    <tbody class="divide-y">

                                        <!-- Loading -->
                                        <tr v-if="loading">
                                            <td colspan="7" class="px-4 py-6 text-center text-slate-600">
                                            Loading...
                                            </td>
                                        </tr>

                                        <!-- Error -->
                                        <tr v-else-if="errorMsg">
                                            <td colspan="7" class="px-4 py-6 text-center text-red-600">
                                            {{ errorMsg }}
                                            </td>
                                        </tr>

                                        <!-- Empty -->
                                        <tr v-else-if="products.length === 0">
                                            <td colspan="7" class="px-4 py-6 text-center text-slate-600">
                                            No products found
                                            </td>
                                        </tr>

                                        <tr
                                            v-else
                                            v-for="product in products"
                                            :key="product.id"
                                            class="hover:bg-slate-50"
                                        >
                                            <!-- Image -->
                                            <td class="px-4 py-3">
                                            <img
                                                :src="product.image ? product.image : 'https://e7.pngegg.com/pngimages/399/825/png-clipart-microcontroller-electronics-product-engineering-embedded-system-design-electronics-engineering-thumbnail.png'"
                                                class="w-10 h-10 rounded object-cover border"
                                            />
                                            </td>

                                            <td class="px-4 py-3 font-semibold text-slate-900">
                                                {{ product.name }} 
                                                <span
                                                    v-if="product.stock_quantity <= product.min_stock"
                                                    class="inline-flex items-center rounded-full bg-red-50 border border-red-100 px-2 py-1 text-xs font-semibold text-red-700"
                                                >
                                                    Low Stock
                                                </span>
                                                <span
                                                    v-else
                                                    class="inline-flex items-center rounded-full bg-green-50 border border-green-100 px-2 py-1 text-xs font-semibold text-green-700"
                                                >
                                                    In Stock
                                                </span>
                                                <br> 
                                                <span class="text-sm text-slate-700">{{ product.sku }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-700">{{ product.stock_quantity }}</td>
                                            <td class="px-4 py-3 text-slate-700">৳ {{ product.price }}</td>

                                            <td class="px-4 py-3 text-right space-x-2">
                                            <button
                                                @click="editProduct(product)"
                                                class="text-blue-600 font-semibold hover:underline"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="deleteProduct(product.id)"
                                                class="text-red-600 font-semibold hover:underline"
                                            >
                                                Delete
                                            </button>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="flex items-center justify-between px-4 py-4 border-t bg-white">

                                <button
                                    @click="fetchProducts(currentPage-1)"
                                    :disabled="currentPage === 1"
                                    class="px-3 py-1 rounded border bg-slate-100 disabled:opacity-40">
                                    Prev
                                </button>

                                <div class="flex gap-2">
                                    <button
                                    v-for="page in lastPage"
                                    :key="page"
                                    @click="fetchProducts(page)"
                                    :class="[
                                        'px-3 py-1 rounded border',
                                        currentPage === page
                                        ? 'bg-slate-900 text-white'
                                        : 'bg-white hover:bg-slate-100'
                                    ]"
                                    >
                                    {{ page }}
                                    </button>
                                </div>

                                <button
                                    @click="fetchProducts(currentPage+1)"
                                    :disabled="currentPage === lastPage"
                                    class="px-3 py-1 rounded border bg-slate-100 disabled:opacity-40">
                                    Next
                                </button>
<div class="text-xs text-slate-500">
  total: {{ total }} | per: {{ perPage }} | last: {{ lastPage }}
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
import api from "../services/api";
import { useRoute, useRouter } from "vue-router"

import Navbar from "./navbar.vue";
import HeaderSection from "./header-section.vue";

const route = useRoute()
const router = useRouter()

const products = ref([]);
const loading = ref(false);
const errorMsg = ref("");

const currentPage = ref(1);
const lastPage = ref(1);

const total = ref(0)
const perPage = ref(5)

async function fetchProducts(page = 1) {
    loading.value = true;
    errorMsg.value = "";

    try{
        const res = await api.get(`/products?page=${page}`)
        
        // const res = await api.get("/products");
        // const payload = res.data;

        // if (Array.isArray(payload?.data)) {
        //     products.value = payload.data;
        // } else if (Array.isArray(payload)) {
        //     products.value = payload;
        // } else if (payload) {
        //     products.value = [payload];
        // } else {
        //     products.value = [];
        // }

        products.value = Array.isArray(res?.data?.data) ? res.data.data : [];

        currentPage.value = Math.min(res?.data?.current_page ?? page, res?.data?.last_page ?? 1)
        lastPage.value = res?.data?.last_page ?? 1

        total.value = res?.data?.total ?? 0
        perPage.value = res?.data?.per_page ?? 5

        router.replace({ query: { page: currentPage.value } });

        console.log("PAGINATE RES:", res.data);

    }  catch (err) {
        console.log("ERR:", err);
        errorMsg.value =
        err?.response?.data?.message ||
        JSON.stringify(err?.response?.data || {}) ||
        err.message ||
        "Products load korte problem hocche.";
    } finally {
        loading.value = false;
    }
}

// onMounted(fetchProducts);
onMounted(() => {
    const page = Number(route.query.page) || 1
    fetchProducts(page)
})
</script>

