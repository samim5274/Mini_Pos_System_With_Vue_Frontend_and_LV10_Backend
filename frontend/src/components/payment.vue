<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-semibold text-slate-800">Order Summary</h4>

                <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm text-slate-600">Total (BDT)</span>
                    <span class="text-sm font-bold text-slate-900">৳ {{ amount }}</span>
                </div>

                <p class="text-[11px] text-slate-500 mt-2">
                    Final amount is verified by server.
                </p>
            </div>
            <button
                class="w-full mt-5 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-50"
                :disabled="loading || amount <= 0"
                @click="payNow">
                {{ loading ? "Redirecting..." : "Pay Now" }}
            </button>

            <div v-if="errorMsg" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ errorMsg }}
            </div>
        </div>        
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";

const route = useRoute();
const orderId = route.params.id;

const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");
const amount = ref(0);
const regNo = ref("");

async function payNow() {
    loading.value = true;
    errorMsg.value = "";

    try {
        const res = await api.post("order/pay", {
            reg: regNo.value,
        });

        successMsg.value = res.data?.message;
        console.log(successMsg);
        console.log(res.data);

        if (res.data?.gateway_url) {
            window.location.href = res.data.gateway_url;
        }
    } catch (err) {
        console.log("PAY ERROR:", err?.response?.data); // add this
        errorMsg.value = err?.response?.data?.message || "Payment init failed";
    } finally {
        loading.value = false;
    }
}

// get Total amount
async function loadOrderTotal() {
    try {
        const res = await api.get(`/order/${orderId}/total`);
        amount.value = res.data?.total || 0;
        regNo.value  = res.data?.data?.reg || "";
    } catch (err) {
        errorMsg.value = err?.response?.data?.message || "Failed to load order total";
    }
}


onMounted(() => {
    loadOrderTotal();
});
</script>
