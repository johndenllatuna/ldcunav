<script setup lang="ts">
import { ref } from 'vue'
import CompassLogo from '@/components/CompassLogo.vue'
import { Mail, Lock, Eye, EyeOff, ArrowRight, CheckCircle2, AlertCircle } from 'lucide-vue-next'

const email = ref<string>('')
const password = ref<string>('')
const rememberMe = ref<boolean>(true)
const showPassword = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const message = ref<{ type: 'success' | 'error'; text: string } | null>(null)

function handleSubmit() {
  if (!email.value || !password.value) {
    message.value = {
      type: 'error',
      text: 'Please provide both your Liceo email and password.',
    }
    return
  }

  isLoading.value = true
  message.value = null

  setTimeout(() => {
    isLoading.value = false
    message.value = {
      type: 'success',
      text: `Welcome back, Licean! Redirecting to campus navigation map...`,
    }
  }, 900)
}


</script>

<template>
  <div class="auth-card">
    <!-- Center Branding Header -->
    <div class="auth-header">
      <CompassLogo :size="78" />
      <h1 class="auth-title">Welcome Back Licean!</h1>
      <p class="auth-subtitle">
        Enter your student or faculty credentials to access campus navigation.
      </p>
    </div>

    <!-- Alert Notification Message -->
    <div
      v-if="message"
      :class="['alert', message.type === 'success' ? 'alert-success' : 'alert-error']"
      role="alert"
    >
      <component :is="message.type === 'success' ? CheckCircle2 : AlertCircle" :size="18" />
      <span>{{ message.text }}</span>
    </div>

    <!-- Login Form -->
    <form id="login-form" class="auth-form" @submit.prevent="handleSubmit" novalidate>
      <!-- Email Field -->
      <div class="form-group">
        <label for="email-input" class="form-label">
          <span>Email</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <Mail :size="18" />
          </span>
          <input
            id="email-input"
            v-model="email"
            type="email"
            class="form-input has-left-icon"
            placeholder="you@liceo.edu.ph"
            autocomplete="email"
            required
          />
        </div>
      </div>

      <!-- Password Field -->
      <div class="form-group">
        <label for="password-input" class="form-label">
          <span>Password</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <Lock :size="18" />
          </span>
          <input
            id="password-input"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            class="form-input has-left-icon has-right-icon"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
          <button
            type="button"
            class="input-icon-right"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
            @click="showPassword = !showPassword"
          >
            <EyeOff v-if="showPassword" :size="18" />
            <Eye v-else :size="18" />
          </button>
        </div>
      </div>

      <!-- Remember Me & Forgot Password Row -->
      <div class="form-extra-row">
        <label class="checkbox-label">
          <input v-model="rememberMe" type="checkbox" />
          <span>Remember me</span>
        </label>
        <router-link
          id="forgot-password-link"
          to="/forgot-password"
          class="inline-link forgot-link"
        >
          Forgot password?
        </router-link>
      </div>

      <!-- Submit Button -->
      <button
        id="login-submit-btn"
        type="submit"
        class="btn btn-primary"
        :disabled="isLoading"
      >
        <span v-if="isLoading" class="btn-spinner"></span>
        <span v-else>Continue</span>
        <ArrowRight v-if="!isLoading" :size="18" />
      </button>
    </form>

    <!-- Sign up Footer Link -->
    <div class="auth-footer">
      <span>Don't have an account?</span>
      <router-link id="signup-link" to="/register" class="inline-link" style="margin-left: 6px;">
        Sign up
      </router-link>
    </div>
  </div>
</template>

<style scoped>


.forgot-link {
  font-size: 0.82rem;
}

.btn-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
