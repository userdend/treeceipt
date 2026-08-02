<script setup>
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const receiptId = route.params.id

const checkStatus = async () => {
  const response = await axios.get(`/api/receipts/status/${receiptId}`)

  if (response.data.status === 'review') {
    router.push({ name: 'receipt-edit', params: { id: receiptId } })

    return
  }

  if (response.data.status === 'failed') {
    router.push({
      name: 'receipt-replace',
      params: { id: receiptId },
      query: {
        reason: response.data.ocr_error_code,
      },
    })

    return
  }

  setTimeout(checkStatus, 3000)
}

onMounted(() => {
  checkStatus()
})
</script>

<template>
  <div class="d-flex flex-column align-center justify-center text-center pa-8">
    <VProgressCircular
      :size="60"
      color="primary"
      indeterminate
      class="mb-4"
    />

    <h3 class="text-h6 mb-2">Processing your receipt...</h3>

    <p class="text-body-2 mb-1">Extracting receipt information</p>

    <p class="text-body-2 text-medium-emphasis">Please wait...</p>
  </div>
</template>
