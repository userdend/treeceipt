<script setup>
import ReceiptDetails from '@/views/pages/receipts/ReceiptDetails.vue'
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
            <ReceiptDetails />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
