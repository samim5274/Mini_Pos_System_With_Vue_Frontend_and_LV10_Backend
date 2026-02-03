import { createRouter, createWebHistory } from 'vue-router'

import Login from '../components/login.vue'
import Register from '../components/register.vue'
import dashboard from '../components/dashboard.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/dashboard', component: dashboard, meta: { requiresAuth: true } },
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
