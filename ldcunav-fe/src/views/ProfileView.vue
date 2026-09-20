<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

function goToAccountSettings() {
  router.push('/account-settings')
}

function goToPrivacy() {
  router.push('/privacy')
}

function goToTerms() {
  router.push('/terms')
}

function logout() {
  router.push('/login')
}
</script>

<template>
  <div class="profile-page">

    <!-- HEADER -->

    <div class="page-header">

      <div>
        <span>Your account</span>

        <h1>Profile</h1>
      </div>

    </div>


    <!-- PROFILE CARD -->

    <div class="profile-card">

      <div class="profile-avatar">
        S
      </div>

      <h2>
        Student
      </h2>

      <p>
        student@liceo.edu.ph
      </p>

    </div>


    <!-- SETTINGS -->

    <div class="settings">


      <!-- ACCOUNT SETTINGS -->

      <button
        type="button"
        @click="goToAccountSettings"
      >

        <span>⚙️</span>

        <span>
          Account Settings
        </span>

        <b>
          ›
        </b>

      </button>


      <!-- PRIVACY -->

      <button
        type="button"
        @click="goToPrivacy"
      >

        <span>🔒</span>

        <span>
          Privacy
        </span>

        <b>
          ›
        </b>

      </button>


      <!-- TERMS -->

      <button
        type="button"
        @click="goToTerms"
      >

        <span>📄</span>

        <span>
          Terms of Service
        </span>

        <b>
          ›
        </b>

      </button>

    </div>


    <!-- LOG OUT -->

    <button
      type="button"
      class="logout-button"
      @click="logout"
    >
      Log Out
    </button>

  </div>
</template>