export const staticPrimaryColor = '#16A34A'
export const staticPrimaryDarkenColor = '#15803D'

export const themes = {
  light: {
    dark: false,
    colors: {
      primary: staticPrimaryColor,
      'on-primary': '#fff',
      'primary-darken-1': staticPrimaryDarkenColor,

      secondary: '#64748B',
      'secondary-darken-1': '#475569',
      'on-secondary': '#fff',

      success: '#22C55E',
      'success-darken-1': '#16A34A',
      'on-success': '#fff',

      info: '#0EA5E9',
      'info-darken-1': '#0284C7',
      'on-info': '#fff',

      warning: '#F59E0B',
      'warning-darken-1': '#D97706',
      'on-warning': '#fff',

      error: '#EF4444',
      'error-darken-1': '#DC2626',
      'on-error': '#fff',

      background: '#F4F7F4',
      'on-background': '#1F2937',

      surface: '#fff',
      'on-surface': '#1F2937',

      'grey-50': '#FAFAFA',
      'grey-100': '#F5F5F5',
      'grey-200': '#EEEEEE',
      'grey-300': '#E0E0E0',
      'grey-400': '#BDBDBD',
      'grey-500': '#9E9E9E',
      'grey-600': '#757575',
      'grey-700': '#616161',
      'grey-800': '#424242',
      'grey-900': '#212121',

      'perfect-scrollbar-thumb': '#CBD5C0',
      'skin-bordered-background': '#fff',
      'skin-bordered-surface': '#fff',
      'expansion-panel-text-custom-bg': '#FAFAFA',
      'track-bg': '#E8F5E9',
      'chat-bg': '#F1F8F2',
    },

    variables: {
      'code-color': '#16A34A',
      'overlay-scrim-background': '#1F2937',
      'tooltip-background': '#14532D',
      'overlay-scrim-opacity': 0.5,
      'hover-opacity': 0.04,
      'focus-opacity': 0.1,
      'selected-opacity': 0.08,
      'activated-opacity': 0.16,
      'pressed-opacity': 0.14,
      'dragged-opacity': 0.1,
      'disabled-opacity': 0.4,
      'border-color': '#1F2937',
      'border-opacity': 0.12,
      'table-header-color': '#F6F7FB',
      'high-emphasis-opacity': 0.9,
      'medium-emphasis-opacity': 0.7,

      'shadow-key-umbra-color': '#1F2937',
      'shadow-xs-opacity': '0.16',
      'shadow-sm-opacity': '0.18',
      'shadow-md-opacity': '0.20',
      'shadow-lg-opacity': '0.22',
      'shadow-xl-opacity': '0.24',
    },
  },

  dark: {
    dark: true,
    colors: {
      primary: staticPrimaryColor,
      'on-primary': '#fff',
      'primary-darken-1': staticPrimaryDarkenColor,

      secondary: '#94A3B8',
      'secondary-darken-1': '#64748B',
      'on-secondary': '#fff',

      success: '#22C55E',
      'success-darken-1': '#16A34A',
      'on-success': '#fff',

      info: '#38BDF8',
      'info-darken-1': '#0284C7',
      'on-info': '#fff',

      warning: '#FBBF24',
      'warning-darken-1': '#D97706',
      'on-warning': '#fff',

      error: '#F87171',
      'error-darken-1': '#DC2626',
      'on-error': '#fff',

      background: '#111827',
      'on-background': '#E5E7EB',

      surface: '#1F2937',
      'on-surface': '#E5E7EB',

      'grey-50': '#1F2937',
      'grey-100': '#374151',
      'grey-200': '#4B5563',
      'grey-300': '#6B7280',
      'grey-400': '#9CA3AF',
      'grey-500': '#D1D5DB',
      'grey-600': '#E5E7EB',
      'grey-700': '#F3F4F6',
      'grey-800': '#F9FAFB',
      'grey-900': '#FFFFFF',

      'perfect-scrollbar-thumb': '#475569',
      'skin-bordered-background': '#1F2937',
      'skin-bordered-surface': '#1F2937',
      'expansion-panel-text-custom-bg': '#263238',
      'track-bg': '#334155',
      'chat-bg': '#1E293B',
    },

    variables: {
      'code-color': '#4ADE80',
      'overlay-scrim-background': '#000',
      'tooltip-background': '#F0FDF4',
      'overlay-scrim-opacity': 0.5,
      'hover-opacity': 0.04,
      'focus-opacity': 0.1,
      'selected-opacity': 0.08,
      'activated-opacity': 0.16,
      'pressed-opacity': 0.14,
      'disabled-opacity': 0.4,
      'dragged-opacity': 0.1,
      'border-color': '#E5E7EB',
      'border-opacity': 0.12,
      'table-header-color': '#1F2937',
      'high-emphasis-opacity': 0.9,
      'medium-emphasis-opacity': 0.7,

      'shadow-key-umbra-color': '#000',
      'shadow-xs-opacity': '0.20',
      'shadow-sm-opacity': '0.22',
      'shadow-md-opacity': '0.24',
      'shadow-lg-opacity': '0.26',
      'shadow-xl-opacity': '0.28',
    },
  },
}

export default themes
