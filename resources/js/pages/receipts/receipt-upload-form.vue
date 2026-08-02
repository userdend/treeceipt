<script setup>
import ReceiptUploadForm from '@/views/pages/receipts/ReceiptUploadForm.vue'
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
            <ReceiptUploadForm mode="create" />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
