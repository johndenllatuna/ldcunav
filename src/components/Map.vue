<template>
  <div class="map-page">
    <div class="search-wrap">
      <div class="search-bar">
        <svg class="search-icon" viewBox="0 0 24 24">
          <circle cx="10.5" cy="10.5" r="6.5"/>
          <path d="M15.5 15.5L21 21"/>
        </svg>

        <input
          v-model="search"
          type="text"
          placeholder="Search campus..."
          autocomplete="off"
          @keydown.enter="searchFirst"
        />

        <button class="locate-btn" @click="resetMap" aria-label="Reset map">
          <svg class="target-icon" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="7"/>
            <circle cx="12" cy="12" r="2.4"/>
            <path d="M12 2.5V5M12 19V21.5M2.5 12H5M19 12H21.5"/>
          </svg>
        </button>
      </div>

      <div v-if="search.trim()" class="search-results">
        <button
          v-for="location in filteredLocations"
          :key="location.id"
          class="search-result"
          @click="selectLocation(location)"
        >
          <strong>{{ location.name }}</strong>
          <small>{{ location.type }}</small>
        </button>

        <div v-if="!filteredLocations.length" class="no-result">
          No campus location found.
        </div>
      </div>
    </div>

    <div class="map-controls">
      <button @click="zoomIn">+</button>
      <button @click="zoomOut">−</button>
      <button @click="resetMap">⌾</button>
      <button @click="showInfo = true">ⓘ</button>
    </div>

    <div class="compass">
      <b>N</b>
      <span>S</span>
    </div>

    <div
      class="map-viewport"
      :class="{ dragging: isDragging }"
      @pointerdown="startDrag"
      @pointermove="dragMap"
      @pointerup="endDrag"
      @pointercancel="endDrag"
    >
      <div
        class="map-shell"
        :style="{
          transform: `translate(${panX}px, ${panY}px) scale(${zoom})`
        }"
      >
        <svg
          class="campus-map"
          viewBox="0 0 998 416"
          preserveAspectRatio="xMidYMid meet"
        >
          <rect width="998" height="416" fill="#ffdddf"/>

          <g class="pavement">
            <rect x="76" y="42" width="17" height="194"/>
            <rect x="76" y="42" width="365" height="17"/>
            <rect x="424" y="42" width="17" height="177"/>
            <rect x="76" y="153" width="365" height="67"/>
            <rect x="76" y="219" width="505" height="18"/>
            <rect x="349" y="237" width="18" height="109"/>
            <rect x="330" y="328" width="32" height="18"/>
            <rect x="575" y="219" width="54" height="199"/>
            <rect x="553" y="349" width="49" height="18"/>
            <rect x="620" y="279" width="245" height="17"/>
            <rect x="864" y="66" width="16" height="337"/>
            <rect x="864" y="66" width="110" height="16"/>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'canteen1' }"
            @click="selectLocation(getLocation('canteen1'))"
          >
            <rect class="coral" x="100" y="20" width="245" height="63" rx="11"/>
            <text x="222.5" y="52" class="title">Canteen</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'east' }"
            @click="selectLocation(getLocation('east'))"
          >
            <rect class="coral" x="145" y="106" width="273" height="47" rx="11"/>
            <text x="281.5" y="123" class="title">East Academic</text>
            <text x="281.5" y="138" class="subtitle">Cluster</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'library' }"
            @click="selectLocation(getLocation('library'))"
          >
            <rect class="coral" x="452" y="89" width="140" height="115" rx="11"/>
            <text x="522" y="137" class="title">Campus</text>
            <text x="522" y="152" class="subtitle">Library</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'chapel' }"
            @click="selectLocation(getLocation('chapel'))"
          >
            <circle class="chapel" cx="770" cy="184" r="79"/>
            <text x="770" y="179" class="title">Campus</text>
            <text x="770" y="196" class="subtitle">Chapel</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'north' }"
            @click="selectLocation(getLocation('north'))"
          >
            <rect class="coral" x="15" y="67" width="53" height="336" rx="11"/>
            <g transform="rotate(-90 41.5 235)">
              <text x="41.5" y="230" class="title">North Academic</text>
              <text x="41.5" y="245" class="subtitle">Cluster</text>
            </g>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'garden' }"
            @click="selectLocation(getLocation('garden'))"
          >
            <rect class="garden" x="73" y="257" width="51" height="95" rx="11"/>
            <g transform="rotate(-90 98.5 304.5)">
              <text x="98.5" y="299" class="title">Botanical</text>
              <text x="98.5" y="314" class="subtitle">Garden</text>
            </g>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'west' }"
            @click="selectLocation(getLocation('west'))"
          >
            <path
              class="coral"
              d="M154 238H190Q199 238 199 247V350Q199 352 202 354H331Q340 354 340 363V394Q340 403 331 403H76Q67 403 67 394V363Q67 354 76 354H141Q150 354 150 345V247Q150 238 154 238Z"
            />
            <text x="203.5" y="378" class="title">West Academic</text>
            <text x="203.5" y="393" class="subtitle">Cluster</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'parking' }"
            @click="selectLocation(getLocation('parking'))"
          >
            <rect class="parking" x="204" y="243" width="119" height="102" rx="17"/>
            <text x="263.5" y="288" class="parking-title">Parking</text>
            <text x="263.5" y="303" class="parking-subtitle">Lot</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'rodelsa' }"
            @click="selectLocation(getLocation('rodelsa'))"
          >
            <rect class="coral" x="408" y="249" width="144" height="167" rx="11"/>

            <g
              class="mini-service"
              @click.stop="selectLocation(getLocation('registrar'))"
            >
              <rect class="mini-service-box" x="431" y="257" width="42" height="14" rx="7"/>
              <text x="452" y="264" class="mini-service-text">Registrar</text>
            </g>

            <g
              class="mini-service"
              @click.stop="selectLocation(getLocation('cashier'))"
            >
              <rect class="mini-service-box" x="481" y="257" width="42" height="14" rx="7"/>
              <text x="502" y="264" class="mini-service-text">Cashier</text>
            </g>

            <text x="480" y="320" class="title">Rodelsa</text>
            <text x="480" y="336" class="subtitle">Hall</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'information' }"
            @click="selectLocation(getLocation('information'))"
          >
            <rect class="info-desk" x="582" y="352" width="27" height="40" rx="8"/>
            <text x="595.5" y="365" class="small-title">Info</text>
            <text x="595.5" y="376" class="small-title">Desk</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'civic' }"
            @click="selectLocation(getLocation('civic'))"
          >
            <rect class="coral" x="634" y="320" width="184" height="85" rx="11"/>

            <g
              class="mini-service"
              @click.stop="selectLocation(getLocation('canteen2'))"
            >
              <rect class="mini-service-box" x="635" y="305" width="40" height="14" rx="7"/>
              <text x="655" y="312" class="mini-service-text">Canteen</text>
            </g>

            <g
              class="mini-service"
              @click.stop="selectLocation(getLocation('clinic'))"
            >
              <rect class="mini-service-box" x="758" y="321" width="38" height="14" rx="7"/>
              <text x="777" y="330" class="mini-service-text">Clinic</text>
            </g>

            <text x="726" y="357" class="title">Liceo Civic</text>
            <text x="726" y="373" class="subtitle">Center</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'canteen3' }"
            @click="selectLocation(getLocation('canteen3'))"
          >
            <circle class="canteen-circle" cx="841.5" cy="385" r="20"/>
            <text x="841.5" y="385" class="small-title">Canteen</text>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'south' }"
            @click="selectLocation(getLocation('south'))"
          >
            <rect class="coral" x="887" y="87" width="47" height="316" rx="11"/>
            <g transform="rotate(90 910.5 245)">
              <text x="910.5" y="239" class="title">South Academic</text>
              <text x="910.5" y="254" class="subtitle">Cluster</text>
            </g>
          </g>

          <g
            class="location"
            :class="{ selected: selected?.id === 'engineering' }"
            @click="selectLocation(getLocation('engineering'))"
          >
            <rect class="coral" x="943" y="87" width="43" height="316" rx="11"/>
            <g transform="rotate(90 964.5 245)">
              <text x="964.5" y="239" class="title">Engineering</text>
              <text x="964.5" y="254" class="subtitle">Building</text>
            </g>
          </g>

          <image
            v-if="selected"
            :href="pinIcon"
            class="location-pin"
            :x="selected.pinX - 20"
            :y="selected.pinY - 42"
            width="40"
            height="40"
          />
        </svg>
      </div>
    </div>

    <div v-if="selected && showInfo" class="info-card">
      <button class="close-btn" @click="showInfo = false">×</button>
      <span class="tag">{{ selected.type }}</span>
      <h2>{{ selected.name }}</h2>
      <p>Description goes here</p>

      <div class="info-buttons">
        <button class="primary-btn">Get Directions</button>
        <button class="secondary-btn">View Details</button>
      </div>
    </div>

    <div v-if="selected && showInfo" class="mobile-sheet">
      <div class="sheet-handle"></div>
      <button class="sheet-close" @click="showInfo = false">×</button>
      <span class="tag">{{ selected.type }}</span>
      <h2>{{ selected.name }}</h2>
      <p>Description goes here</p>

      <div class="info-buttons">
        <button class="primary-btn">Get Directions</button>
        <button class="secondary-btn">View Details</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import pinIcon from '../assets/pinicon.png'

