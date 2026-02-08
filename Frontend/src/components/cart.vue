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

                    <!-- Quick Add (Cart Top) -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">

                            <!-- Left title -->
                            <div class="lg:w-52">
                                <h3 class="text-sm font-semibold text-slate-800">Quick Add</h3>
                                <p class="text-xs text-slate-500">Scan barcode / search product</p>
                            </div>

                            <form @keyup.enter.prevent="addCartForm" class="space-y-5">

                                <!-- Right: Input group -->
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                
                                    <!-- Search input -->
                                    <div class="relative flex-1 sm:w-80">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        🔎
                                        </span>

                                        <input
                                            type="text"
                                            ref="quickAddInput"
                                            v-model="form.inputSearch"                                            
                                            placeholder="Scan barcode / SKU / search product..."
                                            class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-200 bg-slate-50"
                                            />
                                    </div>

                                    <!-- Qty -->
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-500">Qty</span>
                                        <input
                                            v-model.number="form.qty"
                                            type="number"                                        
                                            min="1"
                                            value="1"
                                            class="w-20 h-11 text-center rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800
                                                    outline-none focus:border-blue-300 focus:ring-2 focus:ring-blue-100"
                                            />
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                    
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

                                <Transition name="toast">
                                    <div
                                        v-if="successMsg"
                                        class="fixed top-5 right-5 z-[9999] w-[320px] rounded-2xl border border-green-200 bg-white shadow-lg">
                                        <div class="flex gap-3 p-4">
                                        <!-- icon -->
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-700">
                                            <!-- ✓ -->
                                            <i class="fa-regular fa-circle-check"></i>
                                        </div>

                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-900">Success</p>
                                            <p class="mt-0.5 text-sm text-slate-600">
                                            {{ successMsg }}
                                            </p>
                                        </div>

                                        <!-- close -->
                                        <button
                                            class="ml-2 rounded-lg px-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                            @click="successMsg = ''"
                                            aria-label="Close">
                                            ✕
                                        </button>
                                        </div>

                                        <!-- progress bar -->
                                        <div class="h-1 w-full overflow-hidden rounded-b-2xl bg-green-50">
                                        <div class="toast-bar h-full bg-green-500"></div>
                                        </div>
                                    </div>
                                </Transition>
                                <Transition name="toast">
                                    <div
                                        v-if="errorMsg"
                                        class="fixed top-5 right-5 z-[9999] w-[320px] rounded-2xl border border-red-200 bg-white shadow-lg">
                                        <div class="flex gap-3 p-4">
                                        <!-- icon -->
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-700">
                                            <i class="fa-solid fa-x"></i>
                                        </div>

                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-900">Failed</p>
                                            <p class="mt-0.5 text-sm text-slate-600">
                                            {{ errorMsg }}
                                            </p>
                                        </div>

                                        <!-- close -->
                                        <button
                                            class="ml-2 rounded-lg px-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                                            @click="errorMsg = ''"
                                            aria-label="Close">
                                            ✕
                                        </button>
                                        </div>

                                        <!-- progress bar -->
                                        <div class="h-1 w-full overflow-hidden rounded-b-2xl bg-red-50">
                                        <div class="toast-bar h-full bg-red-500"></div>
                                        </div>
                                    </div>
                                </Transition>

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
                                                            @click="decreaseQty(item)"
                                                            aria-label="Decrease quantity"
                                                        >
                                                            <span class="text-lg leading-none">−</span>
                                                        </button>

                                                        <!-- Input -->
                                                        <input
                                                            type="number"
                                                            min="1"
                                                            v-model.number="item.quantity"
                                                            @input="queueQtyUpdate(item)"
                                                            class="h-9 w-16 text-center text-sm font-semibold text-slate-800 outline-none border-x border-slate-200 bg-white focus:bg-slate-50"
                                                        />

                                                        <!-- Plus -->
                                                        <button
                                                            type="button"
                                                            class="h-9 w-9 grid place-items-center text-slate-700 hover:bg-slate-50 active:bg-slate-100"
                                                            @click="increaseQty(item)"
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
                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 h-fit">

                                <!-- Header -->
                                <div class="flex items-start justify-between">
                                    <div>
                                    <h2 class="text-lg font-bold text-slate-900">Order Summary</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ itemCount }} items</p>
                                    </div>

                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    INV-{{ carts?.[0]?.reg || '—' }}
                                    </span>
                                </div>

                                <!-- Totals -->
                                <div class="mt-5 space-y-3 text-sm">
                                    <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Subtotal</span>
                                    <span class="font-semibold text-slate-900">৳ {{ subTotal.toFixed(2) }}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Discount</span>
                                    <span class="font-semibold text-slate-900">- ৳ {{ discountAmount.toFixed(2) }}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                    <span class="text-slate-600">VAT</span>
                                    <span class="font-semibold text-slate-900">৳ {{ vatAmount.toFixed(2) }}</span>
                                    </div>

                                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                                    <span class="text-slate-900 font-bold">Total</span>
                                    <span class="text-slate-900 font-extrabold text-xl">৳ {{ total.toFixed(2) }}</span>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-semibold text-slate-600 mb-3">Adjustments</p>

                                    <div class="space-y-3">
                                    <!-- VAT (%) -->
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="text-sm font-medium text-slate-700">VAT</label>
                                        <div class="relative w-36">
                                        <input
                                            v-model.number="vatRate"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0"
                                            class="w-full h-10 rounded-xl border border-slate-200 bg-white px-3 pr-8 text-right font-semibold text-slate-900 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
                                        />
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                                        </div>
                                    </div>

                                    <!-- Discount (BDT) -->
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="text-sm font-medium text-slate-700">Discount</label>
                                        <div class="relative w-36">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">%</span>
                                        <input
                                            v-model.number="discount"
                                            type="number"
                                            min="0"
                                            placeholder="0"
                                            class="w-full h-10 rounded-xl border border-slate-200 bg-white pl-7 pr-3 text-right font-semibold text-slate-900 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
                                        />
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="text-sm font-medium text-slate-700">Payment Method</label>
                                        <select
                                        v-model="paymentMethod"
                                        class="h-10 w-36 rounded-xl border border-slate-200 bg-white px-3 font-semibold text-slate-900 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
                                        >
                                        <option value="cash">Cash</option>
                                        <option value="bkash">Bkash</option>
                                        <option value="nagad">Nagad</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>

                                <!-- Cash / Return -->
                                <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold text-slate-600 mb-3">Cash Handling</p>

                                    <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-slate-500">Received (৳)</label>
                                        <input
                                        v-model.number="paidAmount"
                                        type="number"
                                        min="0"
                                        placeholder="0"
                                        class="mt-1 w-full h-10 rounded-xl border border-slate-200 bg-slate-50 px-3 text-right font-semibold text-slate-900 outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-300"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs text-slate-500">Return (৳)</label>
                                        <input
                                        :value="returnAmount.toFixed(2)"
                                        type="text"
                                        readonly
                                        class="mt-1 w-full h-10 rounded-xl border border-slate-200 bg-slate-100 px-3 text-right font-bold text-slate-900"
                                        />
                                    </div>
                                    </div>

                                    <div class="flex items-center mt-3 justify-between rounded-xl bg-red-50 border border-red-200 px-3 py-2">
                                        <span class="text-red-700 font-medium">Due</span>
                                        <span class="font-bold text-red-700">৳ {{ dueAmount.toFixed(2) }}</span>
                                    </div>
                                </div>

                                <!-- Checkout -->
                                <button
                                    class="w-full mt-5 px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="loading || carts.length === 0"
                                    @click="checkOut"
                                >
                                    Checkout
                                </button>

                                <p class="text-xs text-slate-500 mt-3">
                                    By placing your order, you agree to our terms &amp; conditions.
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
import { ref, reactive, onMounted, computed, nextTick } from "vue";
import api,  { makeImg } from "../services/api";
import { useRouter } from "vue-router"
import { useCartStore } from "../stores/cartStore";
const cartStore = useCartStore();

