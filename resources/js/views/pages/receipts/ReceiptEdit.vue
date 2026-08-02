<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const { success, error } = useSnackbar()

const route = useRoute()
const receiptId = route.params.id

const receipt = ref({
  merchant: '',
  receiptDate: '',
  currency: '',
  total: 0,
  items: [],
})

const loading = ref(false)
const saving = ref(false)
const errors = ref({})

const getReceipt = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/receipts/data/${receiptId}`)

    const data = response.data

    receipt.value = {
      ...data,
      receiptDate: data.receipt_date?.slice(0, 10),
    }
  } catch (err) {
    error('Failed to load receipt. Please contact support.')
  } finally {
    loading.value = false
  }
}

const saveReceipt = async () => {
  saving.value = true
  errors.value = {}
  try {
    const response = await axios.put(`/api/receipts/data/${receiptId}`, receipt.value)

    success(response.data.message)
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
      error('Something went wrong. Please check your input.')

      return
    }
    error('Failed to update receipt. Please contact support.')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  getReceipt()
})
</script>

<template>
  <VProgressCircular
    v-if="loading"
    indeterminate
    class="d-flex mx-auto my-8"
  />
  <VForm
    v-else
    @submit.prevent="saveReceipt"
  >
    <VRow>
      <!-- 👉 Merchant -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="merchant">Merchant</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="merchant"
              v-model="receipt.merchant"
              placeholder="Merchant name"
              persistent-placeholder
              :error-messages="errors.merchant"
            />
          </VCol>
        </VRow>
      </VCol>

      <!-- 👉 Date -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="receiptDate">Date</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="receiptDate"
              v-model="receipt.receiptDate"
              type="date"
              persistent-placeholder
              :error-messages="errors.receiptDate"
            />
          </VCol>
        </VRow>
      </VCol>

      <!-- 👉 Currency -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="currency">Currency</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="currency"
              v-model="receipt.currency"
              placeholder="MYR"
              persistent-placeholder
              :error-messages="errors.currency"
            />
          </VCol>
        </VRow>
      </VCol>

      <!-- 👉 Total -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="total">Total</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="total"
              v-model="receipt.total"
              type="number"
              persistent-placeholder
              :error-messages="errors.total"
            />
          </VCol>
        </VRow>
      </VCol>

      <!-- 👉 Items -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label>Items</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <div
              v-for="(item, index) in receipt.items"
              :key="index"
              class="mb-2"
            >
              <VRow>
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="item.name"
                    label="Item"
                  />
                </VCol>
                <VCol
                  cols="6"
                  md="3"
                >
                  <VTextField
                    v-model="item.quantity"
                    type="number"
                    label="Qty"
                  />
                </VCol>
                <VCol
                  cols="6"
                  md="3"
                >
                  <VTextField
                    v-model="item.price"
                    type="number"
                    label="Price"
                  />
                </VCol>
              </VRow>
            </div>
          </VCol>
        </VRow>
      </VCol>

      <!-- 👉 Submit and reset button -->
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          />
          <VCol
            cols="12"
            md="9"
          >
            <VBtn
              :loading="saving"
              :disabled="saving"
              type="submit"
              class="me-4"
            >
              Save Receipt
            </VBtn>
            <VBtn
              color="secondary"
              variant="outlined"
              type="reset"
              :to="{ name: 'receipt-list' }"
            >
              Back
            </VBtn>
          </VCol>
        </VRow>
      </VCol>
    </VRow>
  </VForm>
</template>
