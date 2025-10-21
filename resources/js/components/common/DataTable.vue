<template>
  <div class="data-table-wrapper">
    <div v-if="!loading && !error" class="mb-3 flex items-center justify-between gap-3">
      <div class="text-sm text-gray-500 font-medium">
        {{ totalLabel }}: {{ totalItems }}
      </div>
      <div class="flex items-center gap-2 flex-1 justify-end">
        <input
          v-if="searchable"
          v-model="searchValue"
          type="text"
          :placeholder="searchPlaceholder"
          class="px-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 w-64"
        />
        <slot name="actions"></slot>
      </div>
    </div>

    <div v-if="loading" class="text-center py-6">
      <div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-solid border-blue-500 border-r-transparent"></div>
      <p class="mt-2 text-sm text-gray-500">Завантаження...</p>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-sm">
      <p>{{ error }}</p>
    </div>

    <div v-else class="bg-white border border-gray-200 rounded">
      <EasyDataTable
        :headers="computedHeaders"
        :items="items"
        :rows-per-page="rowsPerPage"
        alternating
        buttons-pagination
        :search-field="searchFields"
        :search-value="searchValue"
        theme-color="#3b82f6"
        table-class-name="customize-table"
      >
        <template
          v-for="column in customColumns"
          :key="column"
          #[`item-${column}`]="item"
        >
          <slot :name="`column-${column}`" :item="item">
            {{ item[column] }}
          </slot>
        </template>

        <!-- Стовбець дій -->
        <template v-if="hasActions" #item-actions="item">
          <div class="flex items-center gap-2">
            <button
              v-if="actions.edit"
              @click="handleAction('edit', item)"
              class="text-blue-500 hover:text-blue-700 transition-colors p-1"
              :title="actions.edit.tooltip || 'Редагувати'"
            >
              <i :class="actions.edit.icon || 'fas fa-edit'" class="text-sm"></i>
            </button>
            <button
              v-if="actions.view"
              @click="handleAction('view', item)"
              class="text-green-500 hover:text-green-700 transition-colors p-1"
              :title="actions.view.tooltip || 'Переглянути'"
            >
              <i :class="actions.view.icon || 'fas fa-eye'" class="text-sm"></i>
            </button>
            <button
              v-if="actions.delete"
              @click="handleAction('delete', item)"
              class="text-red-500 hover:text-red-700 transition-colors p-1"
              :title="actions.delete.tooltip || 'Видалити'"
            >
              <i :class="actions.delete.icon || 'fas fa-trash'" class="text-sm"></i>
            </button>
            <!-- Кастомні дії -->
            <template v-if="actions.custom">
              <button
                v-for="(customAction, index) in actions.custom"
                :key="index"
                @click="handleAction('custom', item, customAction)"
                :class="customAction.class || 'text-gray-500 hover:text-gray-700 transition-colors p-1'"
                :title="customAction.tooltip"
              >
                <i :class="customAction.icon" class="text-sm"></i>
              </button>
            </template>
          </div>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import '@fortawesome/fontawesome-free/css/all.min.css'

