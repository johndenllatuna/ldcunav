<script setup lang="ts">
import { useRoute } from 'vue-router'
import CompassLogo from './components/CompassLogo.vue'

const route = useRoute()
</script>

<template>
  <div class="app-layout">
    <!-- Persistent Top Navigation Bar -->
    <header class="app-navbar">
      <router-link to="/" class="nav-brand" aria-label="LdcuNav Home">
        <CompassLogo :size="32" :glow="false" :animated="false" />
        <span class="brand-text">
          <span class="brand-accent">Ldcu</span>Nav
        </span>
      </router-link>

      <div class="campus-badge">
        <span class="campus-dot"></span>
        <span>Main Campus</span>
      </div>

      <nav class="nav-links-right">
        <router-link
          to="/"
          class="nav-pill-link"
          :class="{ active: route.name === 'login' }"
        >
          Sign In
        </router-link>
        <router-link
          to="/register"
          class="nav-pill-link"
          :class="{ active: route.name === 'register' }"
        >
          Register
        </router-link>
      </nav>
    </header>

    <!-- Main Content Container with Router View -->
    <main class="auth-container">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
