import axios from 'axios'
import { defineStore } from 'pinia'

export const useReceiptsStore = defineStore('receipts', {
  state: () => ({
    pendingCount: 0,
    pollingId: null,
  }),
  actions: {
    async fetchPendingCount() {
      try {
        const { data } = await axios.get('/api/receipts/pending/count')

        this.pendingCount = data
      } catch (error) {
        console.error('Failed to fetch pending count:', error)
      }
    },
    startPolling(intervalMs = 3000) {
      if (this.pollingId) return // avoid duplicate timers

      const poll = async () => {
        await this.fetchPendingCount()
        this.pollingId = setTimeout(poll, intervalMs)
      }

      poll()
    },
    stopPolling() {
      clearTimeout(this.pollingId)
      this.pollingId = null
    },

    // call this from wherever a receipt gets approved/rejected
    decrementPending() {
      if (this.pendingCount > 0) this.pendingCount--
    },
  },
})
