<template>
    <div v-if="order" class="invoice-wrap">
        <!-- HEADER -->
        <div class="invoice-header">
        <h2>{{ company?.name || "YOUR COMPANY" }}</h2>
        <p>{{ company?.address || "" }}</p>
        <p>
            {{ company?.email || "" }}
            <span v-if="company?.email && company?.phone"> || </span>
            {{ company?.phone || "" }}
        </p>
        <p class="order-title">INVOICE</p>
        </div>

        <!-- SUB HEADER -->
        <div class="invoice-subheader">
        <div class="info-line">
            <span><strong>Bill Officer:</strong> {{ order?.bill_officer || order?.user?.name || "-" }}</span>
            <span><strong>Customer:</strong> {{ order?.customerName || order?.customer_name || "-" }}</span>
        </div>
        <div class="info-line">
            <span><strong>C.Phone:</strong> {{ order?.customerPhone || order?.customer_phone || "-" }}</span>
            <span><strong>Date:</strong> {{ formatDate(order?.date || order?.created_at) }}</span>
        </div>
        <div class="info-line">
            <span><strong>Invoice:</strong> {{ order?.reg || "-" }}</span>
            <span><strong>Status:</strong> {{ order?.status || "-" }}</span>
        </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
        <thead>
            <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>৳/Unit</th>
            <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="(it, idx) in items" :key="it.id || idx">
            <td>{{ idx + 1 }}</td>
            <td>{{ limitText(it?.product?.name || it?.name || "Item", 15) }}</td>
            <td>{{ it?.quantity ?? 0 }}</td>
            <td>{{ money(it?.price ?? 0) }}</td>
            <td>{{ money((Number(it?.price || 0) * Number(it?.quantity || 0))) }}</td>
            </tr>
        </tbody>
        </table>

        <!-- TOTALS -->
        <table class="totals-table">
        <tr class="separator">
            <td>Subtotal:</td>
            <td>৳{{ money(orderSubtotal) }}</td>
        </tr>
        <tr>
            <td>Discount:</td>
            <td>- ৳{{ money(orderDiscount) }}</td>
        </tr>
        <tr>
            <td>VAT:</td>
            <td>+ ৳{{ money(orderVat) }}</td>
        </tr>
        <tr class="separator final-total">
            <td>Payable:</td>
            <td>৳{{ money(orderPayable) }}</td>
        </tr>
        <tr>
            <td>Paid:</td>
            <td>৳{{ money(orderPaid) }}</td>
        </tr>
        <tr>
            <td class="final-total">Due:</td>
            <td class="final-total">৳{{ money(orderDue) }}</td>
        </tr>
        </table>

        <div class="note">
        Developed by <strong>ARS Soft Solution</strong> || +8801533021557
        </div>

        <div class="note">.</div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { useRoute } from "vue-router";
import api from "../../services/api";

const route = useRoute();

const order = ref(null);
const items = ref([]);
const company = ref(null);

function formatDate(dateStr) {
    if (!dateStr) return "-";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return "-";
    return d.toLocaleDateString("en-GB", { day: "2-digit", month: "2-digit", year: "numeric" });
}

function limitText(text, max = 15) {
    const s = String(text ?? "");
    return s.length > max ? s.slice(0, max) + "..." : s;
}

function money(v) {
    const n = Number(v ?? 0);
    return n.toFixed(2);
}

const orderSubtotal = computed(() => {
    const o = order.value || {};
    if (o.total != null) return Number(o.total);
    return (items.value || []).reduce((sum, it) => sum + (Number(it.price || 0) * Number(it.quantity || 0)), 0);
});

const orderDiscount = computed(() => Number(order.value?.discount ?? 0));
const orderVat = computed(() => Number(order.value?.vat ?? 0));

const orderPayable = computed(() => {
    const o = order.value || {};
    if (o.payable != null) return Number(o.payable);
    return orderSubtotal.value - orderDiscount.value + orderVat.value;
});

const orderPaid = computed(() => Number(order.value?.pay ?? order.value?.paid ?? 0));

const orderDue = computed(() => {
    const o = order.value || {};
    if (o.due != null) return Number(o.due);
    return Math.max(0, orderPayable.value - orderPaid.value);
});

async function loadOrder() {
    const reg = route.params.reg;
    const res = await api.post(`/order/invoice-print/${reg}`);

    order.value = res.data?.data?.order || null;
    items.value = res.data?.data?.cartitems || [];
    company.value = res.data?.company ?? null;
}

onMounted(async () => {
    await loadOrder();

    setTimeout(() => window.print(), 300);

    window.onafterprint = () => {
        window.close();
    };

    const onVisibility = () => {
        if (!document.hidden) {
        setTimeout(() => window.close(), 300);
        }
    };

    document.addEventListener("visibilitychange", onVisibility);

    setTimeout(() => {
        window.close();
    }, 800); 

    window.addEventListener("beforeunload", () => {
        document.removeEventListener("visibilitychange", onVisibility);
    });
});
</script>



<style scoped>

.invoice-wrap {
  font-family: 'Consolas', 'Courier New', monospace;
  font-size: 10px;
  width: 68mm;
  margin: 0 auto;
  padding: 2mm 2mm;
  line-height: 1.3;
}

/* HEADER */
.invoice-header {
  text-align: center;
  padding: 5px 0;
  border-bottom: 1px dashed #000;
  margin-bottom: 8px;
}
.invoice-header h2 {
  font-size: 14px;
  margin: 2px 0;
  text-transform: uppercase;
}
.invoice-header p {
  margin: 0;
  font-size: 10px;
}
.invoice-header .order-title {
  font-size: 11px;
  font-weight: bold;
  margin-top: 5px;
}

/* SUBHEADER */
.invoice-subheader {
  margin-bottom: 8px;
  font-size: 10px;
}
.info-line {
  display: flex;
  justify-content: space-between;
  gap: 6px;
  margin: 1px 0;
}
.info-line span { white-space: nowrap; }

/* ITEMS TABLE */
.items-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 8px;
  font-size: 9.5px;
}
.items-table th, .items-table td {
  padding: 2px 0;
  border: none;
  text-align: right;
  white-space: pre-wrap;
}
.items-table thead {
  border-bottom: 1px dashed #000;
}
.items-table th:nth-child(1), .items-table td:nth-child(1) { width: 5%;  text-align: left; }
.items-table th:nth-child(2), .items-table td:nth-child(2) { width: 40%; text-align: left; }
.items-table th:nth-child(3), .items-table td:nth-child(3) { width: 10%; text-align: right; }
.items-table th:nth-child(4), .items-table td:nth-child(4) { width: 20%; text-align: right; }
.items-table th:nth-child(5), .items-table td:nth-child(5) { width: 25%; text-align: right; }

/* TOTALS */
.totals-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 10px;
  margin-bottom: 10px;
}
.totals-table tr td { padding: 2px 0; }
.totals-table td:first-child { text-align: left; width: 50%; }
.totals-table td:last-child { text-align: right; width: 50%; }

.separator { border-top: 1px dashed #000; }
.final-total { font-weight: bold; font-size: 11px; }

/* NOTE */
.note {
  text-align: center;
  font-size: 10px;
  margin-top: 5px;
  margin-bottom: 15px;
  padding-top: 5px;
  page-break-inside: avoid;
}
</style>

<!-- print-only global rules (scoped হবে না) -->
<style>
@media print {
  /* global reset শুধু print সময় */
  * { margin: 0; padding: 0; box-sizing: border-box; }

  @page { size: 80mm auto; margin: 0; }

  html, body { margin: 0 !important; padding: 0 !important; }

  /* print করার সময় background remove */
  body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
