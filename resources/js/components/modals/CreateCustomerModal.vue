<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 overflow-y-auto" style="z-index: 9999;">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity backdrop-blur bg-opacity-75" @click="$emit('close')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block modal-shadow align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative" style="z-index: 10000;">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Створити нового клієнта</h3>
            <button
              type="button"
              @click="$emit('close')"
              class="text-gray-400 hover:text-gray-500"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="handleSubmit">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ім'я *
                </label>
                <input
                  v-model="customer.contact_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': errors.contact_name }"
                  placeholder="Іван Петренко"
                />
                <p v-if="errors.contact_name" class="mt-1 text-sm text-red-600">
                  {{ errors.contact_name }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Компанія
                </label>
                <input
                  v-model="customer.company_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="ТОВ Компанія"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Email
                </label>
                <input
                  v-model="customer.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="email@example.com"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Телефон
                </label>
                <input
                  v-model="customer.phone"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="+380 XX XXX XXXX"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Мобільний
                </label>
                <input
                  v-model="customer.mobile"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="+380 XX XXX XXXX"
                />
              </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
              <button
                type="button"
                @click="$emit('close')"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Скасувати
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ loading ? 'Створення...' : 'Створити клієнта' }}
              </button>
            </div>
          </form>
        </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script>
export default {
    name: 'CreateCustomerModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        initialName: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            customer: {
                contact_name: '',
                company_name: '',
                email: '',
                phone: '',
                mobile: '',
                contact_type: 'customer'
            },
            errors: {},
            loading: false
        }
    },
    watch: {
        show(newVal) {
            if (newVal) {
                this.customer.contact_name = this.initialName
                this.errors = {}
            }
        }
    },
    methods: {
        validate() {
            this.errors = {}
            
            if (!this.customer.contact_name) {
                this.errors.contact_name = 'Введіть ім\'я клієнта'
            }
            
            return Object.keys(this.errors).length === 0
        },
        async handleSubmit() {
            if (!this.validate()) {
                return
            }
        
            this.loading = true
        
            try {
                const response = await fetch('/api/customers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.customer)
                })
                
                const data = await response.json()
                
                if (data.success) {
                    this.$emit('created', data.data)
                    this.resetForm()
                } else {
                    alert('Помилка створення клієнта: ' + (data.message || 'Невідома помилка'))
                }
            } catch (error) {
                console.error('Error creating customer:', error)
                alert('Помилка створення клієнта')
            } finally {
                this.loading = false
            }
        },
        resetForm() {
            this.customer = {
                contact_name: '',
                company_name: '',
                email: '',
                phone: '',
                mobile: '',
                contact_type: 'customer'
            }
            this.errors = {}
        }
    }
}
</script>

