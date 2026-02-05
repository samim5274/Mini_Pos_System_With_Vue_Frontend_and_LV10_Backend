import { createRouter, createWebHistory } from 'vue-router'

import Login from '../components/login.vue'
import Register from '../components/register.vue'
import dashboard from '../components/dashboard.vue'
import products from '../components/product-list.vue'
import createProduct from '../components/create-product.vue'
import editProduct from '../components/edit-product.vue'
import cart from '../components/cart.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/dashboard', component: dashboard, meta: { requiresAuth: true } },
  { path: '/products', component: products, meta: { requiresAuth: true } },
  { path: '/create-product', component: createProduct, meta: { requiresAuth: true } },
  { path: '/edit-product/:id', component: editProduct, props: true, meta: { requiresAuth: true } },
  { path: '/cart', component: cart, meta: { requiresAuth: true } },
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
