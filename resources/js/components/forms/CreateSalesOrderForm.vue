<template>
  <div class="create-sales-order-form">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Створити замовлення на продаж</h2>
    </div>

    <form @submit.prevent="handleSubmit">
      <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Інформація про клієнта</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Клієнт *
            </label>
            <div class="relative">
              <input
                v-model="customerSearch"
                @input="searchCustomers"
                @focus="showCustomerDropdown = true"
                type="text"
                placeholder="Почніть вводити ім'я клієнта..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.customer }"
              />
              
              <div 
                v-if="showCustomerDropdown"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
              >
                <div
                  v-for="customer in filteredCustomers"
                  :key="customer.id"
                  @click="selectCustomer(customer)"
                  class="px-4 py-2 hover:bg-blue-50 cursor-pointer"
                >
                  <div class="font-medium">{{ customer.contact_name }}</div>
                  <div class="text-sm text-gray-500" v-if="customer.company_name">
                    {{ customer.company_name }}
                  </div>
                </div>
                
                <div
                  v-if="customerSearch.length >= 2 && filteredCustomers.length === 0"
                  @click="openCreateCustomerModal"
                  class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-t border-gray-200"
                >
                  <div class="flex items-center gap-2 text-blue-600 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                    </svg>
                    Додати нового клієнта "{{ customerSearch }}"
                  </div>
                </div>
              </div>
            </div>
            <p v-if="errors.customer" class="mt-1 text-sm text-red-600">{{ errors.customer }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="customer@example.com"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Дата замовлення *
            </label>
            <input
              v-model="form.date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.date }"
            />
            <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Дата відвантаження
            </label>
            <input
              v-model="form.shipment_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Reference #
            </label>
            <input
              v-model="form.reference_number"
              type="text"
              placeholder="REF-12345"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>

      <CreateCustomerModal
        :show="showCreateCustomerModal"
        :initial-name="customerSearch"
        @close="showCreateCustomerModal = false"
        @created="handleCustomerCreated"
      />

      <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Товари</h3>
          <button
            type="button"
            @click="addLineItem"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Додати товар
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Товар</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Опис</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase w-24">Кількість</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase w-32">Ціна</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase w-24">Знижка</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase w-32">Податок</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase w-32">Сума</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase w-24">На складі</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase w-20"></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, index) in form.line_items"
                :key="index"
                class="border-b border-gray-200 hover:bg-gray-50"
              >
                <td class="px-4 py-3">
                  <div class="relative">
                    <input
                      v-model="item.itemSearch"
                      @input="searchItems(index)"
                      @focus="item.showDropdown = true"
                      type="text"
                      placeholder="Почніть вводити назву..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                      :class="{ 'border-red-500': !item.item_id && submitted }"
                    />
                    
                    <div 
                      v-if="item.showDropdown && item.filteredItems && item.filteredItems.length > 0"
                      class="fixed z-50 w-[300px] mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-96 overflow-auto"
                    >
                      <div
                        v-for="availableItem in item.filteredItems"
                        :key="availableItem.id"
                        @click="selectItem(index, availableItem)"
                        class="px-3 py-2 hover:bg-blue-50 cursor-pointer"
                      >
                        <div class="font-medium text-sm">{{ availableItem.name }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ availableItem.sku || 'N/A' }}</div>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <input
                    v-model="item.description"
                    type="text"
                    placeholder="Опис..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </td>
                <td class="px-4 py-3">
                  <input
                    v-model.number="item.quantity"
                    @input="calculateLineTotal(index)"
                    type="number"
                    min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </td>
                <td class="px-4 py-3">
                  <input
                    v-model.number="item.rate"
                    @input="calculateLineTotal(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </td>
                <td class="px-4 py-3">
                  <input
                    v-model.number="item.discount"
                    @input="calculateLineTotal(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-right focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm text-right">
                    <div class="font-medium">{{ item.tax_name || '-' }}</div>
                    <div class="text-xs text-gray-500">{{ item.tax_percentage }}%</div>
                    <div class="text-xs text-gray-600">{{ formatCurrency(item.tax_amount) }}</div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="text-sm font-semibold text-right">
                    {{ formatCurrency(item.item_total) }}
                  </div>
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    v-if="item.stock_quantity !== null"
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': item.stock_quantity > item.quantity,
                      'bg-yellow-100 text-yellow-800': item.stock_quantity > 0 && item.stock_quantity <= item.quantity,
                      'bg-red-100 text-red-800': item.stock_quantity === 0
                    }"
                  >
                    {{ item.stock_quantity }}
                  </span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <button
                    type="button"
                    @click="removeLineItem(index)"
                    class="text-red-600 hover:text-red-800"
                    :disabled="form.line_items.length === 1"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-6 flex justify-end">
          <div class="w-80 space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Проміжний підсумок:</span>
              <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Загальний податок:</span>
              <span class="font-medium">{{ formatCurrency(totalTax) }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-2">
              <span>Всього:</span>
              <span class="text-blue-600">{{ formatCurrency(total) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="outOfStockItems.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          Товари не на складі - необхідні замовлення на закупівлю
        </h3>

        <table class="w-full">
          <thead>
            <tr class="bg-yellow-100">
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-700">Товар</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Потрібно</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">На складі</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-700">Замовити</th>
              <th class="px-4 py-2 text-center text-xs font-medium text-gray-700">Створити PO?</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in outOfStockItems"
              :key="index"
              class="border-b border-yellow-200"
            >
              <td class="px-4 py-3">
                <div class="font-medium text-sm">{{ item.name }}</div>
                <div class="text-xs text-gray-600">{{ item.sku }}</div>
              </td>
              <td class="px-4 py-3 text-right font-medium">{{ item.quantity_needed }}</td>
              <td class="px-4 py-3 text-right">{{ item.stock_quantity }}</td>
              <td class="px-4 py-3 text-right font-semibold text-red-600">
                {{ item.quantity_to_order }}
              </td>
              <td class="px-4 py-3 text-center">
                <input
                  v-model="item.create_po"
                  type="checkbox"
                  class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center">
        <button
          type="button"
          @click="$emit('cancel')"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Скасувати
        </button>

        <div class="flex gap-3">
          <button
            type="button"
            @click="saveAsDraft"
            class="px-6 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50"
          >
            Зберегти як чернетку
          </button>
          
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Створити замовлення
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import CreateCustomerModal from '../modals/CreateCustomerModal.vue'

export default {
  name: 'CreateSalesOrderForm',
  components: {
    CreateCustomerModal
  },
  data() {
    return {
      form: {
        customer_id: null,
        customer_name: '',
        email: '',
        salesorder_number: '',
        reference_number: '',
        date: new Date().toISOString().split('T')[0],
        shipment_date: '',
        line_items: [this.createEmptyLineItem()],
        currency_code: 'UAH',
        status: 'confirmed'
      },
      customerSearch: '',
      customers: [],
      filteredCustomers: [],
      showCustomerDropdown: false,
      showCreateCustomerModal: false,
      items: [],
      errors: {},
      submitted: false,
      loading: false
    }
  },
  computed: {
    subtotal() {
      return this.form.line_items.reduce((sum, item) => {
        const itemSubtotal = (item.rate * item.quantity) - (item.discount || 0)
        return sum + itemSubtotal
      }, 0)
    },
    totalTax() {
      return this.form.line_items.reduce((sum, item) => sum + (item.tax_amount || 0), 0)
    },
    total() {
      return this.subtotal + this.totalTax
    },
    outOfStockItems() {
      return this.form.line_items
        .filter(item => {
          if (!item.item_id || item.stock_quantity === null) return false
          return item.stock_quantity < item.quantity
        })
        .map(item => ({
          ...item,
          quantity_needed: item.quantity,
          quantity_to_order: item.quantity - (item.stock_quantity || 0),
          create_po: false
        }))
    }
  },
  mounted() {
    this.loadCustomers()
    this.loadItems()
  },
  methods: {
    createEmptyLineItem() {
      return {
        item_id: null,
        itemSearch: '',
        name: '',
        description: '',
        quantity: 1,
        rate: 0,
        discount: 0,
        tax_id: null,
        tax_name: '',
        tax_percentage: 0,
        tax_amount: 0,
        item_total: 0,
        stock_quantity: null,
        sku: '',
        showDropdown: false,
        filteredItems: []
      }
    },
    async loadCustomers() {
      try {
        const response = await fetch('/api/customers?perPage=1000')
        const data = await response.json()
        this.customers = data.data || []
      } catch (error) {
        console.error('Error loading customers:', error)
      }
    },
    async loadItems() {
      try {
        const response = await fetch('/api/items?perPage=1000')
        const data = await response.json()
        this.items = data.data || []
      } catch (error) {
        console.error('Error loading items:', error)
      }
    },
    searchCustomers() {
      if (this.customerSearch.length < 2) {
        this.filteredCustomers = []
        return
      }
      
      const search = this.customerSearch.toLowerCase()
      this.filteredCustomers = this.customers.filter(customer => 
        customer.contact_name.toLowerCase().includes(search) ||
        (customer.company_name && customer.company_name.toLowerCase().includes(search)) ||
        (customer.email && customer.email.toLowerCase().includes(search))
      ).slice(0, 10)
    },
    selectCustomer(customer) {
      this.form.customer_id = customer.contact_id
      this.form.customer_name = customer.contact_name
      this.form.email = customer.email || ''
      this.customerSearch = customer.contact_name
      this.showCustomerDropdown = false
      this.filteredCustomers = []
    },
    openCreateCustomerModal() {
      this.showCustomerDropdown = false
      this.showCreateCustomerModal = true
    },
    handleCustomerCreated(customer) {
      this.customers.push(customer)
      this.selectCustomer(customer)
      this.showCreateCustomerModal = false
    },
    searchItems(index) {
      const item = this.form.line_items[index]
      
      if (item.itemSearch.length < 1) {
        item.filteredItems = []
        return
      }
      
      const search = item.itemSearch.toLowerCase()
      item.filteredItems = this.items.filter(availableItem =>
        availableItem.name.toLowerCase().includes(search) ||
        (availableItem.sku && availableItem.sku.toLowerCase().includes(search))
      ).slice(0, 10)
    },
    async selectItem(index, selectedItem) {
      const item = this.form.line_items[index]
      
      item.item_id = selectedItem.item_id
      item.name = selectedItem.name
      item.itemSearch = selectedItem.name
      item.rate = parseFloat(selectedItem.rate) || 0
      item.tax_id = selectedItem.tax_id
      item.tax_name = selectedItem.tax_name || ''
      item.tax_percentage = parseFloat(selectedItem.tax_percentage) || 0
      item.sku = selectedItem.sku || ''
      item.description = selectedItem.description || ''
      item.showDropdown = false
      item.filteredItems = []
      
      await this.checkStock(index, selectedItem.item_id)
      this.calculateLineTotal(index)
    },
    async checkStock(index, itemId) {
      try {
        const response = await fetch(`/api/items/${itemId}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
        })
        const data = await response.json()
        
        this.form.line_items[index].stock_quantity = data.stock_on_hand || 0
      } catch (error) {
        console.error('Error checking stock:', error)
        this.form.line_items[index].stock_quantity = 0
      }
    },
    calculateLineTotal(index) {
      const item = this.form.line_items[index]
      
      const subtotal = (item.rate * item.quantity) - (item.discount || 0)
      const taxAmount = (subtotal * item.tax_percentage) / 100
      
      item.tax_amount = taxAmount
      item.item_total = subtotal + taxAmount
    },
    addLineItem() {
      this.form.line_items.push(this.createEmptyLineItem())
    },
    removeLineItem(index) {
      if (this.form.line_items.length > 1) {
        this.form.line_items.splice(index, 1)
      }
    },
    validate() {
      this.errors = {}
      
      if (!this.form.customer_id) {
        this.errors.customer = 'Виберіть клієнта'
      }
      
      if (!this.form.date) {
        this.errors.date = 'Виберіть дату'
      }
      
      if (this.form.line_items.length === 0) {
        this.errors.line_items = 'Додайте хоча б один товар'
      }
      
      return Object.keys(this.errors).length === 0
    },
    async handleSubmit() {
      this.submitted = true
      
      if (!this.validate()) {
        return
      }
      
      this.loading = true
      
      try {
        const orderData = this.prepareOrderData()

        if(this.outOfStockItems.some(item => item.create_po)) {
          return await this.createWithPurchaseOrders();
        }
        
        const response = await fetch('/api/sales-orders', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(orderData)
        })
        
        const data = await response.json()
        
        if (data.success) {
          this.$emit('created', data.data)
          alert('Замовлення успішно створено!')
        } else {
          alert('Помилка створення замовлення: ' + (data.message || 'Невідома помилка'))
        }
      } catch (error) {
        console.error('Error creating order:', error)
        alert('Помилка створення замовлення')
      } finally {
        this.loading = false
      }
    },
    async saveAsDraft() {
      this.form.status = 'draft'
      await this.handleSubmit()
    },
    async createWithPurchaseOrders() {
      this.submitted = true
      
      if (!this.validate()) {
        return
      }
      
      this.loading = true
      
      try {
        const orderData = this.prepareOrderData()
        orderData.create_purchase_orders = true
        orderData.purchase_order_items = this.outOfStockItems.filter(item => item.create_po)
        
        const response = await fetch('/api/sales-orders/with-purchase-orders', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(orderData)
        })
        
        const data = await response.json()
        
        if (data.success) {
          this.$emit('created', data.data)
          alert(`Замовлення створено! Purchase Orders: ${data.purchase_orders_count}`)
        } else {
          alert('Помилка: ' + (data.message || 'Невідома помилка'))
        }
      } catch (error) {
        console.error('Error:', error)
        alert('Помилка створення')
      } finally {
        this.loading = false
      }
    },
    prepareOrderData() {
      return {
        customer_id: this.form.customer_id,
        customer_name: this.form.customer_name,
        email: this.form.email,
        salesorder_number: this.form.salesorder_number,
        reference_number: this.form.reference_number,
        date: this.form.date,
        shipment_date: this.form.shipment_date,
        currency_code: this.form.currency_code,
        status: this.form.status,
        line_items: this.form.line_items.map(item => ({
          item_id: item.item_id,
          name: item.name,
          description: item.description,
          quantity: item.quantity,
          rate: item.rate,
          discount: item.discount,
          tax_id: item.tax_id,
          tax_name: item.tax_name,
          tax_percentage: item.tax_percentage,
          item_total: item.item_total
        })),
        subtotal: this.subtotal,
        total_tax: this.totalTax,
        total: this.total
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('uk-UA', {
        style: 'currency',
        currency: 'UAH',
        minimumFractionDigits: 2
      }).format(value || 0)
    }
  }
}
</script>

<style scoped>

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>

