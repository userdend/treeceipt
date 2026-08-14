<script setup>
import ExportList from '@/views/pages/exports/ExportList.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Export List">
          <VCardText>
            <VAlert
              type="info"
              class="mb-4"
            >
              Showing all workspace exports. You can download the exported files once they are ready.
            </VAlert>
            <ExportList />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
