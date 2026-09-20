<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const fullName = ref('Student')

const studentId = ref('2021-00001')
const email = ref('student@liceo.edu.ph')

const saved = ref(false)

function saveChanges() {
  saved.value = true

  setTimeout(() => {
    saved.value = false
  }, 2500)
}

function goBack() {
  router.push('/profile')
}

function changePassword() {
  router.push({
    path: '/forgot-password',
    query: {
      from: 'settings'
    }
  })
}
</script>

<template>

  <div class="settings-page">

    <!-- HEADER -->

    <header class="settings-header">

      <button
        class="settings-back"
        type="button"
        aria-label="Back to profile"
        @click="goBack"
      >
        ←
      </button>

      <div>

        <span>
          Profile
        </span>

        <h1>
          Account Settings
        </h1>

      </div>

    </header>


    <!-- CONTENT -->

    <main class="settings-content">

      <!-- SUCCESS -->

      <div
        v-if="saved"
        class="settings-success"
      >
        ✓ Changes saved successfully.
      </div>


      <!-- PERSONAL INFORMATION -->

      <section class="settings-section">

        <h2>
          Personal Information
        </h2>


        <!-- FULL NAME -->

        <label for="full-name">
          FULL NAME
        </label>

        <input
          id="full-name"
          v-model="fullName"
          class="settings-input"
          type="text"
          autocomplete="name"
        />


        <!-- STUDENT ID -->

        <label for="student-id">
          STUDENT ID NUMBER
        </label>

        <input
          id="student-id"
          v-model="studentId"
          class="settings-input settings-readonly"
          type="text"
          readonly
          aria-readonly="true"
        />


        <!-- EMAIL -->

        <label for="student-email">
          LDCU EMAIL
        </label>

        <input
          id="student-email"
          v-model="email"
          class="settings-input settings-readonly"
          type="email"
          readonly
          aria-readonly="true"
        />

      </section>


      <!-- PASSWORD -->

      <section class="settings-section">

        <h2>
          Password
        </h2>

        <button
          class="settings-option"
          type="button"
          @click="changePassword"
        >

          <span>
            🔑
          </span>

          <span>
            Change Password
          </span>

          <b>
            ›
          </b>

        </button>

      </section>


      <!-- SAVE -->

      <button
        class="settings-save"
        type="button"
        @click="saveChanges"
      >
        Save Changes
      </button>

    </main>

  </div>

</template>