<script setup>
import WorkspaceList from '@/views/pages/workspaces/WorkspaceList.vue'
</script>

<template>
  <div>
    <VRow>
      <VCol
        cols="12"
        md="12"
      >
        <VCard title="Workspace List">
          <VCardText>
            <WorkspaceList />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>
