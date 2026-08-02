<script setup>
import WorkspaceCreate from '@/views/pages/workspaces/WorkspaceCreate.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VCard title="Create New Workspace">
          <VCardText>
            <WorkspaceCreate />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
