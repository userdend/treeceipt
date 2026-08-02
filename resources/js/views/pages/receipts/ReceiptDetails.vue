<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const receiptId = route.params.id

const receipt = ref({
  merchant: '',
  receiptDate: '',
  currency: '',
  total: 0,
  imageUrl: '',
  items: [],
})

const loading = ref(false)
const imageLoadFailed = ref(false)

const getReceipt = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/receipts/data/${receiptId}`)

    const data = response.data

    receipt.value = {
      ...data,
      receiptDate: data.receipt_date?.slice(0, 10),
      imageUrl: data.image_url,
    }
  } catch (error) {
    router.push({ name: 'error' })
  } finally {
    loading.value = false
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
  <VRow v-else>
    <VCol
      cols="12"
      md="5"
    >
      <VCard variant="outlined">
        <VImg
          v-if="receipt.imageUrl"
          :src="receipt.imageUrl"
          max-height="600"
          contain
          class="rounded"
          @error="imageLoadFailed = true"
        >
          <template #placeholder>
            <VRow
              class="fill-height ma-0"
              align="center"
              justify="center"
            >
              <VProgressCircular
                indeterminate
                color="grey-lighten-4"
              />
            </VRow>
          </template>
        </VImg>

        <!-- No image at all, or image URL failed to load -->
        <VCardText
          v-if="!receipt.imageUrl || imageLoadFailed"
          class="d-flex flex-column align-center justify-center text-medium-emphasis"
          style="min-height: 300px"
        >
          <VIcon
            icon="ri-image-line"
            size="48"
            class="mb-2"
          />
          <span>No image available</span>
        </VCardText>
      </VCard>
    </VCol>
    <VCol
      cols="12"
      md="7"
    >
      <VForm @submit.prevent="saveReceipt">
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
                  variant="filled"
                  readonly
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
                  variant="filled"
                  readonly
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
                  variant="filled"
                  readonly
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
                  variant="filled"
                  readonly
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
                        variant="filled"
                        readonly
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
                        variant="filled"
                        readonly
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
                        variant="filled"
                        readonly
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
    </VCol>
  </VRow>
</template>
