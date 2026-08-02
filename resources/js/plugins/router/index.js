import { useAuthStore } from '@/plugins/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async to => {
  const auth = useAuthStore()
  if (!auth.loaded) {
    await auth.fetchUser()
  }

  // User is not logged in
  if (to.meta.requiresAuth && !auth.user) {
    return {
      path: '/login',
    }
  }

  // User already logged in, prevent login/register access
  if (to.meta.guestOnly && auth.user) {
    return {
      path: '/dashboard',
    }
  }
})

export default function (app) {
  app.use(router)
}
export { router }
