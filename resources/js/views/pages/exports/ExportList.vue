<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import { formatDate } from '@/utils/formatDate'
import axios from 'axios'
import { ref, watch } from 'vue'

const { success, error } = useSnackbar()

const exports = ref([])
const totalExports = ref(0)
const loading = ref(false)
const isDownloading = ref(false)

const headers = [
  { title: 'File', key: 'file_name' },
  { title: 'Status', key: 'status' },
  { title: 'Total Receipts', key: 'total_receipts' },
  { title: 'Date', key: 'updated_at' },
  { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
]

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [], // e.g. [{ key: 'receipt_date', order: 'desc' }]
})

const pollTimer = ref(null)

const hasRunningExport = () => {
  return exports.value.some(exportItem => ['processing'].includes(exportItem.status))
}

const startPolling = () => {
  if (pollTimer.value) return

  pollTimer.value = setInterval(() => {
    loadExports(true)
  }, 3000)
}

const stopPolling = () => {
  if (pollTimer.value) {
    clearInterval(pollTimer.value)
    pollTimer.value = null
  }
}

const loadExports = async (isPolling = false) => {
  // Separate initial/table loading from background polling
  if (!isPolling) {
    loading.value = true
  }

  try {
    const { page, itemsPerPage, sortBy } = options.value

    const response = await axios.get('/api/exports/list', {
      params: {
        page,
        itemsPerPage,
        sortBy: sortBy[0]?.key ?? null,
        sortOrder: sortBy[0]?.order ?? null,
      },
    })

    exports.value = response.data.data ?? []
    totalExports.value = response.data.total

    // Start/stop polling based on current export status
    if (hasRunningExport()) {
      startPolling()
    } else {
      stopPolling()
    }
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    if (!isPolling) {
      loading.value = false
    }
  }
}

const onUpdateOptions = newOptions => {
  options.value = newOptions
}

const downloadPdf = async item => {
  if (isDownloading.value) return

  isDownloading.value = true

  try {
    const response = await axios.get(`/api/exports/download/pdf/${item.id}`)

    window.open(response.data.url, '_blank', 'noopener,noreferrer')
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    isDownloading.value = false
  }
}

onBeforeUnmount(() => {
  stopPolling()
})

watch(options, loadExports, { deep: true })
</script>

<template>
  <VDataTableServer
    :headers="headers"
    :items="exports"
    :items-length="totalExports"
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
    <template #item.file_name="{ item }">
      {{ item.file_name ?? '-' }}
    </template>

    <template #item.status="{ item }">
      {{ item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'N/A' }}
    </template>

    <template #item.total_receipts="{ item }">
      {{ item.total_receipts ?? '-' }}
    </template>

    <template #item.updated_at="{ item }">
      {{ formatDate(item.updated_at) }}
    </template>

    <template #item.actions="{ item }">
      <div class="d-flex justify-center flex-nowrap">
        <VBtn
          :loading="isDownloading"
          :disabled="isDownloading"
          color="secondary"
          variant="outlined"
          icon="ri-file-download-line"
          @click="downloadPdf(item)"
        />
      </div>
    </template>

    <template #no-data>
      <div class="pa-6 text-center">
        <VIcon
          icon="ri-inbox-archive-line"
          size="48"
          class="mb-2"
        />
        <div>No exports found.</div>
        <small>Export to get started.</small>
      </div>
    </template>
  </VDataTableServer>
</template>
