<template>
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
                            <h1 class="text-2xl font-bold text-slate-900">Expense Setting</h1>
                            <p class="text-sm text-slate-600">Manage category and sub-category details</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="router.back()" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                <i class="fa-solid fa-arrow-left-long me-2"></i> Back
                            </button>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- ===== Category Section ===== -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Create Category -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                                <h2 class="text-lg font-bold text-slate-900">Create new category</h2>
                                <p class="text-sm text-slate-500 mt-1">Add a new expense category.</p>

                                <form @submit.prevent="createCategory" class="mt-5 space-y-4">
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">Category name</label>
                                        <input
                                        v-model="newCategory"
                                        type="text"
                                        placeholder="e.g. Transport"
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-200"
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                        Save Category
                                    </button>
                                </form>
                            </div>

                            <!-- Category List -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-slate-900">Category Details</h2>
                                    <span class="text-xs text-slate-500">
                                        Total: {{ category?.total ?? 0 }}
                                    </span>
                                </div>

                                <ul class="mt-4 divide-y divide-slate-100">
                                    <li
                                        v-for="cat in (category?.data || [])" :key="cat.id"
                                        class="flex items-center justify-between py-3">
                                        <span class="text-sm font-medium text-slate-800">{{ cat.name }}</span>

                                        <div class="flex gap-2">
                                            <button
                                                class="rounded-lg border border-rose-200 px-3 py-1 text-xs text-rose-700 hover:bg-rose-50"
                                                @click="openEditCategory(cat)">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button
                                                class="rounded-lg border border-rose-200 px-3 py-1 text-xs text-rose-700 hover:bg-rose-50"
                                                @click="deleteCategory(cat.id)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </li>

                                    <li v-if="(category?.data || []).length === 0" class="py-8 text-center text-sm text-slate-500">
                                        No categories found.
                                    </li>
                                </ul>

                                <!-- Category Pagination Buttons -->
                                <div class="mt-5 flex items-center justify-between">
                                    <button
                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                        :disabled="!category?.prev_page_url"
                                        @click="fetchSetting((category?.current_page ?? 1) - 1, subcategory?.current_page ?? 1)">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>

                                    <div class="text-xs text-slate-500">
                                        Page {{ category?.current_page ?? 1 }} / {{ category?.last_page ?? 1 }}
                                    </div>

                                    <button
                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                        :disabled="!category?.next_page_url"
                                        @click="fetchSetting((category?.current_page ?? 1) + 1, subcategory?.current_page ?? 1)">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>

                                <!-- popup section -->
                                <!-- Edit Category Modal -->
                                <div
                                v-if="showCategoryEditModal"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                @click.self="closeEditCategory"
                                >
                                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                                        <h2 class="text-lg font-bold text-slate-900 mb-4">Edit Category</h2>

                                        <form @submit.prevent="updateCategory" class="space-y-4">
                                        <div>
                                            <label class="text-xs font-semibold text-slate-600">Category name</label>
                                            <input
                                            v-model="editCategoryName"
                                            type="text"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-200"
                                            placeholder="e.g. Transport"
                                            />
                                        </div>

                                        <div class="flex justify-end gap-2 pt-4">
                                            <button
                                            type="button"
                                            @click="closeEditCategory"
                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50"
                                            >
                                            Cancel
                                            </button>

                                            <button
                                            type="submit"
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                                            >
                                            Update
                                            </button>
                                        </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ===== Sub Category Section ===== -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Create Subcategory -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                                <h2 class="text-lg font-bold text-slate-900">Create new sub-category</h2>
                                <p class="text-sm text-slate-500 mt-1">Add a sub-category under a category.</p>

                                <form @submit.prevent="createSubCategory" class="mt-5 space-y-4">
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">Select category</label>
                                        <select
                                        v-model="selectedCategoryId"
                                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-200"
                                        >
                                        <option value="">-- Choose --</option>
                                        <option v-for="c in (category?.data || [])" :key="c.id" :value="c.id">
                                            {{ c.name }}
                                        </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">Sub-category name</label>
                                        <input
                                        v-model="newSubCategory"
                                        type="text"
                                        placeholder="e.g. Bus Fare"
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-slate-200"
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                                    >
                                        Save Sub-Category
                                    </button>
                                </form>
                            </div>

                            <!-- Subcategory List -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-bold text-slate-900">Sub-Category Details</h2>
                                    <span class="text-xs text-slate-500">
                                        Total: {{ subcategory?.total ?? 0 }}
                                    </span>
                                    </div>

                                <ul class="mt-4 divide-y divide-slate-100">
                                    <li
                                        v-for="sub in (subcategory?.data || [])"
                                        :key="sub.id"                                    
                                        class="flex items-center justify-between py-3">
                                        <div class="space-y-0.5">
                                            <div class="text-sm font-medium text-slate-800">{{ sub.name }}</div>
                                            <div class="text-xs text-slate-500">
                                                Category: {{ sub?.category?.name ?? "-" }}
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <button
                                                class="rounded-lg border border-rose-200 px-3 py-1 text-xs text-rose-700 hover:bg-rose-50"
                                                @click="editSubCategory(sub.id)"
                                                >
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button
                                                class="rounded-lg border border-rose-200 px-3 py-1 text-xs text-rose-700 hover:bg-rose-50"
                                                @click="deleteSubCategory(sub.id)"
                                                >
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </li>

                                    <li v-if="(subcategory?.data || []).length === 0" class="py-8 text-center text-sm text-slate-500">
                                        No sub-categories found.
                                    </li>
                                </ul>

                                <!-- Subcategory Pagination Buttons -->
                                <div class="mt-5 flex items-center justify-between">
                                    <button
                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                        :disabled="!subcategory?.prev_page_url"
                                        @click="fetchSetting(category?.current_page ?? 1, (subcategory?.current_page ?? 1) - 1)">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>

                                    <div class="text-xs text-slate-500">
                                        Page {{ subcategory?.current_page ?? 1 }} / {{ subcategory?.last_page ?? 1 }}
                                    </div>

                                    <button
                                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-40"
                                        :disabled="!subcategory?.next_page_url"
                                        @click="fetchSetting(category?.current_page ?? 1, (subcategory?.current_page ?? 1) + 1)">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </button>
                                </div>

                                <!-- popup for edit sub-category -->
                                <!-- Edit SubCategory Modal -->
                                <div v-if="showEditModal" 
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

                                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                                        
                                        <h2 class="text-lg font-bold text-slate-900 mb-4">
                                        Edit Sub-Category
                                        </h2>

                                        <form @submit.prevent="updateSubCategory" class="space-y-4">
                                        
                                        <div>
                                            <label class="text-xs font-semibold text-slate-600">Category</label>
                                            <select v-model="editCategoryId"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                                            
                                            <option v-for="c in (category?.data || [])"
                                                    :key="c.id"
                                                    :value="c.id">
                                                {{ c.name }}
                                            </option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold text-slate-600">
                                            Sub-category Name
                                            </label>
                                            <input v-model="editSubCategoryName"
                                            type="text"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2 text-sm" />
                                        </div>

                                        <div class="flex justify-end gap-2 pt-4">
                                            <button type="button"
                                            @click="closeEditModal"
                                            class="rounded-xl border px-4 py-2 text-sm">
                                            Cancel
                                            </button>

                                            <button type="submit"
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm text-white">
                                            Update
                                            </button>
                                        </div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </main>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../../services/api";

