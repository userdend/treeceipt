<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import axios from 'axios'
import { ref, watch } from 'vue'

const { success, error } = useSnackbar()

const exports = ref([])
const totalExports = ref(0)
const loading = ref(false)

const headers = [
  { title: 'File', key: 'file_path' },
  { title: 'Status', key: 'status' },
  { title: 'Total Receipts', key: 'total_receipts' },
]

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [], // e.g. [{ key: 'receipt_date', order: 'desc' }]
})

const loadExports = async () => {
  loading.value = true
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
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    loading.value = false
  }
}

const onUpdateOptions = newOptions => {
  options.value = newOptions
}

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
    <template #item.file_path="{ item }">
      {{ item.file_path ?? '-' }}
    </template>

    <template #item.status="{ item }">
      {{ item.status ?? '-' }}
    </template>

    <template #item.total_receipts="{ item }">
      {{ item.total_receipts ?? '-' }}
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
