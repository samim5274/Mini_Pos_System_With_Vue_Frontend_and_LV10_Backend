import { createRouter, createWebHistory } from 'vue-router'

import Login from '../components/login.vue'
import Register from '../components/register.vue'
import dashboard from '../components/dashboard.vue'
import products from '../components/product-list.vue'
import createProduct from '../components/create-product.vue'
import editProduct from '../components/edit-product.vue'
import cart from '../components/cart.vue'
import payment from '../components/payment.vue'
import order from '../components/order.vue'
import orderDetails from '../components/order-details.vue'
import PrintInvoice from '../components/print/print-invoice-80mm.vue'
import expense from '../components/Expense/expense-overview.vue'
import createExpense from '../components/Expense/create-expense.vue'
import expenseDetails from '../components/Expense/expense-details.vue'
import printExpenseA4Halft from '../components/print/print-expense-a4-halft.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/dashboard', component: dashboard, meta: { requiresAuth: true } },
  { path: '/products', component: products, meta: { requiresAuth: true } },
  { path: '/create-product', component: createProduct, meta: { requiresAuth: true } },
  { path: '/edit-product/:id', component: editProduct, props: true, meta: { requiresAuth: true } },
  { path: '/cart', component: cart, meta: { requiresAuth: true } },
  { path: '/order/payment/:id', component: payment, meta: {requiresAuth: true} },
  { path: '/order', component: order, meta: {requiresAuth: true} },
  { path: '/order/:id', component: orderDetails, meta: {requiresAuth: true} },
  { path: '/order/invoice-print/:reg', component: PrintInvoice, meta: {requiresAuth: true} },
  { path: '/expense', component: expense, meta: {requiresAuth: true} },
  { path: '/create-expense', component: createExpense, meta: {requiresAuth: true} },
  { path: '/expense-details/:id', component: expenseDetails, meta: {requiresAuth: true} },
  { path: '/expense-print/:id', component: printExpenseA4Halft, meta: {requiresAuth: true} },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  
  const token = localStorage.getItem("token");

  if(to.meta.requiresAuth && !token){
    next('/login');
  } else {
    next();
  }
})

export default router
