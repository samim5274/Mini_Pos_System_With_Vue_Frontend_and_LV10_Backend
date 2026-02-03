<template>
<div class="min-h-screen bg-gradient-to-br from-indigo-200 to-blue-400 flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-slate-800">
        Login Account
        </h2>

        <p class="text-sm text-center text-slate-500 mt-1">
        Enter your credentials to continue
        </p>

        <!-- Error Message -->
        <div
            v-if="generalError"
            class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
        >
            {{ generalError }}
        </div>

        <!-- Form -->
        <form @submit.prevent="submitLogin" class="mt-6 space-y-5">

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input
            type="email"
            v-model="form.email"
            required
            placeholder="example@mail.com"
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition"
            >
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input
            type="password"
            v-model="form.password"
            required
            placeholder="********"
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition"
            >
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between text-sm">

            <label class="flex items-center gap-2 text-slate-600">
            <input type="checkbox" v-model="form.remember" class="w-4 h-4">
            Remember me
            </label>

            <p class="text-indigo-600 hover:underline">
            Forgot password?
            </p>

        </div>

        <!-- Button -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition"
        >
            {{ loading ? "Please wait..." : "Login" }}
        </button>

        <!-- Register link -->
        <p class="text-center text-sm text-slate-600 mt-4">
            Don’t have an account?
            <router-link to="/register"
            class="text-indigo-600 font-semibold hover:underline hover:text-indigo-800">
            Register here
            </router-link>
        </p>

        </form>

    </div>
</div>

</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();

const loading = ref(false);
const generalError = ref("");
const errors = reactive({});

const form = reactive({
    email: "",
    password: "",
    remember: false,
});

function resetErrors(){
    generalError.value = "";
    Object.keys(errors).forEach((k) => delete errors[k]);
}

async function submitLogin() {
    resetErrors();
    loading.value = true;

    try{
        const res = await api.post("/login", {
            email: form.email,
            password: form.password,
            device_name: "web",
            remember: form.remember,
        });

        if (!res?.data?.token) {
            generalError.value = res?.data?.message || "Login failed!";
            return;
        }

        // save token
        localStorage.setItem("token", res.data.token);
        localStorage.setItem("user", JSON.stringify(res.data.user));    
        router.push("/dashboard");
    } catch (err){
        const status = err?.response?.status;
        const data = err?.response?.data;

        if (status === 422) {
        Object.assign(errors, data?.errors || {});
        generalError.value = data?.message || "Validation error";
        } else {
        generalError.value =
            data?.message || "Something went wrong..!";
        }
    } finally {
        loading.value = false;
    }
}

</script>

<style>

</style>