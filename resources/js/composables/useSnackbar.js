import { ref } from 'vue'

const visible = ref(false)
const message = ref('')
const color = ref('success')

export function useSnackbar() {
  const show = (msg, c = 'success') => {
    message.value = msg
    color.value = c
    visible.value = true
  }

  return {
    visible,
    message,
    color,
    success: msg => show(msg, 'success'),
    error: msg => show(msg, 'error'),
  }
}
