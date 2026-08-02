<script setup>
import { useSnackbar } from '@/composables/useSnackbar'

const { visible, message, color } = useSnackbar()
</script>

<template>
  <VApp>
    <RouterView />
    <VSnackbar
      v-model="visible"
      :color="color"
      timeout="3000"
    >
      {{ message }}
    </VSnackbar>
  </VApp>
</template>
