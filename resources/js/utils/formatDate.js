export const formatDate = date => {
  if (!date) return '-'

  return new Intl.DateTimeFormat('en-MY', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(new Date(date))
}
