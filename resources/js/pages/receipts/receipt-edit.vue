<script setup>
import ReceiptEdit from '@/views/pages/receipts/ReceiptEdit.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Receipt Edit Form">
          <VCardText>
            <VAlert
              type="info"
              class="mb-4"
            >
              The total price will not be calculated automatically. Please ensure that you enter the correct total price
              for the receipt.
            </VAlert>
            <ReceiptEdit />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
