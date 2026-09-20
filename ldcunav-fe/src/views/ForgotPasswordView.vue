<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()

const email = ref('')
const submitted = ref(false)
const error = ref('')

const isChangingPassword = computed(() => {
  return route.query.from === 'settings'
})

const pageTitle = computed(() => {
  return isChangingPassword.value
    ? 'Change Password'
    : 'Forgot Password?'
})

const pageDescription = computed(() => {
  return isChangingPassword.value
    ? "Enter your LDCU email address and we'll send you a link to reset your password."
    : "Don't worry! Enter your LDCU email address and we'll help you reset your password."
})

function sendResetLink() {
  error.value = ''

  if (!email.value) {
    error.value = 'Please enter your LDCU email address.'
    return
  }

  if (!email.value.endsWith('@liceo.edu.ph')) {
    error.value = 'Please use your official LDCU email address.'
    return
  }

  submitted.value = true
}

function goBack() {
  if (isChangingPassword.value) {
    router.push('/account-settings')
  } else {
    router.push('/login')
  }
}
</script>


<template>

  <div class="forgot-password-page">

    <!-- HEADER -->

    <header class="forgot-header">

      <button
        class="forgot-back"
        type="button"
        aria-label="Go back"
        @click="goBack"
      >
        ←
      </button>


      <div class="forgot-brand">

        <div class="forgot-brand-icon">
          ⌖
        </div>

        <strong>
          LDCUNav
        </strong>

      </div>

    </header>


    <!-- CONTENT -->

    <main class="forgot-content">


      <!-- ================================================
           EMAIL FORM
      ================================================= -->

      <template v-if="!submitted">

        <div class="forgot-icon">
          🔐
        </div>


        <h1>
          {{ pageTitle }}
        </h1>


        <p class="forgot-description">
          {{ pageDescription }}
        </p>


        <form
          class="forgot-form"
          @submit.prevent="sendResetLink"
        >

          <label for="forgot-email">
            LDCU EMAIL
          </label>


          <div class="forgot-input">

            <span class="email-icon">
              ✉
            </span>

            <input
              id="forgot-email"
              v-model="email"
              type="email"
              placeholder="yourname@liceo.edu.ph"
              autocomplete="email"
            />

          </div>


          <!-- ERROR -->

          <p
            v-if="error"
            class="forgot-error"
          >

            <span>
              !
            </span>

            {{ error }}

          </p>


          <button
            type="submit"
            class="forgot-submit"
          >
            Send Reset Link
          </button>

        </form>


        <!-- FOOTER -->

        <p class="forgot-footer">

          <template v-if="isChangingPassword">

            Remember your password?

            <RouterLink to="/account-settings">
              Back to Account Settings
            </RouterLink>

          </template>

          <template v-else>

            Remember your password?

            <RouterLink to="/login">
              Log In
            </RouterLink>

          </template>

        </p>

      </template>


      <!-- ================================================
           SUCCESS
      ================================================= -->

      <template v-else>

        <div class="forgot-success-icon">
          ✓
        </div>


        <h1>
          Check Your Email
        </h1>


        <p class="forgot-description">

          We've sent a password reset link to:

        </p>


        <strong class="reset-email">
          {{ email }}
        </strong>


        <div class="reset-info">

          📩 Check your inbox and follow the
          instructions to create a new password.

        </div>


        <button
          class="forgot-submit"
          type="button"
          @click="goBack"
        >

          <template v-if="isChangingPassword">
            Back to Account Settings
          </template>

          <template v-else>
            Back to Log In
          </template>

        </button>


        <p class="forgot-footer">

          Didn't receive the email?

          <button
            class="try-again"
            type="button"
            @click="submitted = false"
          >
            Try Again
          </button>

        </p>

      </template>

    </main>

  </div>

</template>