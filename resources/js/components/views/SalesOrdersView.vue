<template>
  <div class="sales-orders-view">
    <CreateSalesOrderForm
      v-if="showCreateForm"
      @created="handleOrderCreated"
      @cancel="showCreateForm = false"
    />

    <DataTable
      v-else
      ref="dataTable"
      api-endpoint="/api/sales-orders"
      :headers="headers"
      :search-fields="['salesorder_number', 'reference_number', 'customer_name', 'company_name']"
      search-placeholder="Пошук по номеру, клієнту, компанії..."
      total-label="Всього замовлень"
      :custom-columns="['salesorder_number', 'customer_name', 'status', 'total', 'date', 'quantity', 'completion', 'created_at']"
      :actions="tableActions"
      @action="handleAction"
    >
      <template #actions>
        <button
          @click="handleAddOrder"
          class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm transition-colors whitespace-nowrap flex items-center gap-1.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Створити замовлення
        </button>
      </template>

      <template #column-salesorder_number="{ item }">
        <div class="flex flex-col">
          <span class="font-semibold text-blue-700">{{ item.salesorder_number }}</span>
          <span v-if="item.reference_number" class="text-xs text-gray-500">Ref: {{ item.reference_number }}</span>
        </div>
      </template>

      <template #column-customer_name="{ item }">
        <div class="flex flex-col">
          <span class="font-medium text-gray-900">{{ item.customer_name }}</span>
          <span v-if="item.company_name" class="text-xs text-gray-500">{{ item.company_name }}</span>
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
          <span v-if="item.balance && item.balance > 0" class="text-xs text-red-600">
            Борг: {{ Number(item.balance).toFixed(2) }} {{ item.currency_code || 'UAH' }}
          </span>
        </div>
      </template>

      <template #column-date="{ item }">
        <div class="flex flex-col text-sm">
          <span class="font-medium text-gray-700">{{ formatDate(item.date) }}</span>
          <span v-if="item.shipment_date" class="text-xs text-gray-500">
            Відвант.: {{ formatDate(item.shipment_date) }}
          </span>
        </div>
      </template>

      <template #column-quantity="{ item }">
        <div class="flex flex-col text-sm text-center">
          <span class="font-medium text-gray-900">{{ item.quantity || 0 }} шт</span>
          <div class="text-xs text-gray-500">
            <span v-if="item.quantity_shipped > 0" class="text-green-600">
              Відв: {{ item.quantity_shipped }}
            </span>
            <span v-if="item.quantity_invoiced > 0" class="text-blue-600 ml-1">
              Інв: {{ item.quantity_invoiced }}
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
import CreateSalesOrderForm from '../forms/CreateSalesOrderForm.vue'

export default {
  name: 'SalesOrdersView',
  components: {
    DataTable,
    CreateSalesOrderForm
  },
  props: {
    user: Object,
    openCreateForm: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      showCreateForm: false,
      headers: [
        { text: 'Номер замовлення', value: 'salesorder_number', sortable: true, width: 150 },
        { text: 'Клієнт', value: 'customer_name', sortable: true },
        { text: 'Дата замовлення', value: 'date', sortable: true, width: 140 },
        { text: 'Сума', value: 'total', sortable: true, width: 100 },
        { text: 'Кількість', value: 'quantity', sortable: true, width: 100 },
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
          confirmMessage: 'Ви впевнені, що хочете видалити це замовлення?'
        }
      }
    }
  },
  watch: {
    openCreateForm: {
      handler(newVal) {
        if (newVal) {
          this.showCreateForm = true
          this.$emit('form-opened')
        }
      },
      immediate: true
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
        'cancelled': 'Відмінено'
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
      console.log('Переглянути замовлення:', item)
      alert(`Перегляд замовлення: ${item.salesorder_number}`)
    },
    handleEdit(item) {
      console.log('Редагувати замовлення:', item)
      alert(`Редагування замовлення: ${item.salesorder_number}`)
    },
    handleDelete(item) {
      console.log('Видалити замовлення:', item)
      alert(`Замовлення "${item.salesorder_number}" буде видалено`)
    },
    handleAddOrder() {
      this.showCreateForm = true
    },
    handleOrderCreated(order) {
      this.showCreateForm = false
      if (this.$refs.dataTable) {
        this.$refs.dataTable.refresh()
      }
    }
  }
}
</script>

<style scoped>
.sales-orders-view {
  padding: 0;
}
</style>

