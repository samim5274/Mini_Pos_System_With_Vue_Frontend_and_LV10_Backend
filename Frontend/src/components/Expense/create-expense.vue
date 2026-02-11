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
                            <h1 class="text-2xl font-bold text-slate-900">Create Expense</h1>
                            <p class="text-sm text-slate-600">Track account, Expense and finance at a glance.</p>
                        </div>
                        <div class="flex gap-2">                            
                            <button @click="router.back()" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                <i class="fa-solid fa-arrow-left-long me-2"></i> Back
                            </button>
                        </div>
                    </div>
                    <!-- Recent Orders -->
                    <section class="xl:col-span-8 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Add Expense</h2>
                                <p class="text-xs text-slate-500">Add a new expense with category & sub-category.</p>
                            </div>

                            <button
                                type="button"
                                @click="resetForm"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                >
                                Reset
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            <form @submit.prevent="submitExpense" class="space-y-5">
                                <!-- Title -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Title <span class="text-red-500">*</span></label>
                                    <input
                                    v-model="form.title"
                                    type="text" required
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="e.g. Lunch with team"
                                    />
                                </div>
                                <!-- Category + Subcategory -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-2">Category <span class="text-red-500">*</span></label>
                                        <select v-model="form.category_id" @change="onCategoryChange" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                            <option value="" selected disabled>-- Select Category --</option>
                                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Sub Category <span class="text-red-500">*</span></label>
                                    <select v-model="form.sub_category_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                        <option value="" selected disabled>-- Select Subcategory --</option>
                                        <option v-for="sub in subcategories" :key="sub.id" :value="sub.id">
                                        {{ sub.name }}
                                        </option>
                                    </select>

                                    <p v-if="form.category_id && subcategories.length === 0" class="mt-2 text-xs text-amber-600">
                                        No subcategories found for this category.
                                    </p>
                                    </div>
                                </div>
                                <!-- Amount -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Amount (৳) <span class="text-red-500">*</span></label>
                                    <input
                                        v-model="form.amount"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="0"
                                    />
                                    </div>
                                </div>
                                <!-- Remark -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-2">Remark (Optional)</label>
                                    <textarea
                                    v-model="form.remark"
                                    rows="3"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Optional note..."
                                    ></textarea>
                                </div>
                                <!-- Footer -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                                    <button
                                    type="submit"
                                    :disabled="loading"
                                    class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                                    >
                                    {{ loading ? "Saving..." : "Save Expense" }}
                                    </button>

                                    <button
                                        type="button"
                                        @click="resetForm"
                                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                        >
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </main>

            </div>
        </div>
    </div>

</template>

<script setup>
import { useRouter } from 'vue-router';
import { onMounted, ref, reactive } from 'vue';
import api from '../../services/api';

import navbar from '../navbar.vue';
import headerSection from '../header-section.vue';
import Message from '../message.vue'

const router = useRouter();

const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");
const categories = ref([]);
const subcategories = ref([]);

const form = reactive({
    category_id: "",
    sub_category_id: "",
    title: "",
    date: "",
    amount: "",
    remark: ""
});

// form clear
function resetForm() {
    Object.assign(form, {
        category_id: "",
        sub_category_id: "",
        title: "",
        amount: "",
        remark: ""
    })

    subcategories.value = [];
}

// get sub category
async function onCategoryChange() {
    try{
        form.sub_category_id = "" 
        subcategories.value = []
        const id = form.category_id;
        const res = await api.get(`/expense/get-subcategory/${id}`);
        subcategories.value = res.data.data;
    } catch (err) {
        console.log("Subcategory API error:", err?.response?.data || err)
    }
}

// page load
async function loadCategory(){
    loading.value = true;
    errorMsg.value = "";

    try{
        const res = await api.get('/expense');
        categories.value = res.data.data.categories;
        // console.log("Categories:", categories.value);
    } catch (err){        
        errorMsg.value = err?.response?.data?.message || "Failed to load data";
    } finally{
        loading.value = false;
    }
}

// create expense
async function submitExpense() {
    try{
        loading.value = true;
        errorMsg.value = "";
        successMsg.value = "";

        const payload = {
            category_id: form.category_id,
            sub_category_id: form.sub_category_id,
            title: form.title,
            amount: form.amount,
            remark: (form.remark || "").trim(),
        }

        console.log("Sending:", payload)
        
        const res = await api.post('/expense/create', payload);
        console.log(res.data?.message);
        successMsg.value = res.data?.message || "Expense added successfully";
        resetForm();
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

onMounted(loadCategory);

</script>