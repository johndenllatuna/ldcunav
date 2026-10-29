<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppIcon from '../components/AppIcon.vue'

interface CampusLocation {
  name: string
  category: string
  description: string
  icon: string
}

type LocationIcon = 'building-arch' | 'building-bank' | 'building-church' | 'salad'

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
    name: 'Engineering Building',
    category: 'Academic Building',
    description: 'Dedicated academic building for engineering programs, classrooms, and laboratories.',
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
  },
  {
    name: 'Campus Chapel',
    category: 'Campus Facility',
    description: 'A quiet place for university liturgies, reflection, and prayer.',
    icon: '⛪'
  },
  {
    name: 'Campus Canteen',
    category: 'Campus Facility',
    description: 'Campus dining with meal kiosks, refreshments, and student seating.',
    icon: '🍽️'
  }
]

const activeCategory = ref('All')
const router = useRouter()

const categories = [
  'All',
  'Academic',
  'Facilities',
  'Administrative'
]

function iconFor(location: CampusLocation): LocationIcon {
  if (location.name.includes('Chapel')) return 'building-church'
  if (location.name.includes('Canteen')) return 'salad'
  return location.category.includes('Academic') ? 'building-arch' : 'building-bank'
}

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
  router.push({ path: '/map', query: { location: location.name } })
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
            <AppIcon :name="iconFor(location)" />
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
