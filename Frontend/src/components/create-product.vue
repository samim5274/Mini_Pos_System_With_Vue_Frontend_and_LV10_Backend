<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

import Navbar from "./navbar.vue";
import HeaderSection from "./header-section.vue";

const router = useRouter()

const loading = ref(false)
const preview = ref(null)
const errors = reactive({})
const isDragOver = ref(false)

const form = reactive({
    name: '',
    sku: '',
    price: '',
    stock_quantity: '',
    min_stock: '',
    image: null
})

function setFile(file){
  if (!file) return

  // only images
  if (!file.type?.startsWith('image/')) return

  form.image = file
  preview.value = URL.createObjectURL(file)
}

function handleImage(e){
  const file = e.target.files?.[0]
  setFile(file)
}

function onDrop(e){
  isDragOver.value = false
  const file = e.dataTransfer?.files?.[0]
  setFile(file)
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
  preview.value = null
}

/* Submit */
async function submit(){
    loading.value = true
    Object.keys(errors).forEach(k => delete errors[k])

    try{
        const fd = new FormData()

        for(const key in form){
            fd.append(key, form[key])
        }

        // console.log(form.image) // should be File object
        // console.log(fd.get('image')) // should be File

        const res = await api.post('/create-product', fd)
        console.log(res.data.message);
        router.push('/products')

    }catch(err){
        if(err.response?.data?.errors){
        Object.assign(errors, err.response.data.errors)
        }
    }finally{
        loading.value = false
    }
}
</script>

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
                   
                    <div class="mx-auto bg-white shadow-lg rounded-2xl p-8">

                        <h2 class="text-2xl font-bold mb-6">➕ Add New Product</h2>

                        <form @submit.prevent="submit" class="space-y-5">

                        <!-- Name -->
                        <div>
                            <label class="label">Product Name</label>
                            <input v-model="form.name" class="input" placeholder="e.g Duble A A4 paper"/>
                            <p class="error" v-if="errors.name">{{ errors.name[0] }}</p>
                        </div>

                        <!-- SKU -->
                        <div>
                            <label class="label">SKU</label>
                            <input v-model="form.sku" class="input" placeholder="e.g A4PAPER001"/>
                            <p class="error" v-if="errors.sku">{{ errors.sku[0] }}</p>
                        </div>

                        <!-- Price + Stock grid -->
                        <div class="grid grid-cols-2 gap-4">

                            <div>
                            <label class="label">Price</label>
                            <input type="number" v-model="form.price" class="input" placeholder="e.g BDT ৳ 450.00/-"/>
                            </div>

                            <div>
                            <label class="label">Stock Quantity</label>
                            <input type="number" v-model="form.stock_quantity" class="input" placeholder="e.g 15 pcs"/>
                            </div>

                        </div>

                        <!-- Min Stock -->
                        <div>
                            <label class="label">Minimum Stock Alert</label>
                            <input type="number" v-model="form.min_stock" class="input" placeholder="e.g 5 pcs"/>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="label">Product Image</label>

                            <!-- Drop Zone -->
                            <div
                                class="rounded-2xl border-2 border-dashed p-5 transition bg-white"
                                :class="isDragOver ? 'border-blue-500 bg-blue-50' : 'border-slate-300'"
                                @dragover="onDragOver"
                                @dragleave="onDragLeave"
                                @drop.prevent="onDrop"
                            >
                                <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="text-sm text-slate-600">
                                    <div class="font-semibold text-slate-800">Drag & drop an image here</div>
                                    <div class="text-xs text-slate-500">or click Browse (jpg, png, webp)</div>
                                </div>

                                <!-- Browse Button -->
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

                                <!-- Preview -->
                                <div v-if="preview" class="mt-4 flex items-center gap-4">
                                <img :src="preview" class="h-28 w-28 rounded-xl border object-cover" />

                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-800">
                                    {{ form.image?.name }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                    {{ Math.round((form.image?.size || 0) / 1024) }} KB
                                    </p>

                                    <button
                                    type="button"
                                    class="mt-2 text-sm text-red-600 hover:underline"
                                    @click="removeImage"
                                    >
                                    Remove
                                    </button>
                                </div>
                                </div>
                            </div>
                            </div>


                        <!-- Buttons -->
                        <div class="flex gap-3 pt-4">

                            <button
                            :disabled="loading"
                            class="bg-blue-600 text-white px-5 py-2 rounded-xl hover:bg-blue-700"
                            >
                            {{ loading ? 'Saving...' : 'Save Product' }}
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

<style scoped>
@reference "../style.css";

.label{
  @apply text-sm font-semibold text-slate-700 block mb-1;
}
.input{
  @apply w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none;
}
.error{
  @apply text-xs text-red-500 mt-1;
}
</style>

