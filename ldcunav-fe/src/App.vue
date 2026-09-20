<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const showNavigation = computed(() => {
  return ![
    'login',
    'register',
    'forgot-password'
  ].includes(String(route.name))
})
</script>

<template>

  <div class="app-shell">

    <main class="page-container">
      <router-view />
    </main>


    <!-- SIDEBAR / BOTTOM NAV -->

    <nav
      v-if="showNavigation"
      class="bottom-nav"
    >

      <RouterLink
        to="/home"
        class="nav-item"
      >
        <span class="nav-icon">
          ⌂
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
          ♧
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
          ⌕
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
          ☆
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
          ♙
        </span>

        <span>
          Profile
        </span>
      </RouterLink>

    </nav>

  </div>

</template>