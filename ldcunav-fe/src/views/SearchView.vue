<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const searchQuery = ref(String(route.query.q || ''))
const activeCategory = ref('All')

const categories = ['All', 'Academic', 'Facilities', 'Offices', 'Services']

const locations = [
  {
    name: 'West Academic Cluster',
    type: 'Academic Building',
    category: 'Academic'
  },
  {
    name: 'North Academic Cluster',
    type: 'Academic Building',
    category: 'Academic'
  },
  {
    name: 'South Academic Cluster',
    type: 'Academic Building',
    category: 'Academic'
  },
  {
    name: 'East Academic Cluster',
    type: 'Academic Building',
    category: 'Academic'
  },
  {
    name: 'Liceo Civic Center',
    type: 'Campus Facility',
    category: 'Facilities'
  },
  {
    name: 'Rodolsa Hall',
    type: 'University Facility',
    category: 'Facilities'
  }
]

const filteredLocations = computed(() => {
  const query = searchQuery.value.toLowerCase().trim()

  return locations.filter((location) => {
    const matchesCategory =
      activeCategory.value === 'All' ||
      location.category === activeCategory.value

    const matchesSearch =
      !query ||
      location.name.toLowerCase().includes(query) ||
      location.type.toLowerCase().includes(query)

    return matchesCategory && matchesSearch
  })
})

function selectCategory(category: string) {
  activeCategory.value = category
}

function openLocation(locationName: string) {
  router.push({
    path: '/map',
    query: {
      location: locationName
    }
  })
}

function updateSearch() {
  router.replace({
    path: '/search',
    query: searchQuery.value
      ? { q: searchQuery.value }
      : {}
  })
}
</script>

<template>
  <div class="search-page">
    <header class="search-header">
      <span>Find your destination</span>
      <h1>Search</h1>
    </header>

    <main class="search-content">

      <!-- SEARCH BAR -->
      <form class="search-box" @submit.prevent="updateSearch">
        <span class="search-icon">⌕</span>

        <input
          v-model="searchQuery"
          type="search"
          placeholder="Search buildings, offices..."
          aria-label="Search buildings and offices"
        />
      </form>

      <!-- CATEGORIES -->
      <div class="search-categories">
        <button
          v-for="category in categories"
          :key="category"
          type="button"
          class="category-button"
          :class="{ active: activeCategory === category }"
          @click="selectCategory(category)"
        >
          {{ category }}
        </button>
      </div>

      <!-- LOCATIONS -->
      <section class="search-results">
        <h2>Locations</h2>

        <div class="location-list">

          <button
            v-for="location in filteredLocations"
            :key="location.name"
            type="button"
            class="search-location-card"
            @click="openLocation(location.name)"
          >
            <div class="location-pin">
              📍
            </div>

            <div class="location-info">
              <strong>{{ location.name }}</strong>
              <span>{{ location.type }}</span>
            </div>

            <span class="location-arrow">›</span>
          </button>

        </div>

        <div
          v-if="filteredLocations.length === 0"
          class="no-results"
        >
          No locations found.
        </div>
      </section>

    </main>
  </div>
</template>