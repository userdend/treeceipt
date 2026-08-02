<script setup>
import WorkspaceEdit from '@/views/pages/workspaces/WorkspaceEdit.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <VCard title="Edit Workspace">
          <VCardText>
            <WorkspaceEdit />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
