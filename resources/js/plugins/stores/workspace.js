import axios from 'axios'
import { defineStore } from 'pinia'

export const useWorkspaceStore = defineStore('workspace', {
  state: () => ({
    workspaces: [],
    currentWorkspace: null,
    loading: false,
  }),

  getters: {
    currentWorkspaceId: state => state.currentWorkspace?.id,
  },

  actions: {
    async fetchWorkspaces() {
      this.loading = true

      try {
        const response = await axios.get('/api/workspaces/list/menu')

        this.workspaces = response.data

        // Set default workspace
        if (!this.currentWorkspace && this.workspaces.length) {
          this.currentWorkspace = this.workspaces[0]
        }
      } finally {
        this.loading = false
      }
    },

    setWorkspace(workspace) {
      this.currentWorkspace = workspace
    },
  },
})
