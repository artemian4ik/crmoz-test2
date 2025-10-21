<template>
  <div 
    class="stat-card rounded-lg p-6 shadow-sm border-2 transition-all duration-200 hover:shadow-md"
    :class="cardClasses"
  >
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium mb-1" :class="labelClasses">
          {{ label }}
        </p>
        <p class="text-3xl font-bold" :class="countClasses">
          {{ formattedCount }}
        </p>
      </div>
      <div 
        class="rounded-full p-3 ml-4"
        :class="iconBgClasses"
      >
        <component 
          :is="iconComponent" 
          :class="iconClasses"
          class="w-6 h-6"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatCard',
  props: {
    label: {
      type: String,
      required: true
    },
    count: {
      type: Number,
      default: 0
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => {
        return ['blue', 'green', 'purple', 'orange', 'indigo', 'red', 'yellow'].includes(value)
      }
    },
    icon: {
      type: String,
      default: 'box'
    }
  },
  computed: {
    formattedCount() {
      return new Intl.NumberFormat('uk-UA').format(this.count)
    },
    cardClasses() {
      const classes = {
        'blue': 'bg-blue-50 border-blue-200 hover:border-blue-300',
        'green': 'bg-green-50 border-green-200 hover:border-green-300',
        'purple': 'bg-purple-50 border-purple-200 hover:border-purple-300',
        'orange': 'bg-orange-50 border-orange-200 hover:border-orange-300',
        'indigo': 'bg-indigo-50 border-indigo-200 hover:border-indigo-300',
        'red': 'bg-red-50 border-red-200 hover:border-red-300',
        'yellow': 'bg-yellow-50 border-yellow-200 hover:border-yellow-300'
      }
      return classes[this.color] || classes.blue
    },
    labelClasses() {
      const classes = {
        'blue': 'text-blue-700',
        'green': 'text-green-700',
        'purple': 'text-purple-700',
        'orange': 'text-orange-700',
        'indigo': 'text-indigo-700',
        'red': 'text-red-700',
        'yellow': 'text-yellow-700'
      }
      return classes[this.color] || classes.blue
    },
    countClasses() {
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
      // Простий SVG компонент для іконок
      return {
        template: this.getIconSvg()
      }
    }
  },
  methods: {
    getIconSvg() {
      const icons = {
        'box': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
          </svg>
        `,
        'users': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
          </svg>
        `,
        'shopping-cart': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
          </svg>
        `,
        'shopping-bag': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
        `,
        'file-text': `
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
        `
      }
      return icons[this.icon] || icons.box
    }
  }
}
</script>

<style scoped>
.stat-card {
  min-height: 140px;
}
</style>

