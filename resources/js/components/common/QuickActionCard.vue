<template>
  <button
    @click="$emit('click')"
    class="quick-action-card text-left p-6 rounded-lg border-2 transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2"
    :class="cardClasses"
  >
    <div class="flex items-start">
      <div 
        class="rounded-full p-3 mr-4"
        :class="iconBgClasses"
      >
        <component 
          :is="iconComponent" 
          :class="iconClasses"
          class="w-6 h-6"
        />
      </div>
      <div class="flex-1">
        <h4 class="font-semibold mb-1" :class="titleClasses">
          {{ title }}
        </h4>
        <p class="text-sm text-gray-600">
          {{ description }}
        </p>
      </div>
    </div>
  </button>
</template>

<script>
export default {
  name: 'QuickActionCard',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: 'plus-circle'
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => {
        return ['blue', 'green', 'purple', 'orange', 'indigo', 'red', 'yellow'].includes(value)
      }
    }
  },
  computed: {
    cardClasses() {
      const classes = {
        'blue': 'bg-white border-blue-200 hover:border-blue-400 focus:ring-blue-500',
        'green': 'bg-white border-green-200 hover:border-green-400 focus:ring-green-500',
        'purple': 'bg-white border-purple-200 hover:border-purple-400 focus:ring-purple-500',
        'orange': 'bg-white border-orange-200 hover:border-orange-400 focus:ring-orange-500',
        'indigo': 'bg-white border-indigo-200 hover:border-indigo-400 focus:ring-indigo-500',
        'red': 'bg-white border-red-200 hover:border-red-400 focus:ring-red-500',
        'yellow': 'bg-white border-yellow-200 hover:border-yellow-400 focus:ring-yellow-500'
      }
      return classes[this.color] || classes.blue
    },
    titleClasses() {
      const classes = {
        'blue': 'text-blue-900',
        'green': 'text-green-900',
        'purple': 'text-purple-900',
        'orange': 'text-orange-900',
        'indigo': 'text-indigo-900',
        'red': 'text-red-900',
        'yellow': 'text-yellow-900'
      }
      return classes[this.color] || classes.blue
    },
    iconBgClasses() {
      const classes = {
        'blue': 'bg-blue-100',
        'green': 'bg-green-100',
        'purple': 'bg-purple-100',
        'orange': 'bg-orange-100',
        'indigo': 'bg-indigo-100',
        'red': 'bg-red-100',
        'yellow': 'bg-yellow-100'
      }
      return classes[this.color] || classes.blue
    },
    iconClasses() {
      const classes = {
        'blue': 'text-blue-600',
        'green': 'text-green-600',
        'purple': 'text-purple-600',
        'orange': 'text-orange-600',
        'indigo': 'text-indigo-600',
        'red': 'text-red-600',
        'yellow': 'text-yellow-600'
      }
      return classes[this.color] || classes.blue
    },
    iconComponent() {
      return {
        template: this.getIconSvg()
      }
    }
  },
  methods: {
    getIconSvg() {
      const icons = {
        'plus-circle': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
        `,
        'user-plus': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
          </svg>
        `,
        'box': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
          </svg>
        `,
        'shopping-bag': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
        `
      }
      return icons[this.icon] || icons['plus-circle']
    }
  }
}
</script>

<style scoped>
.quick-action-card {
  cursor: pointer;
}

.quick-action-card:active {
  transform: scale(0.98);
}
</style>

