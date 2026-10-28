<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppIcon from './components/AppIcon.vue'

const route = useRoute()
const router = useRouter()

const showNavigation = computed(() => {
  return ![
    'login',
    'register',
    'forgot-password'
  ].includes(String(route.name))
})
</script>

<template>

  <div class="app-shell" :class="{ 'auth-shell': !showNavigation }">

    <main class="page-container">
      <router-view />
    </main>


    <!-- SIDEBAR / BOTTOM NAV -->

    <nav
      v-if="showNavigation"
      class="bottom-nav"
    >
      <div class="sidebar-brand" aria-label="LDCUNav">
        <span>LDCU</span><span>Nav</span>
      </div>

      <RouterLink
        to="/home"
        class="nav-item"
      >
        <span class="nav-icon">
          <AppIcon name="home" />
        </span>

        <span>
          Home
        </span>
      </RouterLink>


      <RouterLink
        to="/map"
        class="nav-item"
      >
        <span class="nav-icon">
          <AppIcon name="map" />
        </span>

        <span>
          Map
        </span>
      </RouterLink>


      <RouterLink
        to="/search"
        class="nav-item"
      >
        <span class="nav-icon">
          <AppIcon name="search" />
        </span>

        <span>
          Search
        </span>
      </RouterLink>


      <RouterLink
        to="/saved"
        class="nav-item"
      >
        <span class="nav-icon">
          <AppIcon name="map-pins" />
        </span>

        <span>
          Saved
        </span>
      </RouterLink>


      <RouterLink
        to="/profile"
        class="nav-item"
      >
        <span class="nav-icon">
          <AppIcon name="user" />
        </span>

        <span>
          Profile
        </span>
      </RouterLink>

      <button
        class="sidebar-logout"
        type="button"
        @click="router.push('/login')"
      >
        <span class="nav-icon"><AppIcon name="logout" /></span>
        <span>Log Out</span>
      </button>

    </nav>

  </div>

</template>
