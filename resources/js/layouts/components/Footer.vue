<template>
  <div class="h-100 d-flex align-center justify-md-space-between justify-center">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center">
      &copy;
      {{ new Date().getFullYear() }}
      By
      <a
        href="https://themeselection.com"
        target="_blank"
        rel="noopener noreferrer"
        class="text-primary ms-1"
        >entt</a
      >
    </span>
    <!-- 👉 Footer: right content -->
    <span class="d-md-flex gap-x-4 text-primary d-none">
      <a
        href="https://themeselection.com/support/"
        target="noopener noreferrer"
        >Support</a
      >
    </span>
  </div>
</template>
