import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import './bootstrap'

import App from './App.vue'
import Login from './components/Login.vue'
import Dashboard from './components/Dashboard.vue'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: { guest: true }
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: Dashboard,
            meta: { requiresAuth: true }
        },
        {
            path: '/',
            redirect: '/dashboard'
        }
    ]
})

router.beforeEach(async (to, from, next) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token
    }

    if (to.meta.requiresAuth) {
        try {
            await axios.get('/api/user')
            next()
        } catch (error) {
            next('/login')
        }
    } else if (to.meta.guest) {
        try {
            await axios.get('/api/user')
            next('/dashboard')
        } catch (error) {
            next()
        }
    } else {
        next()
    }
})

const app = createApp(App)
app.use(router)
app.mount('#app')
