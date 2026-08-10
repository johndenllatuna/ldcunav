<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <header class="navbar">
    <div class="brand">
      <span class="brand-text">LdcuNav</span>
    </div>
  </header>

  <main class="main-container">
    <div class="content-wrapper">
      <router-view />
    </div>
  </main>
</template>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  padding: 1.5rem 2rem;
  z-index: 100;
  display: flex;
  align-items: center;
}

.brand-text {
  font-family: 'Orbitron', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 700;
  font-size: 1.5rem;
  letter-spacing: 0.08em;
  color: #000000;
  user-select: none;
}

.main-container {
  height: 100vh;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0;
  width: 100%;
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>

