<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const searchQuery = ref('')

const recentSearches = [
  'Rodolsa Hall',
  'West Academic',
  'Civic Center',
  'North Academic'
]

const locations = [
  {
    name: 'West Academic Cluster',
    type: 'Academic Building',
    icon: '🏛️'
  },
  {
    name: 'North Academic Cluster',
    type: 'Academic Building',
    icon: '🏢'
  },
  {
    name: 'South Academic Cluster',
    type: 'Academic Building',
    icon: '🏫'
  },
  {
    name: 'East Academic Cluster',
    type: 'Academic Building',
    icon: '🎓'
  },
  {
    name: 'Liceo Civic Center',
    type: 'Campus Facility',
    icon: '🏫'
  },
  {
    name: 'Rodolsa Hall',
    type: 'University Facility',
    icon: '🏢'
  }
]

function goToSearch() {
  const query = searchQuery.value.trim()

  if (query) {
    router.push({
      path: '/search',
      query: { q: query }
    })
  } else {
    router.push('/search')
  }
}

function selectRecent(search: string) {
  searchQuery.value = search

  router.push({
    path: '/search',
    query: { q: search }
  })
}

function openLocation(locationName: string) {
  router.push({
    path: '/search',
    query: { q: locationName }
  })
}

function goToExplore() {
  router.push('/explore')
}

function goToProfile() {
  router.push('/profile')
}

function goToMap() {
  router.push('/map')
}
</script>

<template>
  <div class="home-page">

    <!-- =====================================================
         DASHBOARD HEADER
    ====================================================== -->

    <header class="home-header">

      <div class="header-top">

        <!-- LOGO -->
        <div class="brand-small">
          <div class="brand-icon">
            ⌖
          </div>

          <strong>LDCUNav</strong>
        </div>

        <!-- PROFILE -->
        <div class="header-actions">

          <button
            class="profile-circle"
            type="button"
            aria-label="Profile"
            @click="goToProfile"
          >
            S
          </button>

        </div>

      </div>


      <!-- GREETING -->
      <div class="greeting">

        <span>Good day,</span>

        <h1>
          Student! 👋
        </h1>

      </div>

    </header>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="home-content">


      <!-- ===================================================
           SEARCH
      ==================================================== -->

      <form
        class="search-box"
        @submit.prevent="goToSearch"
      >

        <button
          class="search-icon"
          type="submit"
          aria-label="Search"
        >
          ⌕
        </button>

        <div class="search-text">

          <input
            v-model="searchQuery"
            type="search"
            placeholder="Where do you want to go?"
            aria-label="Search campus"
          />

          <small>
            Search buildings, offices, facilities...
          </small>

        </div>

      </form>


      <!-- ===================================================
           RECENT SEARCHES
      ==================================================== -->

      <section class="section">

        <div class="section-label">
          RECENT
        </div>

        <div class="recent-scroll">

          <button
            v-for="recent in recentSearches"
            :key="recent"
            class="recent-chip"
            type="button"
            @click="selectRecent(recent)"
          >
            {{ recent }}
          </button>

        </div>

      </section>


      <!-- ===================================================
           EXPLORE CAMPUS
      ==================================================== -->

      <section class="section">

        <div class="section-heading">

          <h2>
            Explore Campus
          </h2>

          <button
            class="see-all-link"
            type="button"
            @click="goToExplore"
          >
            See all
          </button>

        </div>


        <!-- CAMPUS CARDS -->

        <div class="location-grid">

          <button
            v-for="location in locations"
            :key="location.name"
            class="location-card"
            type="button"
            @click="openLocation(location.name)"
          >

            <!-- ICON -->

            <div class="location-icon">
              {{ location.icon }}
            </div>


            <!-- LOCATION INFORMATION -->

            <div class="location-card-info">

              <h3>
                {{ location.name }}
              </h3>

              <p>
                {{ location.type }}
              </p>

            </div>

          </button>

        </div>

      </section>


      <!-- ===================================================
           FULL CAMPUS MAP
      ==================================================== -->

      <button
        class="map-promo"
        type="button"
        @click="goToMap"
      >

        <!-- DECORATIVE MAP BACKGROUND -->

        <div
          class="map-pattern"
          aria-hidden="true"
        >

          <div class="map-road road-one"></div>

          <div class="map-road road-two"></div>

          <div class="map-road road-three"></div>

          <div class="map-building building-one"></div>

          <div class="map-building building-two"></div>

          <div class="map-building building-three"></div>

        </div>


        <!-- MAP CONTENT -->

        <div class="map-promo-content">

          <div class="map-promo-icon">
            ⌖
          </div>

          <div class="map-promo-text">

            <strong>
              View Full Campus Map
            </strong>

            <span>
              Explore all buildings and facilities
            </span>

          </div>

        </div>


        <!-- ARROW -->

        <span
          class="map-arrow"
          aria-hidden="true"
        >
          →
        </span>

      </button>

    </main>

  </div>
</template>