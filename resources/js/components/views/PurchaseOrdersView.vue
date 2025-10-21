<template>
  <div class="purchase-orders-view">
    <DataTable
      ref="dataTable"
      api-endpoint="/api/purchase-orders"
      :headers="headers"
      :search-fields="['purchaseorder_number', 'reference_number', 'vendor_name']"
      search-placeholder="Пошук по номеру, постачальнику..."
      total-label="Всього замовлень на покупку"
      :custom-columns="['purchaseorder_number', 'vendor_name', 'status', 'total', 'date', 'quantity', 'created_at']"
      :actions="tableActions"
      @action="handleAction"
    >
      <template #column-purchaseorder_number="{ item }">
        <div class="flex flex-col">
          <span class="font-semibold text-blue-700">{{ item.purchaseorder_number }}</span>
          <span v-if="item.reference_number" class="text-xs text-gray-500">Ref: {{ item.reference_number }}</span>
        </div>
      </template>

      <template #column-vendor_name="{ item }">
        <div class="flex flex-col">
          <span class="font-medium text-gray-900">{{ item.vendor_name }}</span>
          <span v-if="item.vendor_id" class="text-xs text-gray-500">ID: {{ item.vendor_id }}</span>
        </div>
      </template>

      <template #column-status="{ item }">
        <span 
          :class="{
            'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'draft',
            'bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'confirmed',
            'bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'closed',
            'bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'void' || item.status === 'cancelled',
            'bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'open',
            'bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium': !item.status
          }"
        >
          {{ getStatusLabel(item.status) }}
        </span>
      </template>

      <template #column-total="{ item }">
        <div class="flex flex-col text-right">
          <span class="font-semibold text-gray-900">
            {{ item.total ? Number(item.total).toFixed(2) : '0.00' }} {{ item.currency_code || 'UAH' }}
          </span>
          <span v-if="item.bcy_total && item.currency_code !== 'UAH'" class="text-xs text-gray-500">
            {{ Number(item.bcy_total).toFixed(2) }} UAH
          </span>
        </div>
      </template>

      <template #column-date="{ item }">
        <div class="flex flex-col text-sm">
          <span class="font-medium text-gray-700">{{ formatDate(item.date) }}</span>
          <span v-if="item.delivery_date" class="text-xs text-gray-500">
            Доставка: {{ formatDate(item.delivery_date) }}
          </span>
        </div>
      </template>

      <template #column-quantity="{ item }">
        <div class="flex flex-col text-sm text-center">
          <span class="font-medium text-gray-900">{{ item.quantity || 0 }} шт</span>
          <div class="text-xs text-gray-500">
            <span v-if="item.quantity_received > 0" class="text-green-600">
              Отримано: {{ item.quantity_received }}
            </span>
          </div>
        </div>
      </template>

      <template #column-created_at="{ item }">
        <span class="text-sm text-gray-700">
          {{ formatDateTime(item.created_at) }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<script>
import DataTable from '../common/DataTable.vue'

export default {
  name: 'PurchaseOrdersView',
  components: {
    DataTable
  },
  props: {
    user: Object
  },
  data() {
    return {
      headers: [
        { text: 'Номер замовлення', value: 'purchaseorder_number', sortable: true, width: 150 },
        { text: 'Постачальник', value: 'vendor_name', sortable: true },
        { text: 'Дата замовлення', value: 'date', sortable: true, width: 140 },
        { text: 'Сума', value: 'total', sortable: true, width: 120 },
        { text: 'Кількість', value: 'quantity', sortable: true, width: 110 },
        { text: 'Статус', value: 'status', sortable: true, width: 120 },
        { text: 'Створено', value: 'created_at', sortable: true, width: 150 }
      ],
      tableActions: {
        view: {
          icon: 'fas fa-eye',
          tooltip: 'Переглянути'
        },
        edit: {
          icon: 'fas fa-edit',
          tooltip: 'Редагувати'
        },
        delete: {
          icon: 'fas fa-trash',
          tooltip: 'Видалити',
          confirmMessage: 'Ви впевнені, що хочете видалити це замовлення на покупку?'
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
      
      return `${day}.${month}.${year}`
    },
    formatDateTime(dateString) {
      if (!dateString) return '-'
      
      const date = new Date(dateString)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      
      return `${day}.${month}.${year} ${hours}:${minutes}`
    },
    getStatusLabel(status) {
      const labels = {
        'draft': 'Чернетка',
        'confirmed': 'Підтверджено',
        'open': 'Відкрито',
        'closed': 'Закрито',
        'void': 'Скасовано',
        'cancelled': 'Відмінено',
        'issued': 'Виставлено',
        'received': 'Отримано'
      }
      return labels[status] || status || 'Не вказано'
    },
    handleAction(payload) {
      const { type, item } = payload
      
      switch(type) {
        case 'view':
          this.handleView(item)
          break
        case 'edit':
          this.handleEdit(item)
          break
        case 'delete':
          this.handleDelete(item)
          break
      }
    },
    handleView(item) {
      console.log('Переглянути замовлення на покупку:', item)
      alert(`Перегляд замовлення на покупку: ${item.purchaseorder_number}`)
    },
    handleEdit(item) {
      console.log('Редагувати замовлення на покупку:', item)
      alert(`Редагування замовлення на покупку: ${item.purchaseorder_number}`)
    },
    handleDelete(item) {
      console.log('Видалити замовлення на покупку:', item)
      alert(`Замовлення на покупку "${item.purchaseorder_number}" буде видалено`)
    }
  }
}
</script>

<style scoped>
.purchase-orders-view {
  padding: 0;
}
</style>

