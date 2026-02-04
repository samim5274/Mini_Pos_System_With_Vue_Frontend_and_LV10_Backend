<template>
  <div class="min-h-screen bg-slate-100">

    <!-- Top Navbar -->
    <header class="bg-white border-b">
      <HeaderSection />
    </header>

    <!-- Page -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Sidebar -->
        <Navbar />

        <!-- Main -->
        <main class="lg:col-span-9 space-y-6">

            <div class="bg-white shadow-lg rounded-2xl p-8">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h2 class="text-2xl font-bold">✏️ Edit Product</h2>
                        <p class="text-sm text-slate-500 mt-1">
                        Update product info and save changes
                        </p>
                    </div>

                    <router-link
                        to="/products"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800"
                    >
                        ← Back
                    </router-link>
                </div>

                <!-- Loading -->
                <div v-if="pageLoading" class="mt-6 p-6 rounded-xl bg-slate-50 text-slate-600">
                Loading product...
                </div>

                <!-- Form -->
                <form v-else @submit.prevent="submit" class="mt-6 space-y-5">

                    <!-- Name -->
                    <div>
                        <label class="label">Product Name</label>
                        <input v-model="form.name" class="input" placeholder="e.g Double A A4 paper" />
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="label">SKU</label>
                        <input v-model="form.sku" class="input" placeholder="e.g A4PAPER001" />
                    </div>

                    <!-- Price + Stock -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                        <label class="label">Price</label>
                        <input type="number" v-model="form.price" class="input" placeholder="e.g 450" />
                        </div>

                        <div>
                        <label class="label">Stock Quantity</label>
                        <input type="number" v-model="form.stock_quantity" class="input" placeholder="e.g 15" />
                        </div>
                    </div>

                    <!-- Min Stock -->
                    <div>
                        <label class="label">Minimum Stock Alert</label>
                        <input type="number" v-model="form.min_stock" class="input" placeholder="e.g 5" />
                    </div>

                    <!-- Current Image -->
                    <div v-if="currentImageUrl" class="rounded-2xl border bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-700 mb-2">Current Image</p>
                        <img
                        :src="currentImageUrl"
                        class="h-28 w-28 rounded-xl border object-cover"
                        />
                    </div>

                    <!-- Image Upload (Drag + Browse) -->
                    <div>
                        <label class="label">Change Image (optional)</label>

                        <div
                            class="rounded-2xl border-2 border-dashed p-5 transition bg-white"
                            :class="isDragOver ? 'border-blue-500 bg-blue-50' : 'border-slate-300'"
                            @dragover="onDragOver"
                            @dragleave="onDragLeave"
                            @drop.prevent="onDrop">

                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="text-sm text-slate-600">
                                <div class="font-semibold text-slate-800">Drag & drop an image here</div>
                                <div class="text-xs text-slate-500">or click Browse (jpg, png, webp)</div>
                                </div>

                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white cursor-pointer hover:bg-slate-800">
                                Browse
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImage"
                                />
                                </label>
                            </div>

                            <div v-if="preview" class="mt-4 flex items-center gap-4">
                                <img :src="preview" class="h-28 w-28 rounded-xl border object-cover" />

                                <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-800">{{ form.image?.name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ Math.round((form.image?.size || 0) / 1024) }} KB
                                </p>

                                <button
                                    type="button"
                                    class="mt-2 text-sm text-red-600 hover:underline"
                                    @click="removeImage"
                                >
                                    Remove selected
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                        :disabled="saving"
                        class="bg-blue-600 text-white px-5 py-2 rounded-xl hover:bg-blue-700 disabled:opacity-60"
                        >
                        {{ saving ? 'Updating...' : 'Update Product' }}
                        </button>

                        <router-link
                        to="/products"
                        class="bg-gray-200 px-5 py-2 rounded-xl"
                        >
                        Cancel
                        </router-link>
                    </div>

                </form>
            </div>

        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onBeforeUnmount, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

import Navbar from "./navbar.vue"
import HeaderSection from "./header-section.vue"

const route = useRoute()
const router = useRouter()
const id = route.params.id

const pageLoading = ref(true)
const saving = ref(false)

const errors = reactive({})
const isDragOver = ref(false)

const preview = ref(null)
const currentImageUrl = ref(null)

const form = reactive({
    name: '',
    sku: '',
    price: '',
    stock_quantity: '',
    min_stock: '',
    image: null, // new selected file
})

function clearPreviewUrl(){
    if (preview.value) URL.revokeObjectURL(preview.value)
    preview.value = null
}

function setFile(file){
    if (!file) return
    if (!file.type?.startsWith('image/')) return

    form.image = file
    clearPreviewUrl()
    preview.value = URL.createObjectURL(file)
}

function handleImage(e){
    setFile(e.target.files?.[0])
}

function onDrop(e){
    isDragOver.value = false
    setFile(e.dataTransfer?.files?.[0])
}

function onDragOver(e){
    e.preventDefault()
    isDragOver.value = true
}

function onDragLeave(){
    isDragOver.value = false
}

function removeImage(){
    form.image = null
    clearPreviewUrl()
}

onBeforeUnmount(() => clearPreviewUrl())

async function loadProduct(){
    pageLoading.value = true

    try{
        // adjust URL if yours is different
        const res = await api.get(`/edit-product/${id}`)
        const p = res.data?.data ?? res.data // depends on your response shape

        form.name = p.name ?? ''
        form.sku = p.sku ?? ''
        form.price = p.price ?? ''
        form.stock_quantity = p.stock_quantity ?? ''
        form.min_stock = p.min_stock ?? ''

        // backend should send image_url; otherwise build it
        currentImageUrl.value = p.image_url;

    }catch(err){
        console.log(err)
    }finally{
        pageLoading.value = false
    }
}

async function submit(){
    if(!confirm("Are you sure to update this product?")) return;
    saving.value = true
    Object.keys(errors).forEach(k => delete errors[k])

    try{
        const fd = new FormData()
        fd.append('name', form.name)
        fd.append('sku', form.sku)
        fd.append('price', form.price)
        fd.append('stock_quantity', form.stock_quantity)
        fd.append('min_stock', form.min_stock)

        // only append if selected
        if (form.image) fd.append('image', form.image)

        // adjust URL/method if yours is PUT/PATCH
        const res = await api.post(`/update-product/${id}`, fd)

        // console.log(res.data?.message)
        router.push('/products')

    }catch(err){
        if(err.response?.data?.errors){
        Object.assign(errors, err.response.data.errors)
        }else{
        console.log(err)
        }
    }finally{
        saving.value = false
    }
}

onMounted(loadProduct)
</script>

<style scoped>
@reference "../style.css";

.label{ @apply text-sm font-semibold text-slate-700 block mb-1; }
.input{ @apply w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none; }
.error{ @apply text-xs text-red-500 mt-1; }
</style>
