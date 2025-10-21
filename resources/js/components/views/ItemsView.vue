<template>
  <div class="items-view">
    <DataTable
      ref="dataTable"
      api-endpoint="/api/items"
      :headers="headers"
      :search-fields="['name', 'sku', 'brand', 'item_id']"
      search-placeholder="Пошук по назві, SKU, бренду..."
      total-label="Всього товарів"
      :custom-columns="['rate', 'purchase_rate', 'status', 'tax_name', 'created_at']"
      :actions="tableActions"
      @action="handleAction"
    >
      <template #column-rate="{ item }">
        <span class="font-medium text-green-600">
          {{ item.rate ? Number(item.rate).toFixed(2) : '0.00' }} грн
        </span>
      </template>

      <template #column-purchase_rate="{ item }">
        <span class="font-medium text-blue-600">
          {{ item.purchase_rate ? Number(item.purchase_rate).toFixed(2) : '0.00' }} грн
        </span>
      </template>

      <template #column-status="{ item }">
        <span 
          :class="{
            'bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'active',
            'bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'inactive',
            'bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium': !item.status
          }"
        >
          {{ item.status === 'active' ? 'Активний' : item.status === 'inactive' ? 'Неактивний' : item.status || 'Не вказано' }}
        </span>
      </template>

      <template #column-tax_name="{ item }">
        <span class="text-sm">
          {{ item.tax_name || '-' }}
          <span v-if="item.tax_percentage" class="text-gray-500 text-xs">
            ({{ item.tax_percentage }}%)
          </span>
        </span>
      </template>

      <template #column-created_at="{ item }">
        <span class="text-sm text-gray-700">
          {{ formatDate(item.created_at) }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script>
import DataTable from '../common/DataTable.vue'

export default {
  name: 'ItemsView',
  components: {
    DataTable
  },
  props: {
    user: Object
  },
  data() {
    return {
      headers: [
        { text: 'ID Товару', value: 'item_id', sortable: true, width: 120 },
        { text: 'Назва', value: 'name', sortable: true },
        { text: 'SKU', value: 'sku', sortable: true, width: 120 },
        { text: 'Бренд', value: 'brand', sortable: true },
        { text: 'Ціна продажу', value: 'rate', sortable: true, width: 130 },
        { text: 'Ціна закупки', value: 'purchase_rate', sortable: true, width: 130 },
        { text: 'Податок', value: 'tax_name', sortable: true, width: 100 },
        { text: 'Статус', value: 'status', sortable: true, width: 120 },
        { text: 'Дата створення', value: 'created_at', sortable: true, width: 150 }
      ],
      tableActions: {
        edit: {
          icon: 'fas fa-edit',
          tooltip: 'Редагувати'
        },
        delete: {
          icon: 'fas fa-trash',
          tooltip: 'Видалити',
          confirmMessage: 'Ви впевнені, що хочете видалити цей товар?'
        }
      }
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '-'
      
      const date = new Date(dateString)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      
      return `${day}.${month}.${year} ${hours}:${minutes}`
    },
    handleAction(payload) {
      const { type, item } = payload
      
      switch(type) {
        case 'edit':
          this.handleEdit(item)
          break
        case 'delete':
          this.handleDelete(item)
          break
      }
    },
    handleEdit(item) {
      console.log('Редагувати товар:', item)
      alert(`Редагування товару: ${item.name} (ID: ${item.id})`)
    },
    handleDelete(item) {
      console.log('Видалити товар:', item)
      alert(`Товар "${item.name}" буде видалено`)
    },
  }
}
</script>

<style scoped>
.items-view {
  padding: 0;
}
</style>
