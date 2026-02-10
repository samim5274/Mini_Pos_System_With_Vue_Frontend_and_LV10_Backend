<template>
  <div>
    <!-- SUCCESS -->
    <Transition name="toast">
      <div
        v-if="successMsg"
        class="fixed top-5 right-5 z-[9999] w-[320px] rounded-2xl border border-green-200 bg-white shadow-lg"
      >
        <div class="flex gap-3 p-4">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-700">
            <i class="fa-regular fa-circle-check"></i>
          </div>

          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900">Success</p>
            <p class="mt-0.5 text-sm text-slate-600">{{ successMsg }}</p>
          </div>

          <button
            class="ml-2 rounded-lg px-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
            @click="closeSuccess"
            aria-label="Close"
          >
            ✕
          </button>
        </div>

        <div class="h-1 w-full overflow-hidden rounded-b-2xl bg-green-50">
          <div class="toast-bar h-full bg-green-500"></div>
        </div>
      </div>
    </Transition>

    <!-- ERROR -->
    <Transition name="toast">
      <div
        v-if="errorMsg"
        class="fixed top-5 right-5 z-[9999] w-[320px] rounded-2xl border border-red-200 bg-white shadow-lg"
      >
        <div class="flex gap-3 p-4">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-700">
            <i class="fa-solid fa-x"></i>
          </div>

          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900">Failed</p>
            <p class="mt-0.5 text-sm text-slate-600">{{ errorMsg }}</p>
          </div>

          <button
            class="ml-2 rounded-lg px-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
            @click="closeError"
            aria-label="Close"
          >
            ✕
          </button>
        </div>

        <div class="h-1 w-full overflow-hidden rounded-b-2xl bg-red-50">
          <div class="toast-bar h-full bg-red-500"></div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { watch } from "vue"

const props = defineProps({
  successMsg: { type: String, default: "" },
  errorMsg: { type: String, default: "" },
  duration: { type: Number, default: 2500 },
})

const emit = defineEmits(["update:successMsg", "update:errorMsg"])

function closeSuccess() {
  emit("update:successMsg", "")
}

function closeError() {
  emit("update:errorMsg", "")
}

// auto hide
watch(() => props.successMsg, (v) => {
  if (!v) return
  setTimeout(closeSuccess, props.duration)
})

watch(() => props.errorMsg, (v) => {
  if (!v) return
  setTimeout(closeError, props.duration)
})
</script>

<style>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.98);
}
.toast-bar {
  width: 100%;
  animation: shrink 2.5s linear forwards;
}
@keyframes shrink {
  from { width: 100%; }
  to { width: 0%; }
}
</style>