import navbar from '../navbar.vue'
import headerSection from '../header-section.vue'
import Message from '../message.vue'

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");

// paginator objects
const category = ref(null);
const subcategory = ref(null);

// missing form refs
const newCategory = ref("");
const selectedCategoryId = ref("");
const newSubCategory = ref("");

// fetch
async function fetchSetting(categoryPage = 1, subcategoryPage = 1){
    // console.log("fetchSetting", { categoryPage, subcategoryPage });
    loading.value = true;
    try {
        const res = await api.get("/expense/setting", {
            params: {
                category_page: categoryPage,
                subcategory_page: subcategoryPage,
            },
        });

        category.value = res.data?.data?.categories;
        subcategory.value = res.data?.data?.subcategories;

    } catch (err){
        errorMsg.value = err?.response?.data?.message || "Failed";
    } finally {
        loading.value = false;
    }
}

async function createCategory() {
    const name = newCategory.value.trim();
    if (!name) {
        errorMsg.value = "Category name is required.";
        return;
    }

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try {
        const res = await api.post("/expense/category", { name });
        successMsg.value = res.data?.message || "Category created.";
        newCategory.value = "";
        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );
    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
        "Failed to create category.";
    } finally {
        loading.value = false;
    }
}

async function createSubCategory() {
    const name = newSubCategory.value.trim();
    if(!name){
        errorMsg.value = "Sub-categoyr name required.";
        return;
    }

    if(!selectedCategoryId.value){
        errorMsg.value = "Please select a categoyr."
        return;
    }

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.post("/expense/subcategory", {
            category_id: selectedCategoryId.value,
            name: name,
        });

        successMsg.value = res.data?.message || "Sub-category created.";
        newSubCategory.value = "";

        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );
    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
        "Failed to create category.";
    } finally {
        loading.value = false;
    }
}

