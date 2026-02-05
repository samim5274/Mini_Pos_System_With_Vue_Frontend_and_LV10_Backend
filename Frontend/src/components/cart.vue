<template>
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
                    
                    <!-- cart item list -->
                     <div class="min-h-screen bg-slate-100">
                        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

                            <!-- Left: Cart Items -->
                            <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
                                <div class="p-4 border-b flex items-center justify-between">
                                    <h1 class="text-xl font-bold">Your Cart</h1>

                                    <button class="text-sm text-red-600 hover:underline">
                                        Clear Cart
                                    </button>
                                </div>

                                <div v-if="errorMsg" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    {{ errorMsg }}
                                </div>

                                <div v-if="successMsg" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                    {{ successMsg }}
                                </div>

                                <!-- Loading -->
                                <div v-if="loading" class="p-8 text-center text-slate-600">
                                    Loading cart...
                                </div>

                                <!-- Empty -->
                                <div v-else-if="isEmpty" class="p-10 text-center">
                                    <div class="text-4xl mb-2">🛒</div>
                                    <p class="text-slate-700 font-medium">Your cart is empty</p>
                                    <p class="text-sm text-slate-500 mt-1">Add products to see them here.</p>

                                    <router-link
                                        to="/products"
                                        class="inline-block mt-5 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        Browse Products
                                    </router-link>
                                </div>

                                

                                <!-- Cart Table -->
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-slate-50 border-b">
                                        <tr>
                                            <th class="p-3 text-left">Product</th>
                                            <th class="p-3 text-center w-36">Qty</th>
                                            <th class="p-3 text-right w-28">Price</th>
                                            <th class="p-3 text-right w-32">Total</th>
                                            <th class="p-3 text-right w-20">Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr
                                        v-for="item in carts" :key="item.id" 
                                        class="border-b hover:bg-slate-50">

                                            <!-- Product -->
                                            <td class="p-3">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500">
                                                        <img 
                                                        v-if="item.product?.image_url || item.product?.image" 
                                                        :src="makeImg(item.product?.image_url || item.product?.image)"
                                                        alt="" class="w-full h-full object-cover" />
                                                        <span v-else>IMG</span>
                                                    </div>

                                                    <div>
                                                    <div class="font-semibold text-slate-800 leading-5">
                                                        {{ item.product?.name }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 mt-0.5">
                                                        SKU: {{ item.product?.sku }}
                                                    </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Qty -->
                                            <td class="p-3">
                                                <div class="flex justify-center">
                                                    <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                                                    
                                                    <!-- Minus -->
                                                    <button
                                                        type="button"
                                                        class="h-9 w-9 grid place-items-center text-slate-700 hover:bg-slate-50 active:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        :disabled="Number(item.quantity) <= 1"
                                                        @click="item.quantity = Math.max(1, Number(item.quantity || 1) - 1)"
                                                        aria-label="Decrease quantity"
                                                    >
                                                        <span class="text-lg leading-none">−</span>
                                                    </button>

                                                    <!-- Input -->
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        v-model.number="item.quantity"
                                                        class="h-9 w-16 text-center text-sm font-semibold text-slate-800 outline-none border-x border-slate-200 bg-white focus:bg-slate-50"
                                                    />

                                                    <!-- Plus -->
                                                    <button
                                                        type="button"
                                                        class="h-9 w-9 grid place-items-center text-slate-700 hover:bg-slate-50 active:bg-slate-100"
                                                        @click="item.quantity = Number(item.quantity || 1) + 1"
                                                        aria-label="Increase quantity"
                                                    >
                                                        <span class="text-lg leading-none">+</span>
                                                    </button>

                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Unit price -->
                                            <td class="p-3 text-right">
                                            ৳ {{ item.product?.price || item.price || 0 }}/-
                                            </td>

                                            <!-- Total -->
                                            <td class="p-3 text-right font-semibold">
                                            ৳ {{ Number(item.quantity || item.qty || 1) *  Number(item.product?.price || item.price || 0)}}/-
                                            </td>

                                            <!-- Remove -->
                                            <td class="p-3 text-right">
                                                <button
                                                    :disabled="loading"
                                                    @click="removeItem(item)"
                                                    class="px-3 py-1.5 text-xs rounded-lg bg-red-50 text-red-700 hover:bg-red-100"
                                                    >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- Footer actions -->
                                    <div class="p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                                        <router-link
                                        to="/products"
                                        class="text-sm text-blue-600 hover:underline">
                                        ← Continue shopping
                                        </router-link>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Summary -->
                            <div class="bg-white rounded-xl shadow p-5 h-fit">
                                <h2 class="text-lg font-bold">Order Summary</h2>

                                <div class="mt-4 space-y-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-600">Subtotal</span>
                                        <span class="font-semibold">৳ {{ subTotal }}/-</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-600">Discount</span>
                                        <span class="font-semibold">৳ 00/-</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-600">VAT</span>
                                        <span class="font-semibold">৳ 00/-</span>
                                    </div>

                                    <div class="border-t pt-3 flex items-center justify-between">
                                        <span class="text-slate-700 font-bold">Total</span>
                                        <span class="text-slate-900 font-bold text-lg">৳ {{ total }}/-</span>
                                    </div>
                                </div>

                                <button
                                    class="w-full mt-5 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-50"
                                    
                                > Checkout </button>

                                <p class="text-xs text-slate-500 mt-3">
                                By placing your order, you agree to our terms & conditions.
                                </p>
                            </div>

                        </div>
                  </div>
                </main>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api,  { makeImg } from "../services/api";
import { useRouter } from "vue-router"

import Navbar from "./navbar.vue";
import HeaderSection from "./header-section.vue";

const router = useRouter()

const carts = ref([]);
const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");

async function fetchCartItems() {
    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.get("/cart");
        carts.value = res.data?.data || [];
        // console.log("carts:", carts.value);
        // console.log("items length:", carts.value.length);
        // console.log("token:", localStorage.getItem("token"));
    } catch (err){
        const status = err?.response?.status;
        if (status === 401) {
            errorMsg.value = "Please login first";
            router.push("/login");
            return;
        }
        errorMsg.value = err?.response?.data?.message || "Failed to load cart.";
    } finally {
        loading.value = false;
    }
}

async function removeItem(item) {
    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.delete(`/cart/remove-item/${item.reg}/${item.product_id}`);
        // console.log(res.data);
        successMsg.value = res.data?.message || "Removed";
        await fetchCartItems();
        carts.value = carts.value.filter(i => i.product_id !== item.product_id);
    } catch(err){
        const status = err?.response?.status;
        if (status === 401) {
            errorMsg.value = "Please login first";
            router.push("/login");
            return;
        }
        errorMsg.value = err?.response?.data?.message || "Failed to load cart.";
    } finally {
        loading.value = false;
    }
}

const isEmpty = computed(() => !loading.value && carts.value.length === 0 );

const subTotal = computed(() => {
    return carts.value.reduce((sum, item) => {
        const qty = Number(item.quantity || item.qty || 1);
        const price = Number(item.product?.price || item.price || 0);
        return sum + qty * price;
    }, 0);
});


const total = computed(() => subTotal.value);


onMounted(fetchCartItems);

</script>

<style>

</style>