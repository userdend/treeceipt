export const routes = [
  {
    path: '/',
    redirect: '/dashboard',
  },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: 'receipts',
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: 'upload',
            children: [
              {
                path: '',
                name: 'receipt-upload',
                component: () => import('@/pages/receipts/receipt-upload-form.vue'),
              },
              {
                path: 'process/:id(\\d+)', // id should always be numeric
                name: 'receipt-process',
                component: () => import('@/pages/receipts/receipt-process.vue'),
              },
              {
                path: 'replace/:id(\\d+)',
                name: 'receipt-replace',
                component: () => import('@/pages/receipts/receipt-re-upload-form.vue'),
              },
            ],
          },
          {
            path: 'list',
            children: [
              {
                path: '',
                name: 'receipt-list',
                component: () => import('@/pages/receipts/receipt-list.vue'),
              },
              {
                path: 'details/:id(\\d+)', // id should always be numeric
                name: 'receipt-details',
                component: () => import('@/pages/receipts/receipt-details.vue'),
              },
              {
                path: 'edit/:id(\\d+)', // id should always be numeric
                name: 'receipt-edit',
                component: () => import('@/pages/receipts/receipt-edit.vue'),
              },
            ],
          },
          {
            path: 'pending',
            component: () => import('@/pages/receipts/receipt-pending.vue'),
          },
          {
            path: 'bin',
            component: () => import('@/pages/receipts/receipt-bin.vue'),
          },
        ],
      },
      {
        path: 'workspaces',
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: 'create',
            component: () => import('@/pages/workspaces/workspace-create.vue'),
          },
          {
            path: 'edit/:id(\\d+)', // id should always be numeric
            name: 'workspace-edit',
            component: () => import('@/pages/workspaces/workspace-edit.vue'),
          },
          {
            path: 'list',
            name: 'workspace-list',
            component: () => import('@/pages/workspaces/workspace-list.vue'),
          },
          {
            path: 'bin',
            component: () => import('@/pages/workspaces/workspace-bin.vue'),
          },
        ],
      },
      {
        path: 'roles',
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: 'list',
            name: 'role-list',
            component: () => import('@/pages/roles/role-list.vue'),
          },
        ],
      },
      {
        path: 'exports/list',
        component: () => import('@/pages/exports/export-list.vue'),
      },
      {
        path: 'typography',
        component: () => import('@/pages/typography.vue'),
      },
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
      },
      {
        path: 'cards',
        component: () => import('@/pages/cards.vue'),
      },
      {
        path: 'tables',
        component: () => import('@/pages/tables.vue'),
      },
      {
        path: 'form-layouts',
        component: () => import('@/pages/form-layouts.vue'),
      },
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/login.vue'),
        meta: {
          guestOnly: true,
        },
      },
      {
        path: '/:pathMatch(.*)*',
        name: 'error',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
