<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import axios from 'axios'
import { ref, watch } from 'vue'

const { success, error } = useSnackbar()

const roles = ref([])
const totalRoles = ref(0)
const loading = ref(false)

const headers = [
  { title: 'Name', key: 'name' },
  { title: 'Display Name', key: 'display_name' },
  { title: 'Description', key: 'description' },
]

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [], // e.g. [{ key: 'receipt_date', order: 'desc' }]
})

const loadRoles = async () => {
  loading.value = true
  try {
    const { page, itemsPerPage, sortBy } = options.value

    const response = await axios.get('/api/roles/list', {
      params: {
        page,
        itemsPerPage,
        sortBy: sortBy[0]?.key ?? null,
        sortOrder: sortBy[0]?.order ?? null,
      },
    })

    roles.value = response.data.data ?? []
    totalRoles.value = response.data.total
  } catch (err) {
    error('Something went wrong. Please contact support.')
  } finally {
    loading.value = false
  }
}

const onUpdateOptions = newOptions => {
  options.value = newOptions
}

watch(options, loadRoles, { deep: true })
</script>

<template>
  <VDataTableServer
    :headers="headers"
    :items="roles"
    :items-length="totalRoles"
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
    <template #item.name="{ item }">
      {{ item.name ?? '-' }}
    </template>

    <template #item.display_name="{ item }">
      {{ item.display_name ?? '-' }}
    </template>

    <template #item.description="{ item }">
      {{ item.description ?? '-' }}
    </template>

    <template #no-data>
      <div class="pa-6 text-center">
        <VIcon
          icon="ri-admin-fill"
          size="48"
          class="mb-2"
        />
        <div>No roles found.</div>
        <small>Create your first role to get started.</small>
      </div>
    </template>
  </VDataTableServer>
</template>