export default {
  name: 'DataTable',
  components: {
    EasyDataTable: Vue3EasyDataTable
  },
  props: { 
    apiEndpoint: {
      type: String,
      required: false
    },
    data: {
      type: Array,
      default: () => []
    },
    headers: {
      type: Array,
      required: true
    },
    actions: {
      type: Object,
      default: () => ({})
    },
    searchable: {
      type: Boolean,
      default: true
    },
    searchFields: {
      type: Array,
      default: () => []
    },
    searchPlaceholder: {
      type: String,
      default: 'Пошук...'
    },
    rowsPerPage: {
      type: Number,
      default: 10
    },
    totalLabel: {
      type: String,
      default: 'Всього записів'
    },
    customColumns: {
      type: Array,
      default: () => []
    },
    autoLoad: {
      type: Boolean,
      default: true
    }
  },
  emits: ['action', 'loaded', 'error'],
  data() {
    return {
      items: [],
      loading: false,
      error: null,
      totalItems: 0,
      searchValue: ''
    }
  },
  computed: {
    hasActions() {
      return Object.keys(this.actions).length > 0
    },
    computedHeaders() {
      const headers = [...this.headers]
      if (this.hasActions) {
        headers.push({
          text: 'Дії',
          value: 'actions',
          sortable: false,
          width: this.getActionsWidth()
        })
      }
      return headers
    }
  },
  mounted() {
    if (this.autoLoad) {
      if (this.apiEndpoint) {
        this.loadData()
      } else if (this.data.length > 0) {
        this.items = this.data
        this.totalItems = this.data.length
      }
    }
  },
  watch: {
    data: {
      handler(newData) {
        if (!this.apiEndpoint && newData) {
          this.items = newData
          this.totalItems = newData.length
        }
      },
      deep: true
    }
  },
  methods: {
    async loadData() {
      if (!this.apiEndpoint) return
      
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(this.apiEndpoint)
        
        if (response.data.items) {
          this.items = response.data.items
          this.totalItems = response.data.total || response.data.items.length
        } else if (response.data.data) {
          this.items = response.data.data
          this.totalItems = response.data.total || response.data.data.length
        } else if (Array.isArray(response.data)) {
          this.items = response.data
          this.totalItems = response.data.length
        }
        
        this.$emit('loaded', this.items)
      } catch (error) {
        console.error('Error loading data:', error)
        this.error = 'Помилка завантаження даних. Спробуйте пізніше.'
        this.$emit('error', error)
      } finally {
        this.loading = false
      }
    },
    handleAction(actionType, item, customAction = null) {
      if (actionType === 'delete' && this.actions.delete?.confirm !== false) {
        const confirmMessage = this.actions.delete?.confirmMessage || 
          `Ви впевнені, що хочете видалити цей запис?`
        
        if (!confirm(confirmMessage)) {
          return
        }
      }
      
      if (actionType === 'custom' && customAction) {
        this.$emit('action', {
          type: customAction.name,
          item,
          action: customAction
        })
      } else {
        this.$emit('action', {
          type: actionType,
          item
        })
      }
    },
    getActionsWidth() {
      let count = 0
      if (this.actions.edit) count++
      if (this.actions.view) count++
      if (this.actions.delete) count++
      if (this.actions.custom) count += this.actions.custom.length
      
      return Math.max(count * 40, 80)
    },
    refresh() {
      this.loadData()
    }
  }
}
</script>

<style scoped>
.data-table-wrapper {
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
}

.customize-table {
  width: 100%;
  min-width: 600px;
}

:deep(.customize-table) {
  --easy-table-border: 1px solid #e5e7eb;
  --easy-table-header-background-color: #ffffff;
  --easy-table-header-font-color: #4b5563;
  --easy-table-body-row-background-color: #ffffff;
  --easy-table-body-row-hover-background-color: #f9fafb;
  --easy-table-body-row-font-size: 0.875rem;
  --easy-table-header-font-size: 0.875rem;
}

:deep(.customize-table th) {
  font-weight: 600;
  font-size: 0.8125rem;
  padding: 0.5rem 0.75rem !important;
  border-bottom: 2px solid #e5e7eb;
}

:deep(.customize-table td) {
  padding: 0.5rem 0.75rem !important;
  font-size: 0.875rem;
}

:deep(.customize-table tbody tr) {
  border-bottom: 1px solid #f3f4f6;
}

:deep(.customize-table tbody tr:last-child) {
  border-bottom: none;
}

:deep(.vue3-easy-data-table__main) {
  border-radius: 0.375rem;
  overflow: hidden;
  border: none;
}

:deep(.vue3-easy-data-table__footer) {
  padding: 0.5rem 0.75rem;
  border-top: 1px solid #e5e7eb;
}

:deep(.pagination__rows-per-page) {
  font-size: 0.8125rem;
}

:deep(.pagination__items-index) {
  font-size: 0.8125rem;
}

.data-table-wrapper::-webkit-scrollbar {
  height: 8px;
}

.data-table-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.data-table-wrapper::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.data-table-wrapper::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>

