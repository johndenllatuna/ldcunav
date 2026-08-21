<script setup lang="ts">
interface Props {
  size?: number | string
  animated?: boolean
  glow?: boolean
}

withDefaults(defineProps<Props>(), {
  size: 80,
  animated: true,
  glow: true,
})
</script>

<template>
  <div
    class="compass-wrapper"
    :class="{ 'has-glow': glow, 'is-animated': animated }"
    :style="{ width: typeof size === 'number' ? `${size}px` : size, height: typeof size === 'number' ? `${size}px` : size }"
  >
    <svg
      class="compass-svg"
      viewBox="0 0 100 100"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      aria-label="LdcuNav Compass Emblem"
      role="img"
    >
      <defs>
        <!-- Gradients -->
        <linearGradient id="maroonGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#8b1c1c" />
          <stop offset="100%" stop-color="#550c0c" />
        </linearGradient>
        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#f59e0b" />
          <stop offset="100%" stop-color="#b45309" />
        </linearGradient>
        <filter id="subtleGlow" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#6b1212" flood-opacity="0.25" />
        </filter>
      </defs>

      <!-- Outer decorative ring -->
      <circle cx="50" cy="50" r="46" stroke="#6b1212" stroke-width="2.5" stroke-dasharray="2 2" opacity="0.4" />
      
      <!-- Main Outer Ring -->
      <circle cx="50" cy="50" r="42" stroke="#6b1212" stroke-width="3" />
      
      <!-- Sub-dial ring -->
      <circle cx="50" cy="50" r="34" stroke="#6b1212" stroke-width="1" stroke-dasharray="1 3" opacity="0.6" />
      <circle cx="50" cy="50" r="26" stroke="#6b1212" stroke-width="1.2" opacity="0.8" />

      <!-- Dial tick marks (30/60/etc. degrees) -->
      <g stroke="#6b1212" stroke-width="1.5" opacity="0.7">
        <line x1="50" y1="9" x2="50" y2="15" />
        <line x1="50" y1="85" x2="50" y2="91" />
        <line x1="9" y1="50" x2="15" y2="50" />
        <line x1="85" y1="50" x2="91" y2="50" />
        
        <!-- Corner ticks -->
        <line x1="21" y1="21" x2="25" y2="25" />
        <line x1="79" y1="21" x2="75" y2="25" />
        <line x1="21" y1="79" x2="25" y2="75" />
        <line x1="79" y1="79" x2="75" y2="75" />
      </g>

      <!-- Secondary Star / Compass Points (NE, NW, SE, SW) -->
      <g opacity="0.65" class="compass-sub-points">
        <!-- NE -->
        <polygon points="50,50 68,32 50,44" fill="#a83232" />
        <polygon points="50,50 68,32 56,50" fill="#d97777" />
        <!-- NW -->
        <polygon points="50,50 32,32 50,44" fill="#a83232" />
        <polygon points="50,50 32,32 44,50" fill="#d97777" />
        <!-- SE -->
        <polygon points="50,50 68,68 50,56" fill="#a83232" />
        <polygon points="50,50 68,68 56,50" fill="#d97777" />
        <!-- SW -->
        <polygon points="50,50 32,68 50,56" fill="#a83232" />
        <polygon points="50,50 32,68 44,50" fill="#d97777" />
      </g>

      <!-- Primary Compass Star (N, S, E, W) -->
      <g filter="url(#subtleGlow)" class="compass-needle-group">
        <!-- NORTH (Dark maroon left, vibrant maroon right) -->
        <polygon points="50,11 44,50 50,44" fill="#520a0a" />
        <polygon points="50,11 56,50 50,44" fill="#8f1b1b" />
        
        <!-- SOUTH (Light maroon left, pale rose right) -->
        <polygon points="50,89 44,50 50,56" fill="#b94b4b" />
        <polygon points="50,89 56,50 50,56" fill="#e28888" />

        <!-- EAST -->
        <polygon points="89,50 50,44 56,50" fill="#7a1414" />
        <polygon points="89,50 50,56 56,50" fill="#b83838" />

        <!-- WEST -->
        <polygon points="11,50 50,44 44,50" fill="#9c2e2e" />
        <polygon points="11,50 50,56 44,50" fill="#c46464" />
      </g>

      <!-- Center Hub -->
      <circle cx="50" cy="50" r="6" fill="url(#maroonGrad)" stroke="#ffffff" stroke-width="1.5" />
      <circle cx="50" cy="50" r="2.5" fill="#fcd34d" />

      <!-- Cardinal Direction Typography -->
      <!-- North -->
      <text x="50" y="8" text-anchor="middle" font-size="7.5" font-family="'Orbitron', sans-serif" font-weight="900" fill="#6b1212">N</text>
      <!-- South -->
      <text x="50" y="99" text-anchor="middle" font-size="7" font-family="'Orbitron', sans-serif" font-weight="700" fill="#9ca3af">S</text>
      <!-- East -->
      <text x="98" y="52.5" text-anchor="middle" font-size="7" font-family="'Orbitron', sans-serif" font-weight="700" fill="#9ca3af">E</text>
      <!-- West -->
      <text x="2.5" y="52.5" text-anchor="middle" font-size="7" font-family="'Orbitron', sans-serif" font-weight="700" fill="#9ca3af">W</text>
    </svg>
  </div>
</template>

<style scoped>
.compass-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: #ffffff;
  padding: 6px;
  box-sizing: border-box;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  user-select: none;
}

.has-glow {
  box-shadow:
    0 10px 25px -5px rgba(107, 18, 18, 0.16),
    0 8px 10px -6px rgba(107, 18, 18, 0.1),
    0 0 0 1px rgba(107, 18, 18, 0.06);
}



.compass-svg {
  width: 100%;
  height: 100%;
  display: block;
  overflow: visible;
}

.is-animated .compass-needle-group {
  transform-origin: 50px 50px;
  animation: subtleWobble 6s ease-in-out infinite alternate;
}

@keyframes subtleWobble {
  0% {
    transform: rotate(-3.5deg);
  }
  50% {
    transform: rotate(3.5deg);
  }
  100% {
    transform: rotate(-1.5deg);
  }
}
</style>
