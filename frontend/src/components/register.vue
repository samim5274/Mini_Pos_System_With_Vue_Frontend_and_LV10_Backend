<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-400 to-blue-300 flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

      <!-- Title -->
      <h2 class="text-2xl font-bold text-slate-800 text-center">
        User Registration
      </h2>

      <div v-if="generalError" class="mt-4 rounded-xl bg-red-50 text-red-700 px-4 py-3 text-sm">
        {{ generalError }}
      </div>

      <form @submit.prevent="submitRegister" class="mt-6 space-y-4">

        <!-- Name -->
        <div>
          <label class="text-sm font-medium text-slate-700">Full Name</label>
          <input
            type="text"
            v-model.trim="form.name"
            required
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none"
            placeholder="Enter your name"
          >
        </div>

        <!-- Email -->
        <div>
          <label class="text-sm font-medium text-slate-700">Email</label>
          <input
            type="email"
            v-model.trim="form.email"
            required
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none"
            placeholder="example@mail.com"
          >
          <p v-if="errors.email" class="text-xs text-red-600 mt-1">
            {{ errors.email[0] }}
          </p>

        </div>

        <!-- Password -->
        <div>
          <label class="text-sm font-medium text-slate-700">Password</label>
          <input
            type="password"
            v-model="form.password"
            required
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none"
            placeholder="********"
          >
          <p v-if="errors.password" class="text-xs text-red-600 mt-1">
            {{ errors.password[0] }}
          </p>

        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Confirm Password</label>
          <input
            type="password"
            v-model="form.password_confirmation"
            required
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none"
            placeholder="********"
          >
        </div>


        <!-- Role -->
        <div>
          <label class="text-sm font-medium text-slate-700">Role</label>
          <select
            v-model="form.role"
            class="mt-1 w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none"
          >
            <option selected disabled>-- Select Role --</option>
            <option value="user">User</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <!-- Active Status -->
        <div class="flex items-center gap-2">
          <input
            type="checkbox"
            v-model="form.is_active"
            value="1"
            checked
            id="is_active"
            class="w-4 h-4 text-indigo-600 border-slate-300 rounded"
          >
          <label for="is_active" class="text-sm text-slate-700">Active Account</label>
        </div>

        <!-- Button -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition"
        >
          {{ loading ? 'Creating...' : 'Register' }}
        </button>

        <!-- Login Link -->
        <p class="text-center text-sm text-slate-600 mt-4">
          Already have an account?
          <router-link to="/login" class="text-indigo-600 font-semibold hover:underline hover:text-indigo-800">
            Login here
          </router-link>
        </p>

      </form>

    </div>
  </div>
</template>



<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const loading = ref(false);
const generalError = ref("");
const errors = reactive({});

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "",
  is_active: "",
});

function resetErrors(){
  generalError.value = "";
  Object.keys(errors).forEach((k) => delete errors[k]);
}

async function submitRegister(){
  resetErrors();
  loading.value = true;

  try{
    // data send backend
    await api.post("/register", {
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
      role: form.role,
      is_active: form.is_active ? 1 : 0,
    });
    router.push("/login");
  } catch (err){
    console.log(err?.response?.data);
    if(err?.response?.status === 422){
      const v = err.response.data.errors || {};
      Object.assign(errors, v);
      generalError.value = err.response.data.message | "Validation error";
    } else {
      generalError.value = err?.response?.data?.message | "Somethins went wrong..!";
    }
  } finally {
    loading.value = false;
  }
}
</script>



<style>

</style>