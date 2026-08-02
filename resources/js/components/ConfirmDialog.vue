<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: 'Confirm Action',
  },
  message: {
    type: String,
    default: 'Are you sure you want to proceed?',
  },
  confirmText: {
    type: String,
    default: 'Confirm',
  },
  cancelText: {
    type: String,
    default: 'Cancel',
  },
  confirmColor: {
    type: String,
    default: 'error',
  },
  loading: {
    type: Boolean,
    default: false,
  },
  maxWidth: {
    type: [String, Number],
    default: 420,
  },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const close = () => {
  emit('update:modelValue', false)
  emit('cancel')
}

const confirm = () => {
  emit('confirm')
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    :max-width="maxWidth"
    @update:model-value="val => emit('update:modelValue', val)"
  >
    <VCard :title="title">
      <VCardText>
        <slot>
          {{ message }}
        </slot>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          variant="outlined"
          :disabled="loading"
          @click="close"
        >
          {{ cancelText }}
        </VBtn>
        <VBtn
          :color="confirmColor"
          :loading="loading"
          @click="confirm"
        >
          {{ confirmText }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