import Navbar from "./navbar.vue";
import HeaderSection from "./header-section.vue";

const router = useRouter()

const carts = ref([]);
const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");
const quickAddInput = ref(null);
const qtyTimers = reactive({});

// auto focus input
const focusInput = async () => {
    await nextTick();
    quickAddInput.value?.focus();
};


//fetch cart item
async function fetchCartItems() {
    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.get("/cart");
        carts.value = res.data?.data || [];
        await cartStore.fetchCart();
        // console.log("carts:", carts.value);
        // console.log("items length:", carts.value.length);
        // console.log("token:", localStorage.getItem("token"));
        if(carts.value.length > 0){
            localStorage.setItem("reg", carts.value[0].reg);
        }
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

// remove item form cart
async function removeItem(item) {
    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.delete(`/cart/remove-item/${item.reg}/${item.product_id}`);
        // console.log(res.data?.message);
        successMsg.value = res.data?.message || "Removed";
        await fetchCartItems();
        carts.value = carts.value.filter(i => i.product_id !== item.product_id);
        await cartStore.fetchCart();
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

const form = reactive({
    inputSearch: "",
    qty: 1
});

// add to cart using input from or scan
async function addCartForm() {
    if(!form.inputSearch) return;

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const payload = {
            search: form.inputSearch,
            qty: form.qty,
        }

        const res = await api.post("/cart/add", payload);
        successMsg.value = res.data?.message || "Added to cart";
        // refresh cart
        await fetchCartItems();
        // clear
        form.inputSearch = "";
        form.qty = 1;

        // focus again
        focusInput();
    } catch(err){
        showError(errorMsg.value = "Failed to add product");
        form.inputSearch = "";
    } finally {
        loading.value = false;
    }
}

// computed
const isEmpty = computed(() => !loading.value && carts.value.length === 0 );
const itemCount = computed(() => carts.value.length);

const discount = ref(0);     // %
const vatRate = ref(0);      // %
const paymentMethod = ref("cash");
const paidAmount = ref(0);

const subTotal = computed(() => {
    return carts.value.reduce((sum, item) => {
        const qty = Number(item.quantity || item.qty || 1);
        const price = Number(item.product?.price || item.price || 0);
        return sum + qty * price;
    }, 0);
});

// discount %
const discountAmount = computed(() => {
    const percent = Math.max(Number(discount.value || 0), 0) / 100;
    return subTotal.value * percent;
});

// VAT %
const vatAmount = computed(() => {
    const base = Math.max(subTotal.value - discountAmount.value, 0);
    const percent = Math.max(Number(vatRate.value || 0), 0) / 100;
    return base * percent;
});

const total = computed(() => {
    return Math.max(subTotal.value - discountAmount.value, 0) + vatAmount.value;
});

const returnAmount = computed(() => {
    return Math.max(Number(paidAmount.value || 0) - total.value, 0);
});

const dueAmount = computed(() => {
    const paid = Number(paidAmount.value || 0);
    if (paid === 0) return 0;
    return paid < total.value ? total.value - paid : 0;
});


// update stock quantity
async function increaseQty(item){
    item.quantity = Math.max(1, Number(item.quantity || 1) + 1);
    queueQtyUpdate(item);
}

async function decreaseQty(item) {
    item.quantity = Math.max(1, Number(item.quantity || 1) - 1);
    queueQtyUpdate(item);
}

async function queueQtyUpdate(item){
    const key = `${item.reg}_${item.product_id}`;

    if(qtyTimers[key]) clearTimeout(qtyTimers[key]);

    qtyTimers[key] = setTimeout(() => {
        updateQty(item);
    }, 400);
}

async function updateQty(item){
    try{
        const res = await api.post(`/cart/qty-update/${item.reg}/${item.product_id}`, {
            quantity: Number(item.quantity),
        });
        if (res?.data?.success) {
            item.quantity = Number(res.data.quantity); // sync
            item.available_stock = res.data.stock;
        }
        await cartStore.fetchCart();
    } catch (err){
        await fetchCartItems();
        const msg = err?.response?.data?.message || "Out of stock.";
        errorMsg.value = msg;
        setTimeout(() => {
            errorMsg.value = "";
        }, 1000);
    }
}

// show message pop up
function showSuccess(msg) {
    successMsg.value = msg;

    // auto hide
    setTimeout(() => {
        successMsg.value = "";
    }, 2500);
}

function showError(msg) {
    errorMsg.value = msg;

    // auto hide
    setTimeout(() => {
        errorMsg.value = "";
    }, 2500);
}

// check out or confirm order
async function  checkOut() {
    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const reg = localStorage.getItem('reg');

        if(!reg || carts.value.length === 0){
            errorMsg.value = "Cart is empty.";
            return;
        }

        const payload = {
            reg,
            discount: Number(discount.value || 0),
            vat_rate: Number(vatRate.value || 0),
            payment_method: paymentMethod.value,
            received_amount: paymentMethod.value === "cash" ? Number(paidAmount.value || 0) : 0,
        }

        const res = await api.post("/order/confirm", {payload});
        successMsg.value = res.data?.message || "Order confirm successfully.";
        showSuccess(successMsg.value);
        // console.log("API:", res.data);
        // console.log("Message:", successMsg.value);

        const win = window.open("about:blank", "_blank");
        if(!win){
            alert("Popup blocked! Allow popups.");
            return;
        }
        
        win.location.href = `/order/invoice-print/${res.data.data.reg}`;

        await refreshCartOnly();
    } catch (err) {
        errorMsg.value = err?.response?.data?.message || "Order failed";
        showError(errorMsg.value = "Order failed");
    } finally {
        loading.value = false;
    }
}

async function refreshCartOnly(){
    const res = await api.get("/cart");
    carts.value = res.data?.data || [];
    await cartStore.fetchCart();
}

onMounted(() => {
    fetchCartItems();
    focusInput();
    cartStore.fetchCart();
});

</script>

<style>
/* Animation */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.25s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.98);
}

/* Progress bar animation */
.toast-bar {
    width: 100%;
    animation: shrink 2.5s linear forwards;
}
@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}
</style>