<template>
  <Sidebar 
    :userName="user?.name || user?.email" 
    :userEmail="user?.email"
    :activeItem="activeMenuItem"
    @navigate="handleNavigate"
  >
    <div class="min-h-screen bg-gray-100">
      <nav class="bg-white shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <h1 class="text-xl font-bold">{{ pageTitle }}</h1>
            </div>
            <div class="flex items-center gap-4">
              <div 
                v-if="zohoTokenStatus"
                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm"
                :class="{
                  'bg-green-50 text-green-700 border border-green-200': zohoTokenStatus.is_active,
                  'bg-red-50 text-red-700 border border-red-200': !zohoTokenStatus.is_active && zohoTokenStatus.status !== 'not_configured',
                  'bg-gray-50 text-gray-700 border border-gray-200': zohoTokenStatus.status === 'not_configured'
                }"
              >
                <svg 
                  class="w-4 h-4" 
                  :class="{
                    'text-green-500': zohoTokenStatus.is_active,
                    'text-red-500': !zohoTokenStatus.is_active && zohoTokenStatus.status !== 'not_configured',
                    'text-gray-500': zohoTokenStatus.status === 'not_configured'
                  }"
                  fill="currentColor" 
                  viewBox="0 0 20 20"
                >
                  <circle cx="10" cy="10" r="8"/>
                </svg>
                <span class="font-medium">Zoho:</span>
                <span>{{ zohoTokenStatus.message }}</span>
                <span v-if="zohoTokenStatus.last_refresh_human" class="text-xs opacity-75">
                  ({{ zohoTokenStatus.last_refresh_human }})
                </span>
              </div>
              
              <button
                @click="handleLogout"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-200"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </nav>

      <main class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <component :is="currentView" :user="user" :open-create-form="openCreateForm" @navigate="handleNavigate" @form-opened="openCreateForm = false" />
        </div>
      </main>
    </div>
  </Sidebar>
</template>

<script>
import axios from 'axios'
import Sidebar from './Sidebar.vue'
import DashboardView from './views/DashboardView.vue'
import CustomersView from './views/CustomersView.vue'
import ItemsView from './views/ItemsView.vue'
import SalesOrdersView from './views/SalesOrdersView.vue'
import PurchaseOrdersView from './views/PurchaseOrdersView.vue'

export default {
  name: 'Dashboard',
  components: {
    Sidebar,
    DashboardView,
    CustomersView,
    SalesOrdersView,
    PurchaseOrdersView,
    ItemsView
  },
  data() {
    return {
      user: null,
      activeMenuItem: 'dashboard',
      zohoTokenStatus: null,
      openCreateForm: false
    }
  },
  computed: {
    currentView() {
      const views = {
        dashboard: 'DashboardView',
        customers: 'CustomersView', 
        sales_orders: 'SalesOrdersView',
        purchase_orders: 'PurchaseOrdersView',
        items: 'ItemsView',
      }
      return views[this.activeMenuItem] || 'DashboardView'
    },
    pageTitle() {
      const titles = {
        dashboard: 'Dashboard',
        items: 'Items',
        sales_orders: 'Sales Orders',
        purchase_orders: 'Purchase Orders',
        customers: 'Customers',
      }
      return titles[this.activeMenuItem] || 'Dashboard'
    }
  },
  async mounted() {
    await this.loadUser()
    await this.loadZohoTokenStatus()
    
    this.tokenStatusInterval = setInterval(() => {
      this.loadZohoTokenStatus()
    }, 30000)
  },
  beforeUnmount() {
    if (this.tokenStatusInterval) {
      clearInterval(this.tokenStatusInterval)
    }
  },
  methods: {
    async loadUser() {
      try {
        const response = await axios.get('/api/user')
        this.user = response.data.user
      } catch (error) {
        this.$router.push('/login')
      }
    },
    async loadZohoTokenStatus() {
      try {
        const response = await axios.get('/api/zoho/token-status')
        this.zohoTokenStatus = response.data
      } catch (error) {
        console.error('Error loading Zoho token status:', error)
        this.zohoTokenStatus = null
      }
    },
    handleNavigate(itemName, options = {}) {
      this.openCreateForm = false
      this.activeMenuItem = itemName
      if (options.openCreateForm) {
        this.$nextTick(() => {
          this.openCreateForm = true
        })
      }
    },
    async handleLogout() {
      try {
        await axios.post('/api/logout')
        this.$router.push('/login')
      } catch (error) {
        console.error('Logout error:', error)
      }
    }
  }
}
</script>

