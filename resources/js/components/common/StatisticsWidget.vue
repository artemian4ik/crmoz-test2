<template>
  <div class="statistics-widget">
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
      <div v-for="i in 5" :key="i" class="animate-pulse">
        <div class="bg-gray-100 rounded-lg p-6 h-32"></div>
      </div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <p class="text-red-600 text-sm">{{ error }}</p>
      <button 
        @click="loadStatistics" 
        class="mt-2 text-red-700 underline text-sm hover:text-red-800"
      >
        Спробувати знову
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
      <StatCard
        v-for="(stat, key) in statistics"
        :key="key"
        :label="stat.label"
        :count="stat.count"
        :color="stat.color"
        :icon="stat.icon"
      />
    </div>
  </div>
</template>

<script>
import StatCard from './StatCard.vue'

export default {
  name: 'StatisticsWidget',
  components: {
    StatCard
  },
  data() {
    return {
      statistics: null,
      loading: false,
      error: null
    }
  },
  mounted() {
    this.loadStatistics()
  },
  methods: {
    async loadStatistics() {
      this.loading = true
      this.error = null

      try {
        const response = await fetch('/api/dashboard/statistics', {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          credentials: 'include'
        })

        if (!response.ok) {
          throw new Error('Не вдалося завантажити статистику')
        }

        const data = await response.json()
        
        if (data.success) {
          this.statistics = data.data
        } else {
          throw new Error(data.message || 'Помилка отримання даних')
        }
      } catch (err) {
        this.error = err.message
        console.error('Statistics loading error:', err)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.statistics-widget {
  margin-bottom: 2rem;
}
</style>

