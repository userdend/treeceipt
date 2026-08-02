<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import { useWorkspaceStore } from '@/plugins/stores/workspace'
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  receiptId: {
    type: Number,
    default: null,
  },

  mode: {
    type: String,
    default: 'create',
  },
})

const router = useRouter()
const workspaceStore = useWorkspaceStore()
const file = ref(null)
const form = ref()
const errors = ref({})
const loading = ref(false)

const { success, error } = useSnackbar()

const normalizedFile = () => (Array.isArray(file.value) ? file.value[0] : file.value)

const resetForm = () => {
  file.value = null
  errors.value = {}
  form.value?.resetValidation()
}

const submit = async () => {
  if (loading.value) return // guard against double-submit / re-entrancy

  const { valid } = await form.value.validate()

  if (!valid) return

  const selectedFile = normalizedFile()

  const formData = new FormData()

  formData.append('workspace_id', workspaceStore.currentWorkspace.id)
  formData.append('file', selectedFile)

  loading.value = true
  errors.value = {}

  try {
    let url = '/api/receipts/upload'

    if (props.mode === 'replace') {
      url = `/api/receipts/replace/${props.receiptId}`
    }

    const response = await axios.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    resetForm()

    success(response.data.message)

    const receiptId = response.data.data.id

    router.push({ name: 'receipt-process', params: { id: receiptId } })
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
      error('Something went wrong. Please check your input.')
    }

    error('Something went wrong. Please contact support.')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <VForm
    ref="form"
    @submit.prevent="submit"
  >
    <VRow>
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="workspace">Workspace</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
            class="mb-4"
          >
            <VTextField
              id="workspace"
              placeholder="Placeholder Text"
              :model-value="workspaceStore.currentWorkspace?.name"
              variant="filled"
              hint="Your active workspace. Switch anytime from the navbar."
              persistent-hint
              readonly
              :error-messages="errors.workspace_id"
            />
          </VCol>
        </VRow>
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="file">File</label>
          </VCol>
          <VCol
            cols="12"
            md="9"
          >
            <VTooltip
              text="Accepted file types: PNG, JPEG, JPG"
              location="top"
            >
              <template #activator="{ props }">
                <div v-bind="props">
                  <VFileInput
                    id="file"
                    v-model="file"
                    label="Image / File"
                    accept=".png,.jpeg,.jpg"
                    :multiple="false"
                    :rules="[
                      value => {
                        const f = Array.isArray(value) ? value[0] : value
                        return !!f || 'Please select a file'
                      },
                      value => {
                        const f = Array.isArray(value) ? value[0] : value
                        if (!f) return true
                        return f.size <= 10 * 1024 * 1024 || 'File size must be less than 10 MB'
                      },
                    ]"
                    hint="Upload 1 file up to 10 MB (PNG, JPEG, JPG)"
                    persistent-hint
                    :error-messages="errors.file"
                  />
                </div>
              </template>
            </VTooltip>
          </VCol>
        </VRow>
      </VCol>

      <VCol
        offset-md="1"
        cols="12"
        md="9"
        class="d-flex gap-4"
      >
        <VBtn
          color="primary"
          type="submit"
          :loading="loading"
          :disabled="loading"
        >
          Upload
        </VBtn>

        <VBtn
          color="secondary"
          variant="outlined"
          type="reset"
          @click.prevent="resetForm"
        >
          Reset
        </VBtn>
        <VBtn
          color="secondary"
          variant="outlined"
          type="reset"
          :to="{ name: 'receipt-list' }"
        >
          Back
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>
