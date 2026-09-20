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
        <h1>Privacy</h1>
      </div>

    </header>


    <main class="information-content">

      <section class="information-card">

        <h2>Your Privacy</h2>

        <p>
          LDCUNav is designed to help students navigate
          Liceo de Cagayan University while keeping their
          account information secure.
        </p>

      </section>


      <section class="information-card">

        <h2>Information We Collect</h2>

        <p>
          Your account may contain information such as your
          name, student ID number, and official LDCU email
          address.
        </p>

      </section>


      <section class="information-card">

        <h2>How Your Information Is Used</h2>

        <p>
          Account information is used to provide access to
          LDCUNav features and personalize your campus
          navigation experience.
        </p>

      </section>


      <section class="information-card">

        <h2>Your Account</h2>

        <p>
          Keep your account information secure and do not
          share your password with other people.
        </p>

      </section>

    </main>

  </div>

</template>