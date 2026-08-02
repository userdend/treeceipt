<script setup>
import logo from '@images/logo.svg?raw'
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png'
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png'
import authV1Tree2 from '@images/pages/auth-v1-tree-2.png'
import authV1Tree from '@images/pages/auth-v1-tree.png'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

function login() {
  window.location.href = '/auth/google'
}
</script>

<template>
  <!-- eslint-disable vue/no-v-html -->

  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <RouterLink
          to="/"
          class="d-flex align-center gap-3"
        >
          <!-- eslint-disable vue/no-v-html -->
          <div
            class="d-flex"
            v-html="logo"
          />
          <h2 class="font-weight-medium text-2xl text-uppercase">Treeceipt</h2>
        </RouterLink>
      </VCardItem>

      <VCardText class="pt-2">
        <h4 class="text-h4 mb-1">Welcome to Treeceipt! 👋🏻</h4>
        <p class="mb-0">
          Keep your <strong>receipts organized</strong> and make <strong>tax preparation</strong> easier.
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="() => {}">
          <VRow>
            <!-- password -->
            <VCol cols="12">
              <VBtn
                block
                variant="outlined"
                class="google-btn"
                @click="login"
              >
                <template #prepend>
                  <VIcon icon="ri-google-fill" />
                </template>

                Continue with Google
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>

    <VImg
      class="auth-footer-start-tree d-none d-md-block"
      :src="authV1Tree"
      :width="250"
    />

    <VImg
      :src="authV1Tree2"
      class="auth-footer-end-tree d-none d-md-block"
      :width="350"
    />

    <!-- bg img -->
    <VImg
      class="auth-footer-mask d-none d-md-block"
      :src="authThemeMask"
    />
  </div>
</template>

<style lang="scss">
@use '@core-scss/template/pages/page-auth';
</style>
