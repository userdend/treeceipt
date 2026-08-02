<script setup>
import ReceiptUploadForm from '@/views/pages/receipts/ReceiptUploadForm.vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const receiptId = route.params.id

const message = computed(() => {
  switch (route.query.reason) {
    case 'multiple_receipts_detected':
      return 'Multiple receipts were detected. Please upload an image containing only one receipt.'

    case 'unreadable_receipt':
      return 'We could not read the receipt. Please upload a clearer image.'

    case 'no_receipt_detected':
      return 'No receipt was detected. Please upload a valid receipt.'

    default:
      return 'Invalid image, please upload a valid image.'
  }
})
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VCard title="Receipt Upload Form">
          <VCardText>
            <VAlert
              v-if="message"
              type="error"
              class="mb-4"
            >
              {{ message }}
            </VAlert>
            <ReceiptUploadForm
              mode="replace"
              :receipt-id="receiptId"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
