<script setup lang="ts">
import { ref, computed } from 'vue'
import CompassLogo from '@/components/CompassLogo.vue'
import {
  Mail,
  User,
  GraduationCap,
  Lock,
  Eye,
  EyeOff,
  UserPlus,
  CheckCircle2,
  AlertCircle
} from 'lucide-vue-next'

const email = ref<string>('')
const fullName = ref<string>('')
const course = ref<string>('')
const password = ref<string>('')
const agreeTerms = ref<boolean>(true)
const showPassword = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const message = ref<{ type: 'success' | 'error'; text: string } | null>(null)

// Password strength calculation
const passwordScore = computed(() => {
  const pwd = password.value
  if (!pwd) return 0
  let score = 0
  if (pwd.length >= 8) score += 1
  if (/[A-Z]/.test(pwd)) score += 1
  if (/[0-9]/.test(pwd)) score += 1
  if (/[^A-Za-z0-9]/.test(pwd)) score += 1
  return score
})

const strengthLabel = computed(() => {
  switch (passwordScore.value) {
    case 1:
      return { text: 'Weak', class: 'active-weak' }
    case 2:
      return { text: 'Fair', class: 'active-fair' }
    case 3:
      return { text: 'Good', class: 'active-good' }
    case 4:
      return { text: 'Strong', class: 'active-strong' }
    default:
      return { text: 'Minimum 8 characters', class: '' }
  }
})

function handleRegister() {
  if (!email.value || !fullName.value || !course.value || !password.value) {
    message.value = {
      type: 'error',
      text: 'Please complete all required registration fields.',
    }
    return
  }

  if (password.value.length < 6) {
    message.value = {
      type: 'error',
      text: 'Password must be at least 6 characters long.',
    }
    return
  }

  isLoading.value = true
  message.value = null

  setTimeout(() => {
    isLoading.value = false
    message.value = {
      type: 'success',
      text: `Account created successfully for ${fullName.value}! You can now sign in.`,
    }
  }, 1000)
}
</script>

<template>
  <div class="auth-card">
    <!-- Center Branding Header -->
    <div class="auth-header">
      <CompassLogo :size="78" />
      <h1 class="auth-title">Join the Licean Community!</h1>
      <p class="auth-subtitle">
        Create your student navigator account to explore campus routes, buildings, and events.
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

    <!-- Registration Form -->
    <form id="register-form" class="auth-form" @submit.prevent="handleRegister" novalidate>
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

      <!-- Full Name Field -->
      <div class="form-group">
        <label for="fullname-input" class="form-label">
          <span>Full Name</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <User :size="18" />
          </span>
          <input
            id="fullname-input"
            v-model="fullName"
            type="text"
            class="form-input has-left-icon"
            placeholder="Juan Dela Cruz"
            autocomplete="name"
            required
          />
        </div>
      </div>

      <!-- Course / Program Field -->
      <div class="form-group">
        <label for="course-input" class="form-label">
          <span>College / Program</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <GraduationCap :size="18" />
          </span>
          <select
            id="course-input"
            v-model="course"
            class="form-select has-left-icon"
            required
          >
            <option value="" disabled selected>Select your degree program</option>
            <optgroup label="College of Information Technology">
              <option value="BSCS">BS Computer Science</option>
              <option value="BSIT">BS Information Technology</option>
              <option value="BSIS">BS Information Systems</option>
            </optgroup>
            <optgroup label="College of Nursing & Allied Health">
              <option value="BSN">BS Nursing</option>
              <option value="BSPharma">BS Pharmacy</option>
              <option value="BSRT">BS Radiologic Technology</option>
              <option value="BSMedTech">BS Medical Laboratory Science</option>
            </optgroup>
            <optgroup label="College of Business & Accountancy">
              <option value="BSA">BS Accountancy</option>
              <option value="BSBA">BS Business Administration</option>
              <option value="BSHM">BS Hospitality Management</option>
              <option value="BSTM">BS Tourism Management</option>
            </optgroup>
            <optgroup label="College of Engineering">
              <option value="BSCE">BS Civil Engineering</option>
              <option value="BSEE">BS Electrical Engineering</option>
              <option value="BSME">BS Mechanical Engineering</option>
              <option value="BSECE">BS Electronics Engineering</option>
            </optgroup>
            <optgroup label="College of Arts and Sciences & Education">
              <option value="BSPsych">BS Psychology</option>
              <option value="BAComm">BA Communication</option>
              <option value="BAPolSci">BA Political Science</option>
              <option value="BSEd">Bachelor of Secondary Education</option>
            </optgroup>
            <optgroup label="Other Department">
              <option value="SHS">Senior High School</option>
              <option value="FACULTY">Faculty / Staff Member</option>
              <option value="GUEST">Campus Visitor / Guest</option>
            </optgroup>
          </select>
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
            placeholder="Create secure password"
            autocomplete="new-password"
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

        <!-- Live Password Strength Meter -->
        <div v-if="password" class="password-strength-wrap">
          <div class="strength-bars">
            <div
              v-for="idx in 4"
              :key="idx"
              class="strength-bar"
              :class="{ [strengthLabel.class]: idx <= passwordScore }"
            ></div>
          </div>
          <div class="strength-text">
            <span>Strength</span>
            <span>{{ strengthLabel.text }}</span>
          </div>
        </div>
      </div>

      <!-- Submit Green Button -->
      <button
        id="register-submit-btn"
        type="submit"
        class="btn btn-success"
        :disabled="isLoading"
      >
        <span v-if="isLoading" class="btn-spinner"></span>
        <span v-else>Register</span>
        <UserPlus v-if="!isLoading" :size="18" />
      </button>

      <!-- Terms & Privacy Note -->
      <p class="terms-text">
        By continuing, you agree to our
        <a href="#terms" @click.prevent>Terms</a> and
        <a href="#privacy" @click.prevent>Privacy Policy</a>
      </p>
    </form>

    <!-- Sign In Footer Link -->
    <div class="auth-footer">
      <span>Already have an account?</span>
      <router-link id="signin-link" to="/" class="inline-link" style="margin-left: 6px;">
        Sign in
      </router-link>
    </div>
  </div>
</template>

<style scoped>
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
