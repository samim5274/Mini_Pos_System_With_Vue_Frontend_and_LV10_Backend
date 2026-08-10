import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "../services/api";

export const useCartStore = defineStore("cart", () => {
    const carts = ref([]);
    const loading = ref(false);

    // how much row/item
    const itemCount = computed(() => carts.value.length);

    // total qty 
    const qtyCount = computed(() =>
        carts.value.reduce((sum, item) => sum + Number(item.quantity || item.qty || 1), 0)
    );

    async function fetchCart() {
        loading.value = true;
        try {
            const res = await api.get("/cart");
            carts.value = res.data?.data || [];
        } catch (e) {        
            carts.value = [];
        } finally {
            loading.value = false;
        }
    }

  return { carts, loading, itemCount, qtyCount, fetchCart };
});
