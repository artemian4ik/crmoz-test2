<template>
  <div class="customers-view">
    <DataTable
      ref="dataTable"
      api-endpoint="/api/customers"
      :headers="headers"
      :search-fields="['contact_name', 'company_name', 'email', 'phone', 'contact_id']"
      search-placeholder="Пошук по імені, компанії, email, телефону..."
      total-label="Всього клієнтів"
      :custom-columns="['contact_name', 'contact_type', 'status', 'outstanding_receivable_amount', 'phone', 'email', 'created_at']"
      :actions="tableActions"
      @action="handleAction"
    >
      <template #column-contact_name="{ item }">
        <div class="flex flex-col">
          <span class="font-semibold text-gray-900">{{ item.contact_name }}</span>
          <span v-if="item.company_name" class="text-xs text-gray-500">{{ item.company_name }}</span>
        </div>
      </template>

      <template #column-contact_type="{ item }">
        <span 
          :class="{
            'bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-medium': item.contact_type === 'customer',
            'bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-medium': item.contact_type === 'vendor',
            'bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs font-medium': item.contact_type === 'both',
            'bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium': !item.contact_type
          }"
        >
          {{ getContactTypeLabel(item.contact_type) }}
        </span>
      </template>

      <template #column-status="{ item }">
        <span 
          :class="{
            'table-cell bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'active',
            'table-cell bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'inactive',
            'table-cell bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-medium': item.status === 'wait',
            'table-cell bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-medium': !item.status
          }"
        >
          {{ getStatusLabel(item.status) }}
        </span>
      </template>

      <template #column-outstanding_receivable_amount="{ item }">
        <span 
          :class="{
            'font-medium text-red-600': item.outstanding_receivable_amount > 0,
            'font-medium text-green-600': item.outstanding_receivable_amount < 0,
            'font-medium text-gray-600': item.outstanding_receivable_amount === 0
          }"
        >
          {{ item.outstanding_receivable_amount ? Number(item.outstanding_receivable_amount).toFixed(2) : '0.00' }} 
          {{ item.currency_code || 'UAH' }}
        </span>
      </template>

      <template #column-phone="{ item }">
        <div class="flex flex-col text-sm">
          <span v-if="item.phone">{{ item.phone }}</span>
          <span v-if="item.mobile" class="text-xs text-gray-500">{{ item.mobile }}</span>
          <span v-if="!item.phone && !item.mobile" class="text-gray-400">-</span>
        </div>
      </template>

      <template #column-email="{ item }">
        <span v-if="item.email" class="text-sm text-gray-700">
          {{ item.email }}
        </span>
        <span v-else class="text-gray-400">-</span>
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
  name: 'CustomersView',
  components: {
    DataTable
  },
  props: {
    user: Object
  },
  data() {
    return {
      headers: [
        { text: 'ID Контакту', value: 'contact_id', sortable: true, width: 150 },
        { text: 'Ім\'я / Компанія', value: 'contact_name', sortable: true },
        { text: 'Тип', value: 'contact_type', sortable: true, width: 120 },
        { text: 'Статус', value: 'status', sortable: true, width: 100 },
        { text: 'Email', value: 'email', sortable: true, width: 200 },
        { text: 'Телефон', value: 'phone', sortable: true, width: 150 },
        { text: 'Борг', value: 'outstanding_receivable_amount', sortable: true, width: 130 },
        { text: 'Дата створення', value: 'created_at', sortable: true, width: 150 }
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
          confirmMessage: 'Ви впевнені, що хочете видалити цього клієнта?'
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
    getContactTypeLabel(type) {
      const labels = {
        'customer': 'Клієнт',
        'vendor': 'Постачальник',
        'both': 'Обидва'
      }
      return labels[type] || type || 'Не вказано'
    },
    getStatusLabel(status) {
      const labels = {
        'wait': 'Очікується імпорт',
        'active': 'Активний',
        'inactive': 'Неактивний'
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
      console.log('Переглянути клієнта:', item)
      alert(`Перегляд клієнта: ${item.contact_name} (ID: ${item.id})`)
    },
    handleEdit(item) {
      console.log('Редагувати клієнта:', item)
      alert(`Редагування клієнта: ${item.contact_name} (ID: ${item.id})`)
    },
    handleDelete(item) {
      console.log('Видалити клієнта:', item)
      alert(`Клієнт "${item.contact_name}" буде видалено`)
    }
  }
}
</script>

<style scoped>
.customers-view {
  padding: 0;
}
</style>

