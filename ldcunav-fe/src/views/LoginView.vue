<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')
const showPassword = ref(false)

function login() {
  if (!email.value || !password.value) {
    alert('Please enter your LDCU email and password.')
    return
  }

  if (!email.value.endsWith('@liceo.edu.ph')) {
    alert('Please use your official LDCU email.')
    return
  }

  router.push('/home')
}
</script>

<template>

  <div class="auth-page">

    <div class="auth-header">

      <button
        class="back-button"
        @click="router.push('/')"
      >
        ←
      </button>

      <div class="brand-small">
        <div class="brand-icon">⌖</div>
        <strong>LDCUNav</strong>
      </div>

    </div>


    <div class="auth-content">

      <h1>Welcome Back</h1>

      <p class="auth-subtitle">
        Log in using your LDCU account.
      </p>


      <form @submit.prevent="login">

        <label>LDCU EMAIL</label>

        <div class="input-wrapper">

          <span>✉</span>

          <input
            v-model="email"
            type="email"
            placeholder="yourname@liceo.edu.ph"
          />

        </div>


        <label>PASSWORD</label>

        <div class="input-wrapper">

  <span>♙</span>

  <input
    v-model="password"
    :type="showPassword ? 'text' : 'password'"
    placeholder="Enter your password"
  />

  <button
    type="button"
    class="password-toggle"
    @click="showPassword = !showPassword"
  >
    {{ showPassword ? '◉' : '◌' }}
  </button>

</div>

<RouterLink
  to="/forgot-password"
  class="forgot"
>
  Forgot Password?
</RouterLink>

<button class="primary-button">
  Log In
</button>

      </form>


      <p class="auth-footer">
        Don't have an account?
        <RouterLink to="/register">
          Sign Up
        </RouterLink>
      </p>

    </div>

  </div>

</template>