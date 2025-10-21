<template>
  <div class="flex">
    <aside 
      :class="[
        'bg-gray-800 text-white transition-all duration-300 h-screen fixed left-0 top-0 z-40',
        isOpen ? 'w-64' : 'w-16'
      ]"
    >
      <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <h2 v-show="isOpen" class="text-lg font-bold">Admin Panel</h2>
        <button 
          @click="toggleSidebar"
          class="p-2 rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg 
            class="w-6 h-6 transition-transform duration-300" 
            :class="{ 'rotate-180': !isOpen }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <nav class="mt-6">
        <a 
          v-for="item in menuItems" 
          :key="item.name"
          :href="item.href"
          @click.prevent="$emit('navigate', item.name)"
          :class="[
            'flex items-center px-4 py-3 hover:bg-gray-700 transition-colors',
            activeItem === item.name ? 'bg-gray-700 border-l-4 border-blue-500' : ''
          ]"
        >
          <span class="text-2xl"></span>
          <span 
            v-show="isOpen" 
            class="ml-4 transition-opacity"
            :class="{ 'opacity-0': !isOpen }"
          >
            {{ item.label }}
          </span>
        </a>
      </nav>

      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
        <div :class="['flex items-center', isOpen ? 'justify-between' : 'justify-center']">
          <div v-show="isOpen" class="flex items-center">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
              {{ userName?.charAt(0).toUpperCase() }}
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium">{{ userName }}</p>
              <p class="text-xs text-gray-400">{{ userEmail }}</p>
            </div>
          </div>
          <button 
            v-if="!isOpen"
            class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center"
          >
            {{ userName?.charAt(0).toUpperCase() }}
          </button>
        </div>
      </div>
    </aside>

    <div 
      :class="[
        'transition-all duration-300',
        isOpen ? 'ml-64' : 'ml-16'
      ]"
      :style="{
        width: isOpen ? 'calc(100vw - 16rem)' : 'calc(100vw - 4rem)',
        maxWidth: isOpen ? 'calc(100vw - 16rem)' : 'calc(100vw - 4rem)',
        overflowX: 'hidden'
      }"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    userName: {
      type: String,
      default: 'User'
    },
    userEmail: {
      type: String,
      default: ''
    },
    activeItem: {
      type: String,
      default: 'dashboard'
    }
  },
  data() {
    return {
      isOpen: true,
      menuItems: [
        {
          name: 'dashboard',
          label: 'Dashboard',
          href: '/dashboard'
        },
        {
          name: 'items',
          label: 'Items',
          href: '/items'
        },
        {
          name: 'sales_orders',
          label: 'Sales Orders',
          href: '/sales_orders'
        },
        {
          name: 'purchase_orders',
          label: 'Purchase Orders',
          href: '/purchase_orders'
        },
        {
          name: 'customers',
          label: 'Customers',
          href: '/customers'
        }
      ]
    }
  },
  methods: {
    toggleSidebar() {
      this.isOpen = !this.isOpen
    }
  }
}
</script>

