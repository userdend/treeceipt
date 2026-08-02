import { router } from '@/plugins/router'
import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loaded: false,
  }),

  actions: {
    async fetchUser() {
      try {
        const response = await axios.get('/api/user')

        this.user = response.data
      } catch (error) {
        this.user = null
      } finally {
        this.loaded = true
      }
    },

    async logout() {
      await axios.post('/logout')

      this.$reset()

      router.push({ name: 'login' })
    },
  },
})
