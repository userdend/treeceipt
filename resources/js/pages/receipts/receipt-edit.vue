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
            <ReceiptEdit />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
