<script setup lang="ts">
import { ref, computed } from 'vue'

interface CampusLocation {
  name: string
  category: string
  description: string
  icon: string
}

const locations: CampusLocation[] = [
  {
    name: 'West Academic Cluster',
    category: 'Academic Building',
    description:
      'Houses engineering, architecture, and technology departments...',
    icon: '🏛️'
  },
  {
    name: 'North Academic Cluster',
    category: 'Academic Building',
    description:
      'Home to the College of Arts and Sciences, library annex, and...',
    icon: '🏛️'
  },
  {
    name: 'South Academic Cluster',
    category: 'Academic Building',
    description:
      'Contains the College of Business and Accountancy and allied...',
    icon: '🏛️'
  },
  {
    name: 'East Academic Cluster',
    category: 'Academic Building',
    description:
      'Dedicated to education, nursing, and graduate school programs.',
    icon: '🏛️'
  },
  {
    name: 'Liceo Civic Center',
    category: 'Campus Facility',
    description:
      'Main event venue hosting university assemblies, cultural...',
    icon: '🏫'
  },
  {
    name: 'Rodolfsa Hall',
    category: 'University Facility',
    description:
      'Multi-purpose hall used for seminars, conferences, and...',
    icon: '🏢'
  }
]

const activeCategory = ref('All')

const categories = [
  'All',
  'Academic',
  'Facilities',
  'Administrative'
]

const filteredLocations = computed(() => {
  if (activeCategory.value === 'All') {
    return locations
  }

  if (activeCategory.value === 'Academic') {
    return locations.filter(
      location => location.category === 'Academic Building'
    )
  }

  if (activeCategory.value === 'Facilities') {
    return locations.filter(
      location =>
        location.category === 'Campus Facility' ||
        location.category === 'University Facility'
    )
  }

  return []
})

function viewMap(location: CampusLocation) {
  console.log(`Opening map for ${location.name}`)
}
</script>

<template>
  <div class="explore-page">

    <!-- HEADER -->

    <header class="explore-header">

      <div>
        <h1>Explore Campus</h1>

        <p>
          Discover all university locations
        </p>
      </div>

    </header>


    <!-- CONTENT -->

    <main class="explore-content">

      <!-- CATEGORY FILTER -->

      <div class="category-scroll">

        <button
          v-for="category in categories"
          :key="category"
          class="category-button"
          :class="{
            active: activeCategory === category
          }"
          @click="activeCategory = category"
        >
          {{ category }}
        </button>

      </div>


      <!-- LOCATION LIST -->

      <div class="explore-list">

        <article
          v-for="location in filteredLocations"
          :key="location.name"
          class="explore-card"
        >

          <!-- ICON -->

          <div class="explore-icon">
            {{ location.icon }}
          </div>


          <!-- INFORMATION -->

          <div class="explore-info">

            <h2>
              {{ location.name }}
            </h2>

            <span class="explore-category">
              {{ location.category }}
            </span>

            <p>
              {{ location.description }}
            </p>

          </div>


          <!-- MAP BUTTON -->

          <button
            class="view-map-button"
            @click="viewMap(location)"
          >
            View Map
          </button>

        </article>

      </div>

    </main>

  </div>
</template>