type Location = {
  id: string
  name: string
  type: string
  pinX: number
  pinY: number
}

const search = ref('')
const selected = ref<Location | null>(null)
const showInfo = ref(false)

const zoom = ref(1)
const panX = ref(0)
const panY = ref(0)

const isDragging = ref(false)
const hasMoved = ref(false)

let startX = 0
let startY = 0
let startPanX = 0
let startPanY = 0

const locations: Location[] = [
  { id: 'canteen1', name: 'Canteen', type: 'Canteen', pinX: 222, pinY: 12 },
  { id: 'east', name: 'East Academic Cluster', type: 'Academic Building', pinX: 282, pinY: 98 },
  { id: 'library', name: 'Campus Library', type: 'Campus Facility', pinX: 522, pinY: 78 },
  { id: 'chapel', name: 'Campus Chapel', type: 'Campus Facility', pinX: 770, pinY: 105 },
  { id: 'north', name: 'North Academic Cluster', type: 'Academic Building', pinX: 41, pinY: 66 },
  { id: 'garden', name: 'Botanical Garden', type: 'Garden', pinX: 98, pinY: 256 },
  { id: 'west', name: 'West Academic Cluster', type: 'Academic Building', pinX: 205, pinY: 353 },
  { id: 'parking', name: 'Parking Lot', type: 'Parking Area', pinX: 264, pinY: 233 },
  { id: 'rodelsa', name: 'Rodelsa Hall', type: 'Campus Facility', pinX: 480, pinY: 243 },
  { id: 'registrar', name: 'Registrar', type: 'Student Service', pinX: 455, pinY: 249 },
  { id: 'cashier', name: 'Cashier', type: 'Student Service', pinX: 505, pinY: 249 },
  { id: 'information', name: 'Information Desk', type: 'Student Service', pinX: 604, pinY: 345 },
  { id: 'civic', name: 'Liceo Civic Center', type: 'Campus Facility', pinX: 726, pinY: 311 },
  { id: 'canteen2', name: 'Canteen', type: 'Canteen', pinX: 660, pinY: 285 },
  { id: 'clinic', name: 'Clinic', type: 'Student Service', pinX: 777, pinY: 313 },
  { id: 'canteen3', name: 'Canteen', type: 'Canteen', pinX: 838, pinY: 345 },
  { id: 'south', name: 'South Academic Cluster', type: 'Academic Building', pinX: 910, pinY: 76 },
  { id: 'engineering', name: 'Engineering Building', type: 'Academic Building', pinX: 964, pinY: 76 }
]

