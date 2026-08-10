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
            <mainSection />

            </div>
        </div>
    </div>

</template>

<script setup>
import { useRouter } from 'vue-router'
import api from '../services/api'
import navbar from './navbar.vue'
import headerSection from './header-section.vue'
import mainSection from './main.vue'

const router = useRouter()

</script>