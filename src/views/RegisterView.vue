<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const fullName = ref('')
const studentId = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const accepted = ref(false)

function register() {

  if (
    !fullName.value ||
    !studentId.value ||
    !email.value ||
    !password.value ||
    !confirmPassword.value
  ) {
    alert('Please complete all fields.')
    return
  }

  if (!email.value.endsWith('@liceo.edu.ph')) {
    alert('Please use your official LDCU email address.')
    return
  }

  if (password.value.length < 8) {
    alert('Password must have at least 8 characters.')
    return
  }

  if (password.value !== confirmPassword.value) {
    alert('Passwords do not match.')
    return
  }

  if (!accepted.value) {
    alert('Please accept the Terms of Service and Privacy Policy.')
    return
  }

  alert('Account created successfully!')

  router.push('/login')
}
</script>

<template>

  <div class="auth-page register-page">

    <div class="auth-header">

      <button
        class="back-button"
        @click="router.push('/login')"
      >
        ←
      </button>

      <div class="brand-small">
        <div class="brand-icon">⌖</div>
        <strong>LDCUNav</strong>
      </div>

    </div>


    <div class="auth-content">

      <h1>Create Your LDCUNav Account</h1>

      <p class="auth-subtitle">
        Register using your LDCU email.
      </p>


      <div class="info-box">
        ℹ️ Please use your official LDCU email address to register.
      </div>


      <form @submit.prevent="register">

        <label>FULL NAME</label>

        <input
          v-model="fullName"
          class="form-input"
          placeholder="Juan Dela Cruz"
        />


        <label>STUDENT ID NUMBER</label>

        <input
          v-model="studentId"
          class="form-input"
          placeholder="2021-00001"
        />


        <label>LDCU EMAIL</label>

        <input
          v-model="email"
          class="form-input"
          type="email"
          placeholder="yourname@liceo.edu.ph"
        />


        <label>PASSWORD</label>

        <input
          v-model="password"
          class="form-input"
          type="password"
          placeholder="Create a strong password"
        />


        <label>CONFIRM PASSWORD</label>

        <input
          v-model="confirmPassword"
          class="form-input"
          type="password"
          placeholder="Re-enter your password"
        />


        <div class="password-rules">

          <div>✓ At least 8 characters</div>
          <div>✓ One uppercase letter</div>
          <div>✓ One number</div>

        </div>


        <label class="checkbox-row">

          <input
            v-model="accepted"
            type="checkbox"
          />

          <span>
            I agree to the
            <b>Terms of Service</b>
            and
            <b>Privacy Policy</b>
            of LDCUNav.
          </span>

        </label>


        <button class="primary-button">
          Create Account
        </button>

      </form>


      <p class="auth-footer">

        Already have an account?

        <RouterLink to="/login">
          Log In
        </RouterLink>

      </p>

    </div>

  </div>

</template>