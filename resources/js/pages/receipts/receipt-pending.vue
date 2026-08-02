<script setup>
import ReceiptErrorList from '@/views/pages/receipts/ReceiptErrorList.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Pending Receipt">
          <VCardText>
            <ReceiptErrorList />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
