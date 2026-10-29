<script setup lang="ts">
withDefaults(defineProps<{
  name: 'home' | 'map' | 'search' | 'map-pins' | 'user' | 'pinned' | 'building-arch' | 'building-bank' | 'settings' | 'key' | 'lock' | 'terms' | 'arrow-left' | 'eye' | 'info-circle' | 'logout' | 'building-church' | 'salad'
  size?: number
}>(), { size: 24 })
</script>

<template>
  <svg xmlns="http://www.w3.org/2000/svg" :width="size" :height="size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    <template v-if="name === 'home'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></template>
    <template v-else-if="name === 'map'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></template>
    <template v-else-if="name === 'search'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 10a7 7 0 1 0 14 0a7 7 0 0 0 -14 0" /><path d="M21 21l-6 -6" /></template>
    <template v-else-if="name === 'map-pins'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M10.828 9.828a4 4 0 1 0 -5.656 0l2.828 2.829l2.828 -2.829" /><path d="M8 7l0 .01" /><path d="M18.828 17.828a4 4 0 1 0 -5.656 0l2.828 2.829l2.828 -2.829" /><path d="M16 15l0 .01" /></template>
    <template v-else-if="name === 'user'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></template>
    <template v-else-if="name === 'pinned'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M9 4v6l-2 4v2h10v-2l-2 -4v-6" /><path d="M12 16l0 5" /><path d="M8 4l8 0" /></template>
    <template v-else-if="name === 'building-arch'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 21l18 0" /><path d="M4 21v-15a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v15" /><path d="M9 21v-8a3 3 0 0 1 6 0v8" /></template>
    <template v-else-if="name === 'settings'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></template>
    <template v-else-if="name === 'key'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0" /><path d="M15 9h.01" /></template>
    <template v-else-if="name === 'lock'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></template>
    <template v-else-if="name === 'terms'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 19v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2" /><path d="M11 17h2" /><path d="M9 12h6" /><path d="M10 7h4" /></template>
    <template v-else-if="name === 'arrow-left'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></template>
    <template v-else-if="name === 'eye'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></template>
    <template v-else-if="name === 'info-circle'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></template>
    <template v-else-if="name === 'logout'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M14 12l-10 0" /><path d="M14 12l-4 4" /><path d="M14 12l-4 -4" /><path d="M20 4l0 16" /></template>
    <template v-else-if="name === 'building-church'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 21l18 0" /><path d="M10 21v-4a2 2 0 0 1 4 0v4" /><path d="M10 5l4 0" /><path d="M12 3l0 5" /><path d="M6 21v-7m-2 2l8 -8l8 8m-2 -2v7" /></template>
    <template v-else-if="name === 'salad'"><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M4 11h16a1 1 0 0 1 1 1v.5c0 1.5 -2.517 5.573 -4 6.5v1a1 1 0 0 1 -1 1h-8a1 1 0 0 1 -1 -1v-1c-1.687 -1.054 -4 -5 -4 -6.5v-.5a1 1 0 0 1 1 -1" /><path d="M18.5 11c.351 -1.017 .426 -2.236 .5 -3.714v-1.286h-2.256c-2.83 0 -4.616 .804 -5.64 2.076" /><path d="M5.255 11.008a12.204 12.204 0 0 1 -.255 -2.008v-1h1.755c.98 0 1.801 .124 2.479 .35" /><path d="M8 8l1 -4l4 2.5" /><path d="M13 11v-.5a2.5 2.5 0 1 0 -5 0v.5" /></template>
    <template v-else><path d="M0 0h24v24H0z" fill="none" stroke="none" /><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></template>
  </svg>
</template>
