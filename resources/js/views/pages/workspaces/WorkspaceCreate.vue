<script setup>
import { useSnackbar } from '@/composables/useSnackbar'
import { useWorkspaceStore } from '@/plugins/stores/workspace'
import axios from 'axios'

const { success, error } = useSnackbar()

const workspaceStore = useWorkspaceStore()

const items = [
  { title: 'Personal', value: 'personal' },
  { title: 'Business', value: 'business' },
]

const saving = ref(false)
const errors = ref({})
const form = ref(null)

const type = ref('personal')
const name = ref('')
const description = ref('')
const taxNo = ref()
const registrationNo = ref()

const required = value => !!value || 'This field is required'

const maxLength255 = v => !v || v.length <= 255 || 'Maximum 255 characters'
const maxLength5000 = v => !v || v.length <= 5000 || 'Maximum 5000 characters'

const submit = async () => {
  saving.value = true
  errors.value = {}

  const { valid } = await form.value.validate()

  if (!valid) {
    return
  }

  const payload = {
    type: type.value,
    name: name.value,
    description: description.value,
    taxNo: taxNo.value,
    registrationNo: registrationNo.value,
  }

  try {
    const response = await axios.post('/api/workspaces/store', payload)

    success(response.data.message)
    reset()
    workspaceStore.fetchWorkspaces()
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
      error('Something went wrong. Please check your input.')

      return
    }
    error('Something went wrong. Please contact support.')
  } finally {
    saving.value = false
  }
}

const reset = () => {
  type.value = 'personal'
  name.value = ''
  description.value = ''
  taxNo.value = ''
  registrationNo.value = ''

  form.value?.resetValidation()
}
</script>

<template>
  <VForm
    ref="form"
    autocomplete="off"
    @submit.prevent="submit"
  >
    <VRow>
      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="name">Type <span class="text-error">*</span></label>
          </VCol>

          <VCol
            cols="12"
            md="9"
          >
            <VSelect
              v-model="type"
              :items="items"
              item-title="title"
              item-value="value"
              :rules="[required]"
              :error-messages="errors.type"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="name">Name <span class="text-error">*</span></label>
          </VCol>

          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="name"
              v-model="name"
              placeholder="Bussiness Co. Ltd."
              persistent-placeholder
              :rules="[required, maxLength255]"
              :error-messages="errors.name"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="description">Description</label>
          </VCol>

          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="description"
              v-model="description"
              placeholder="Describe your bussiness"
              persistent-placeholder
              :rules="[maxLength5000]"
              :error-messages="errors.description"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="taxNo">Tax No.</label>
          </VCol>

          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="taxNo"
              v-model="taxNo"
              type="text"
              placeholder="Tax Identification Number"
              persistent-placeholder
              :rules="[maxLength255]"
              :error-messages="errors.taxNo"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          >
            <label for="registrationNo">Registration No.</label>
          </VCol>

          <VCol
            cols="12"
            md="9"
          >
            <VTextField
              id="registrationNo"
              v-model="registrationNo"
              type="text"
              placeholder="Business Registration Number"
              persistent-placeholder
              :rules="[maxLength255]"
              :error-messages="errors.registrationNo"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol cols="12">
        <VRow no-gutters>
          <VCol
            cols="12"
            md="3"
          />
          <VCol
            cols="12"
            md="9"
          >
            <VBtn
              :loading="saving"
              :disabled="saving"
              type="submit"
              class="me-4"
            >
              Submit
            </VBtn>
            <VBtn
              color="secondary"
              variant="outlined"
              @click="reset"
            >
              Reset
            </VBtn>
          </VCol>
        </VRow>
      </VCol>
    </VRow>
  </VForm>
</template>
