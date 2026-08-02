<script setup>
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import { useSnackbar } from '@/composables/useSnackbar'
import { useWorkspaceStore } from '@/plugins/stores/workspace'
import { formatDate } from '@/utils/formatDate'
import axios from 'axios'
import { ref, watch } from 'vue'

const { success, error } = useSnackbar()

const receipts = ref([])
const totalReceipts = ref(0)
const loading = ref(false)
const workspaceStore = useWorkspaceStore()

const isDeleteDialogOpen = ref(false)
const receiptToDelete = ref(null)
const isDeleting = ref(false)

const isRestoreDialogOpen = ref(false)
const receiptToRestore = ref(null)
const isRestoring = ref(false)

const headers = [
  { title: 'Merchant', key: 'merchant' },
  { title: 'Currency', key: 'currency' },
  { title: 'Total', key: 'total' },
  { title: 'Date', key: 'receipt_date' },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [], // e.g. [{ key: 'receipt_date', order: 'desc' }]
})

const loadReceipts = async () => {
  loading.value = true
  try {
    const { page, itemsPerPage, sortBy } = options.value

    const response = await axios.get('/api/receipts/bin', {
      params: {
        workspaceId: workspaceStore.currentWorkspaceId,
        page,
        itemsPerPage,
        sortBy: sortBy[0]?.key ?? null,
        sortOrder: sortBy[0]?.order ?? null,
      },
    })

    receipts.value = response.data.data ?? []
    totalReceipts.value = response.data.total
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
  receiptToDelete.value = item
  isDeleteDialogOpen.value = true
}

const confirmDelete = async () => {
  if (!receiptToDelete.value) return

  isDeleting.value = true
  try {
    const response = await axios.delete(`/api/receipts/data/${receiptToDelete.value.id}/force`)

    receipts.value = receipts.value.filter(r => r.id !== receiptToDelete.value.id)

    isDeleteDialogOpen.value = false
    receiptToDelete.value = null
    success(response.data.message)
    loadReceipts()
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    isDeleting.value = false
  }
}

// --- Restore handlers ---
const openRestoreDialog = item => {
  receiptToRestore.value = item
  isRestoreDialogOpen.value = true
}

const confirmRestore = async () => {
  if (!receiptToRestore.value) return

  isRestoring.value = true
  try {
    const response = await axios.patch(`/api/receipts/data/${receiptToRestore.value.id}/restore`)

    receipts.value = receipts.value.filter(r => r.id !== receiptToRestore.value.id)

    isRestoreDialogOpen.value = false
    receiptToRestore.value = null
    success(response.data.message)
    loadReceipts()
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    isRestoring.value = false
  }
}

watch(options, loadReceipts, { deep: true })

watch(
  () => workspaceStore.currentWorkspaceId,
  newWorkspaceId => {
    if (newWorkspaceId) options.value = { ...options.value, page: 1 }
  },
)
</script>

<template>
  <VDataTableServer
    :headers="headers"
    :items="receipts"
    :items-length="totalReceipts"
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
    <template #item.merchant="{ item }">
      {{ item.merchant ?? '-' }}
    </template>

    <template #item.currency="{ item }">
      {{ item.currency ?? 'MYR' }}
    </template>

    <template #item.total="{ item }">
      {{ item.total ?? '0.00' }}
    </template>

    <template #item.receipt_date="{ item }">
      {{ formatDate(item.receipt_date) }}
    </template>

    <template #item.actions="{ item }">
      <div class="d-flex justify-center flex-nowrap">
        <VBtn
          icon="ri-device-recover-fill"
          variant="outlined"
          color="info"
          class="me-2"
          @click="openRestoreDialog(item)"
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
          icon="ri-file-list-3-line"
          size="48"
          class="mb-2"
        />
        <div>No receipts found.</div>
        <small>Upload your first receipt to get started.</small>
      </div>
    </template>
  </VDataTableServer>

  <!-- Restore confirmation dialog -->
  <ConfirmDialog
    v-model="isRestoreDialogOpen"
    title="Restore Receipt"
    :loading="isRestoring"
    confirm-text="Restore"
    confirm-color="info"
    @confirm="confirmRestore"
  >
    Are you sure you want to restore receipt <strong>{{ receiptToRestore?.merchant }}</strong
    >? It will be moved back to your active receipts.
  </ConfirmDialog>

  <!-- Permanent delete confirmation dialog -->
  <ConfirmDialog
    v-model="isDeleteDialogOpen"
    title="Delete Receipt Permanently"
    :loading="isDeleting"
    confirm-text="Delete Permanently"
    @confirm="confirmDelete"
  >
    Are you sure you want to permanently delete receipt <strong>{{ receiptToDelete?.merchant }}</strong
    >? <strong>This action cannot be undone</strong> — the receipt and its items will be permanently removed.
  </ConfirmDialog>
</template>