async function deleteCategory(id) {
    if (!id) return;

    const ok = confirm("Are you sure you want to delete this category?");
    if (!ok) return;

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try{
        const res = await api.delete(`/expense/category/${id}`);
        successMsg.value = res.data?.message || "Category deleted successfully.";

        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );
    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
        "Failed to deleted category.";
    } finally {
        loading.value = false;
    }
}

async function deleteSubCategory(id) {
    if (!id) return;

    const ok = confirm("Are you sure you want to delete this sub-category?");
    if (!ok) return;

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try {
        const res = await api.delete(`/expense/subcategory/${id}`);
        successMsg.value = res.data?.message || "Sub-category deleted successfully.";

        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );
    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
        "Failed to deleted sub-category.";
    } finally {
        loading.value = false;
    }
}

// edit sub-category popup start
const showEditModal = ref(false);
const editSubCategoryId = ref(null);
const editSubCategoryName = ref("");

function editSubCategory(id){
    const sub = subcategory.value?.data?.find(s => s.id === id);
    if (!sub) return;

    editSubCategoryId.value = sub.id;
    editSubCategoryName.value = sub.name;
    editCategoryId.value = sub.category_id;

    showEditModal.value = true;
}

function closeEditModal(){
    showEditModal.value = false;
    editSubCategoryId.value = null;
    editSubCategoryName.value = "";
    editCategoryId.value = "";
}

// Update API call
async function updateSubCategory() {
    if (!editSubCategoryName.value.trim()) return;

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try {
        const res = await api.put(`/expense/edit/subcategory/${editSubCategoryId.value}`,
            {
                category_id: editCategoryId.value,
                name: editSubCategoryName.value.trim(),
            }
        );

        successMsg.value = res.data?.message || "Updated successfully.";
        closeEditModal();

        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );

    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        "Failed to update sub-category.";
    } finally {
        loading.value = false;
    }
}

// edit category popup start
const showCategoryEditModal = ref(false);
const editCategoryName = ref("");
const editCategoryId = ref("");

function openEditCategory(cat) {
    if (!cat) return;
    editCategoryId.value = cat.id;
    editCategoryName.value = cat.name || "";
    showCategoryEditModal.value = true;
}

function closeEditCategory() {
    showCategoryEditModal.value = false;
    editCategoryId.value = null;
    editCategoryName.value = "";
}

async function updateCategory() {
    const name = editCategoryName.value.trim();
    if (!name) {
        errorMsg.value = "Category name is required.";
        return;
    }

    loading.value = true;
    errorMsg.value = "";
    successMsg.value = "";

    try {
        const res = await api.put(`/expense/edit/category/${editCategoryId.value}`, {
            name
        });

        successMsg.value = res.data?.message || "Category updated successfully.";
        closeEditCategory();

        await fetchSetting(
            category.value?.current_page ?? 1,
            subcategory.value?.current_page ?? 1
        );

    } catch (err) {
        errorMsg.value =
        err?.response?.data?.message ||
        Object.values(err?.response?.data?.errors || {})?.[0]?.[0] ||
        "Failed to update category.";
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchSetting(1, 1);
});

</script>

<style></style>