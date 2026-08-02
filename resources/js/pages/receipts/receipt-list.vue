<script setup>
import ReceiptList from '@/views/pages/receipts/ReceiptList.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Receipt List">
          <VCardText>
            <ReceiptList />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
