<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const savedLocations = ref([
  {
    name: 'Rodolsa Hall',
    type: 'University Facility'
  },
  {
    name: 'West Academic Cluster',
    type: 'Academic Building'
  }
])

function openLocation(locationName: string) {
  router.push({
    path: '/map',
    query: {
      location: locationName
    }
  })
}

function removeLocation(locationName: string) {
  savedLocations.value = savedLocations.value.filter(
    location => location.name !== locationName
  )
}
</script>

<template>
  <div class="saved-page">

    <header class="saved-header">
      <span>Your places</span>
      <h1>Saved Locations</h1>
    </header>

    <main class="saved-content">

      <div
        v-if="savedLocations.length"
        class="saved-list"
      >

        <div
          v-for="location in savedLocations"
          :key="location.name"
          class="saved-location-card"
        >

          <!-- CLICK LOCATION -->
          <button
            type="button"
            class="saved-location-button"
            @click="openLocation(location.name)"
          >
            <div class="saved-pin">
              📍
            </div>

            <div class="saved-info">
              <strong>{{ location.name }}</strong>
              <span>{{ location.type }}</span>
            </div>
          </button>

          <!-- REMOVE FROM SAVED -->
          <button
            type="button"
            class="saved-heart"
            :aria-label="`Remove ${location.name} from saved locations`"
            @click="removeLocation(location.name)"
          >
            ♡
          </button>

        </div>

      </div>

      <div
        v-else
        class="empty-saved"
      >
        <div>♡</div>
        <h2>No saved locations</h2>
        <p>Your saved places will appear here.</p>

        <button
          type="button"
          @click="router.push('/search')"
        >
          Explore Locations
        </button>
      </div>

    </main>
  </div>
</template>