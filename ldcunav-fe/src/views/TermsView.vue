<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

function goBack() {
  router.push('/profile')
}
</script>

<template>

  <div class="information-page">

    <header class="information-header">

      <button
        class="information-back"
        @click="goBack"
      >
        ←
      </button>

      <div>
        <span>Profile</span>
        <h1>Terms of Service</h1>
      </div>

    </header>


    <main class="information-content">

      <section class="information-card">

        <h2>Using LDCUNav</h2>

        <p>
          LDCUNav is intended to help students find
          buildings, facilities, offices, and other
          university locations.
        </p>

      </section>


      <section class="information-card">

        <h2>Account Responsibility</h2>

        <p>
          You are responsible for keeping your LDCU account
          information and password secure.
        </p>

      </section>


      <section class="information-card">

        <h2>Campus Information</h2>

        <p>
          Campus locations and descriptions are provided to
          assist with navigation. Always follow official
          university signs and instructions when on campus.
        </p>

      </section>


      <section class="information-card">

        <h2>Acceptable Use</h2>

        <p>
          LDCUNav should be used for legitimate university
          navigation and information purposes.
        </p>

      </section>

    </main>

  </div>

</template>