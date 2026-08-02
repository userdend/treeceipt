<script setup>
import ReceiptBin from '@/views/pages/receipts/ReceiptBin.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Receipt Bin">
          <VCardText>
            <ReceiptBin />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
