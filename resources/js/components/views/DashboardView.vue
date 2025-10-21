<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Widgets</h2>
    
    <StatisticsWidget />

    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-4">Швидкі дії</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <QuickActionCard
          title="Створити замовлення"
          description="Нове замовлення на продаж"
          icon="plus-circle"
          color="blue"
          @click="createSalesOrder"
        />
      </div>
    </div>
  </div>
</template>

<script>
import StatisticsWidget from '../common/StatisticsWidget.vue'
import QuickActionCard from '../common/QuickActionCard.vue'

export default {
  name: 'DashboardView',
  components: {
    StatisticsWidget,
    QuickActionCard
  },
  props: {
    user: Object
  },
  methods: {
    formatDate(date) {
      if (!date) return 'Не вказано'
      return new Date(date).toLocaleDateString('uk-UA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    createSalesOrder() {
      this.$emit('navigate', 'sales_orders', { openCreateForm: true })
    },
  }
}
</script>

<style scoped>

</style>