const filteredLocations = computed(() => {
  const query = search.value.trim().toLowerCase()

  if (!query) return []

  return locations
    .filter(
      location =>
        location.name.toLowerCase().includes(query) ||
        location.type.toLowerCase().includes(query)
    )
    .slice(0, 7)
})

function getLocation(id: string) {
  return locations.find(location => location.id === id)!
}

function selectLocation(location: Location) {
  selected.value = location
  showInfo.value = true
  search.value = ''
}

function searchFirst() {
  const location = filteredLocations.value[0]
  if (location) selectLocation(location)
}

function zoomIn() {
  zoom.value = Math.min(zoom.value + 0.1, 2)
}

function zoomOut() {
  zoom.value = Math.max(zoom.value - 0.1, 0.4)
}

function startDrag(event: PointerEvent) {
  if (event.button !== 0) return

  const target = event.target as Element

  if (target.closest('.location')) {
    hasMoved.value = false
    return
  }

  isDragging.value = true
  hasMoved.value = false

  startX = event.clientX
  startY = event.clientY
  startPanX = panX.value
  startPanY = panY.value

  const viewport = event.currentTarget as HTMLElement
  viewport.setPointerCapture?.(event.pointerId)
}

function dragMap(event: PointerEvent) {
  if (!isDragging.value) return

  const dx = event.clientX - startX
  const dy = event.clientY - startY

  if (Math.abs(dx) > 4 || Math.abs(dy) > 4) {
    hasMoved.value = true
  }

  if (!hasMoved.value) return

  panX.value = startPanX + dx
  panY.value = startPanY + dy
}

function endDrag(event: PointerEvent) {
  if (!isDragging.value) return

  const viewport = event.currentTarget as HTMLElement

  isDragging.value = false
  viewport.releasePointerCapture?.(event.pointerId)
}

function resetMap() {
  selected.value = null
  showInfo.value = false
  search.value = ''
  zoom.value = 1
  panX.value = 0
  panY.value = 0
}
</script>