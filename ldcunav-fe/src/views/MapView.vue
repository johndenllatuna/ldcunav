<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const selectedLocation = computed(() => {
  return String(route.query.location || 'Campus')
})

function goBack() {
  router.back()
}

function openSearch() {
  router.push('/search')
}
</script>

<template>
  <div class="map-page">

    <header class="map-header">
      <button
        type="button"
        class="map-back"
        @click="goBack"
      >
        ←
      </button>

      <div>
        <span>Campus navigation</span>
        <h1>Map</h1>
      </div>
    </header>

    <main class="map-content">

      <div class="map-container">

        <div class="map-grid"></div>

        <div class="map-road map-road-one"></div>
        <div class="map-road map-road-two"></div>
        <div class="map-road map-road-three"></div>

        <div class="map-building map-building-one">
          <span>North Academic</span>
        </div>

        <div class="map-building map-building-two">
          <span>West Academic</span>
        </div>

        <div class="map-building map-building-three">
          <span>Rodolsa Hall</span>
        </div>

        <div class="map-building map-building-four">
          <span>Civic Center</span>
        </div>

        <div class="map-pin-selected">
          <span>●</span>
        </div>

      </div>

      <section class="selected-location">
        <span>Selected location</span>

        <div class="selected-location-row">
          <div class="selected-location-icon">
            📍
          </div>

          <div>
            <strong>{{ selectedLocation }}</strong>
            <small>Campus location</small>
          </div>
        </div>
      </section>

      <button
        type="button"
        class="map-search-button"
        @click="openSearch"
      >
        Search another location
      </button>

    </main>

  </div>
</template>

<style scoped>
.map-page {
  min-height: 100vh;
  padding: 28px;
  background: #f7f5f2;
}

.map-header {
  max-width: 720px;
  margin: 0 auto 20px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.map-back {
  width: 38px;
  height: 38px;
  border: 0;
  border-radius: 50%;
  background: #fff;
  color: #061b3d;
  font-size: 20px;
  cursor: pointer;
}

.map-header span {
  color: #8290a8;
  font-size: 11px;
}

.map-header h1 {
  margin: 2px 0 0;
  color: #061b3d;
  font-size: 24px;
}

.map-content {
  width: 100%;
  max-width: 720px;
  margin: 0 auto;
}

.map-container {
  position: relative;
  height: 430px;
  overflow: hidden;
  border-radius: 20px;
  background: #e8eee6;
  box-shadow: 0 8px 25px rgba(20, 35, 60, 0.09);
}

.map-grid {
  position: absolute;
  inset: 0;
  opacity: 0.35;
  background-image:
    linear-gradient(#c5d0c4 1px, transparent 1px),
    linear-gradient(90deg, #c5d0c4 1px, transparent 1px);
  background-size: 35px 35px;
}

.map-road {
  position: absolute;
  background: #ffffff;
  border: 2px solid #d9ded8;
}

.map-road-one {
  width: 130%;
  height: 70px;
  top: 40%;
  left: -15%;
  transform: rotate(-8deg);
}

.map-road-two {
  width: 100%;
  height: 55px;
  top: 5%;
  left: 15%;
  transform: rotate(48deg);
}

.map-road-three {
  width: 100%;
  height: 55px;
  bottom: 4%;
  left: -10%;
  transform: rotate(-48deg);
}

.map-building {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  border-radius: 8px;
  background: #ffffff;
  border: 2px solid #d3dbd0;
  color: #516079;
  font-size: 10px;
  font-weight: 700;
  text-align: center;
}

.map-building-one {
  width: 130px;
  height: 70px;
  top: 65px;
  left: 80px;
}

.map-building-two {
  width: 145px;
  height: 75px;
  bottom: 70px;
  left: 70px;
}

.map-building-three {
  width: 125px;
  height: 65px;
  top: 80px;
  right: 70px;
}

.map-building-four {
  width: 140px;
  height: 70px;
  bottom: 60px;
  right: 65px;
}

.map-pin-selected {
  position: absolute;
  left: 50%;
  top: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: translate(-50%, -50%);
  border-radius: 50% 50% 50% 0;
  background: #d71952;
  color: white;
  font-size: 15px;
  box-shadow: 0 5px 15px rgba(215, 25, 82, 0.35);
}

.selected-location {
  margin-top: 14px;
  padding: 17px;
  border-radius: 15px;
  background: #fff;
  box-shadow: 0 6px 18px rgba(20, 35, 60, 0.07);
}

.selected-location > span {
  color: #8290a8;
  font-size: 9px;
}

.selected-location-row {
  display: flex;
  align-items: center;
  margin-top: 9px;
  gap: 11px;
}

.selected-location-icon {
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  background: #fff0f3;
}

.selected-location-row strong,
.selected-location-row small {
  display: block;
}

.selected-location-row strong {
  color: #061b3d;
  font-size: 12px;
}

.selected-location-row small {
  margin-top: 2px;
  color: #929db0;
  font-size: 9px;
}

.map-search-button {
  width: 100%;
  height: 45px;
  margin-top: 12px;
  border: 0;
  border-radius: 12px;
  background: #061b3d;
  color: #fff;
  font-family: inherit;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

@media (max-width: 767px) {
  .map-page {
    padding: 20px;
  }

  .map-container {
    height: 360px;
  }
}
</style>