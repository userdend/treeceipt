<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'
import { useReceiptsStore } from '@/plugins/stores/receipts'
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'

const receiptsStore = useReceiptsStore()

onMounted(() => receiptsStore.startPolling())
onBeforeUnmount(() => receiptsStore.stopPolling())
</script>

<template>
  <!-- 👉 Dashboards -->
  <VerticalNavGroup
    :item="{
      title: 'Dashboards',
      icon: 'ri-home-smile-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Analytics',
        to: '/dashboard',
      }"
    />
  </VerticalNavGroup>

  <!-- 👉 Apps & Pages -->
  <VerticalNavSectionTitle
    :item="{
      heading: 'Apps & Pages',
    }"
  />

  <VerticalNavGroup
    :item="{
      title: 'Receipt',
      icon: 'ri-receipt-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'Upload',
        to: '/receipts/upload',
        icon: 'ri-upload-line',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'List',
        to: '/receipts/list',
        icon: 'ri-list-check',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Pending',
        badgeContent: receiptsStore.pendingCount > 0 ? receiptsStore.pendingCount : '',
        badgeClass: receiptsStore.pendingCount > 0 ? 'bg-error' : '',
        to: '/receipts/pending',
        icon: 'ri-file-warning-fill',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Bin',
        to: '/receipts/bin',
        icon: 'ri-delete-bin-line',
      }"
    />
  </VerticalNavGroup>

  <VerticalNavGroup
    :item="{
      title: 'Workspace',
      badgeContent: '',
      badgeClass: '',
      icon: 'ri-artboard-2-line',
    }"
  >
    <VerticalNavLink
      :item="{
        title: 'New',
        to: '/workspaces/create',
        icon: 'ri-add-line',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'List',
        to: '/workspaces/list',
        icon: 'ri-list-check',
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Bin',
        to: '/workspaces/bin',
        icon: 'ri-delete-bin-line',
      }"
    />
  </VerticalNavGroup>

  <!--
    <VerticalNavGroup
    :item="{
    title: 'Role',
    badgeContent: '',
    badgeClass: '',
    icon: 'ri-admin-fill',
    }"
    >
    <VerticalNavLink
    :item="{
    title: 'List',
    to: '/roles/list',
    icon: 'ri-list-check',
    }"
    />
    </VerticalNavGroup> 
  -->

  <!-- 👉 User Interface -->
  <!--
    <VerticalNavSectionTitle
    :item="{
    heading: 'User Interface',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Typography',
    icon: 'ri-text',
    to: '/typography',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Icons',
    icon: 'ri-remixicon-line',
    to: '/icons',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Cards',
    icon: 'ri-bar-chart-box-line',
    to: '/cards',
    }"
    /> 
  -->

  <!-- 👉 Forms & Tables -->
  <!--
    <VerticalNavSectionTitle
    :item="{
    heading: 'Forms & Tables',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Form Layouts',
    icon: 'ri-layout-4-line',
    to: '/form-layouts',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Tables',
    icon: 'ri-table-alt-line',
    to: '/tables',
    }"
    /> 
  -->

  <!-- 👉 Others -->
  <VerticalNavLink
    :item="{
      title: 'Exports',
      to: '/exports/list',
      icon: 'ri-inbox-archive-line',
    }"
  />

  <VerticalNavSectionTitle
    :item="{
      heading: 'Others',
    }"
  />
  <!--
    <VerticalNavLink
    :item="{
    title: 'Documentation',
    icon: 'ri-article-line',
    href: 'https://demos.themeselection.com/materio-vuetify-vuejs-admin-template/documentation/guide/laravel-integration/folder-structure.html',
    target: '_blank',
    }"
    />  
  -->

  <VerticalNavLink
    :item="{
      title: 'Raise Support',
      href: 'https://github.com/themeselection/materio-vuetify-vuejs-laravel-admin-template-free/issues',
      icon: 'ri-lifebuoy-line',
      target: '_blank',
    }"
  />
</template>
