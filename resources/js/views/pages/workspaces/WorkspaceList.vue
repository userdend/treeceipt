<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import { useWorkspaceStore } from '@/plugins/stores/workspace'
import axios from 'axios'
import { ref, watch } from 'vue'

const { success, error } = useSnackbar()

const workspaceStore = useWorkspaceStore()

const workspaces = ref([])
const totalWorkspaces = ref(0)
const loading = ref(false)

const isDeleteDialogOpen = ref(false)
const workspaceToDelete = ref(null)
const isDeleting = ref(false)

const headers = [
  { title: 'Type', key: 'type' },
  { title: 'Name', key: 'name' },
  { title: 'Description', key: 'description' },
  { title: 'Tax Identification Number', key: 'tax_no' },
  { title: 'Business Registration Number', key: 'registration_no' },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [], // e.g. [{ key: 'receipt_date', order: 'desc' }]
})

const loadWorkspaces = async () => {
  loading.value = true
  try {
    const { page, itemsPerPage, sortBy } = options.value

    const response = await axios.get('/api/workspaces/list', {
      params: {
        page,
        itemsPerPage,
        sortBy: sortBy[0]?.key ?? null,
        sortOrder: sortBy[0]?.order ?? null,
      },
    })

    workspaces.value = response.data.data ?? []
    totalWorkspaces.value = response.data.total
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    loading.value = false
  }
}

const onUpdateOptions = newOptions => {
  options.value = newOptions
}

// --- Delete handlers ---
const openDeleteDialog = item => {
  workspaceToDelete.value = item
  isDeleteDialogOpen.value = true
}

const confirmDelete = async () => {
  if (!workspaceToDelete.value) return

  isDeleting.value = true
  try {
    const response = await axios.delete(`/api/workspaces/data/${workspaceToDelete.value.id}`)

    workspaces.value = workspaces.value.filter(r => r.id !== workspaceToDelete.value.id)

    isDeleteDialogOpen.value = false
    workspaceToDelete.value = null
    success(response.data.message)
    loadWorkspaces()
    workspaceStore.fetchWorkspaces()
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    isDeleting.value = false
  }
}

watch(options, loadWorkspaces, { deep: true, immediate: true })
</script>

<template>
  <VDataTableServer
    :headers="headers"
    :items="workspaces"
    :items-length="totalWorkspaces"
    :loading="loading"
    :items-per-page="options.itemsPerPage"
    :items-per-page-options="[
      { value: 5, title: '5' },
      { value: 10, title: '10' },
      { value: 25, title: '25' },
      { value: 50, title: '50' },
    ]"
    item-value="id"
    @update:options="onUpdateOptions"
  >
    <template #item.type="{ item }">
      {{ item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) : 'N/A' }}
    </template>

    <template #item.name="{ item }">
      {{ item.name ?? 'N/A' }}
    </template>

    <template #item.description="{ item }">
      {{ item.description ?? 'N/A' }}
    </template>

    <template #item.tax_no="{ item }">
      {{ item.tax_no ?? 'N/A' }}
    </template>

    <template #item.registration_no="{ item }">
      {{ item.registration_no ?? 'N/A' }}
    </template>

    <template #item.actions="{ item }">
      <div class="d-flex justify-center flex-nowrap">
        <VBtn
          icon="ri-pencil-line"
          variant="outlined"
          color="secondary"
          class="me-2"
          :to="{ name: 'workspace-edit', params: { id: item.id } }"
        />
        <VBtn
          color="error"
          variant="outlined"
          icon="ri-delete-bin-line"
          @click="openDeleteDialog(item)"
        />
      </div>
    </template>

    <template #no-data>
      <div class="pa-6 text-center">
        <VIcon
          icon="ri-artboard-2-line"
          size="48"
          class="mb-2"
        />
        <div>No workspaces found.</div>
        <small>Create your first workspace to get started.</small>
      </div>
    </template>
  </VDataTableServer>

  <!-- Permanent delete confirmation dialog -->
  <ConfirmDialog
    v-model="isDeleteDialogOpen"
    title="Delete Workspace"
    :loading="isDeleting"
    confirm-text="Delete"
    @confirm="confirmDelete"
  >
    Are you sure you want to delete <strong>{{ workspaceToDelete?.name }}</strong
    >? All related receipts will also be deleted.
  </ConfirmDialog>
</template>
