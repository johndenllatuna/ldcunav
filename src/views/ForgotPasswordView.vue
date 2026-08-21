<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import CompassLogo from '@/components/CompassLogo.vue'
import {
  Mail,
  ArrowRight,
  ArrowLeft,
  KeyRound,
  CheckCircle2,
  AlertCircle,
  RefreshCw,
  Lock,
  Eye,
  EyeOff
} from 'lucide-vue-next'

const email = ref<string>('')
const step = ref<'request' | 'verify' | 'completed'>('request')
const isLoading = ref<boolean>(false)
const message = ref<{ type: 'success' | 'error' | 'info'; text: string } | null>(null)

// Verification OTP & New Password state
const otp = ref<string[]>(['', '', '', '', '', ''])
const newPassword = ref<string>('')
const confirmPassword = ref<string>('')
const showNewPassword = ref<boolean>(false)
const resendTimer = ref<number>(60)
let timerInterval: number | null = null

function startResendCountdown() {
  resendTimer.value = 60
  if (timerInterval) clearInterval(timerInterval)
  timerInterval = window.setInterval(() => {
    if (resendTimer.value > 0) {
      resendTimer.value -= 1
    } else {
      if (timerInterval) clearInterval(timerInterval)
    }
  }, 1000)
}

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

function handleSendReset() {
  if (!email.value) {
    message.value = {
      type: 'error',
      text: 'Please enter your Liceo email address.',
    }
    return
  }

  isLoading.value = true
  message.value = null

  setTimeout(() => {
    isLoading.value = false
    step.value = 'verify'
    startResendCountdown()
    message.value = {
      type: 'info',
      text: `A reset code has been dispatched to ${email.value}.`,
    }
  }, 1000)
}

function handleOtpInput(index: number, event: Event) {
  const input = event.target as HTMLInputElement
  const val = input.value

  // Keep single character
  if (val.length > 1) {
    otp.value[index] = val.slice(-1)
  }

  // Auto focus next input
  if (val && index < 5) {
    const nextInput = document.getElementById(`otp-digit-${index + 1}`) as HTMLInputElement
    if (nextInput) nextInput.focus()
  }
}

function handleOtpKeydown(index: number, event: KeyboardEvent) {
  if (event.key === 'Backspace' && !otp.value[index] && index > 0) {
    const prevInput = document.getElementById(`otp-digit-${index - 1}`) as HTMLInputElement
    if (prevInput) {
      prevInput.focus()
      otp.value[index - 1] = ''
    }
  }
}

function handleResetPassword() {
  const code = otp.value.join('')
  if (code.length < 6) {
    message.value = {
      type: 'error',
      text: 'Please enter the complete 6-digit verification code.',
    }
    return
  }

  if (!newPassword.value || newPassword.value.length < 6) {
    message.value = {
      type: 'error',
      text: 'New password must be at least 6 characters.',
    }
    return
  }

  if (newPassword.value !== confirmPassword.value) {
    message.value = {
      type: 'error',
      text: 'Passwords do not match.',
    }
    return
  }

  isLoading.value = true
  message.value = null

  setTimeout(() => {
    isLoading.value = false
    step.value = 'completed'
    message.value = {
      type: 'success',
      text: 'Your password has been successfully reset! You can now sign in with your new password.',
    }
  }, 1200)
}

function resendCode() {
  if (resendTimer.value > 0) return
  message.value = null
  startResendCountdown()
  message.value = {
    type: 'info',
    text: `A new verification code was sent to ${email.value}.`,
  }
}
</script>

<template>
  <div class="auth-card">
    <!-- Header Branding -->
    <div class="auth-header">
      <CompassLogo :size="78" />

      <!-- Step 1 Title -->
      <template v-if="step === 'request'">
        <h1 class="auth-title">Forgot Password?</h1>
        <p class="auth-subtitle">
          Enter your registered Licean email and we'll send you instructions to reset your account password.
        </p>
      </template>

      <!-- Step 2 Title -->
      <template v-else-if="step === 'verify'">
        <h1 class="auth-title">Verify & Reset</h1>
        <p class="auth-subtitle">
          Enter the 6-digit code sent to <strong style="color: #111827;">{{ email }}</strong> and choose a new password.
        </p>
      </template>

      <!-- Step 3 Title -->
      <template v-else>
        <h1 class="auth-title">Password Reset Complete!</h1>
        <p class="auth-subtitle">
          Your account security credentials have been updated successfully.
        </p>
      </template>
    </div>

    <!-- Alert Notification Message -->
    <div
      v-if="message"
      :class="['alert', message.type === 'success' ? 'alert-success' : message.type === 'error' ? 'alert-error' : 'alert-info']"
      role="alert"
    >
      <component
        :is="message.type === 'success' ? CheckCircle2 : message.type === 'error' ? AlertCircle : KeyRound"
        :size="18"
      />
      <span>{{ message.text }}</span>
    </div>

    <!-- STEP 1: Email Request Form -->
    <form
      v-if="step === 'request'"
      id="forgot-form"
      class="auth-form"
      @submit.prevent="handleSendReset"
      novalidate
    >
      <div class="form-group">
        <label for="forgot-email-input" class="form-label">
          <span>Licean Email Address</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <Mail :size="18" />
          </span>
          <input
            id="forgot-email-input"
            v-model="email"
            type="email"
            class="form-input has-left-icon"
            placeholder="you@liceo.edu.ph"
            autocomplete="email"
            required
            autofocus
          />
        </div>
      </div>

      <button
        id="send-reset-btn"
        type="submit"
        class="btn btn-primary"
        :disabled="isLoading"
      >
        <span v-if="isLoading" class="btn-spinner"></span>
        <span v-else>Send Reset Link</span>
        <ArrowRight v-if="!isLoading" :size="18" />
      </button>

      <router-link to="/" class="btn btn-secondary back-btn">
        <ArrowLeft :size="16" />
        <span>Back to Sign In</span>
      </router-link>
    </form>

    <!-- STEP 2: Code Verification & New Password Form -->
    <form
      v-else-if="step === 'verify'"
      id="verify-reset-form"
      class="auth-form"
      @submit.prevent="handleResetPassword"
      novalidate
    >
      <!-- 6-digit OTP Inputs -->
      <div class="form-group">
        <label class="form-label">
          <span>6-Digit Verification Code</span>
          <button
            type="button"
            class="resend-btn"
            :disabled="resendTimer > 0"
            @click="resendCode"
          >
            <RefreshCw :size="12" :class="{ 'spin-icon': isLoading }" />
            <span v-if="resendTimer > 0">Resend in {{ resendTimer }}s</span>
            <span v-else>Resend Code</span>
          </button>
        </label>
        <div class="otp-container">
          <input
            v-for="(digit, idx) in otp"
            :id="`otp-digit-${idx}`"
            :key="idx"
            v-model="otp[idx]"
            type="text"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="1"
            class="otp-digit-box"
            @input="handleOtpInput(idx, $event)"
            @keydown="handleOtpKeydown(idx, $event)"
          />
        </div>
      </div>

      <!-- New Password -->
      <div class="form-group">
        <label for="new-password-input" class="form-label">
          <span>New Password</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <Lock :size="18" />
          </span>
          <input
            id="new-password-input"
            v-model="newPassword"
            :type="showNewPassword ? 'text' : 'password'"
            class="form-input has-left-icon has-right-icon"
            placeholder="••••••••"
            autocomplete="new-password"
            required
          />
          <button
            type="button"
            class="input-icon-right"
            :aria-label="showNewPassword ? 'Hide password' : 'Show password'"
            @click="showNewPassword = !showNewPassword"
          >
            <EyeOff v-if="showNewPassword" :size="18" />
            <Eye v-else :size="18" />
          </button>
        </div>
      </div>

      <!-- Confirm New Password -->
      <div class="form-group">
        <label for="confirm-password-input" class="form-label">
          <span>Confirm New Password</span>
        </label>
        <div class="input-wrapper">
          <span class="input-icon-left">
            <Lock :size="18" />
          </span>
          <input
            id="confirm-password-input"
            v-model="confirmPassword"
            :type="showNewPassword ? 'text' : 'password'"
            class="form-input has-left-icon"
            placeholder="••••••••"
            autocomplete="new-password"
            required
          />
        </div>
      </div>

      <!-- Submit Reset Button -->
      <button
        id="confirm-reset-btn"
        type="submit"
        class="btn btn-primary"
        :disabled="isLoading"
      >
        <span v-if="isLoading" class="btn-spinner"></span>
        <span v-else>Update Password</span>
      </button>

      <button
        type="button"
        class="btn btn-secondary back-btn"
        @click="step = 'request'"
      >
        <ArrowLeft :size="16" />
        <span>Change Email</span>
      </button>
    </form>

    <!-- STEP 3: Completed State -->
    <div v-else class="completed-state">
      <div class="success-icon-wrap">
        <CheckCircle2 :size="48" color="#16a34a" />
      </div>
      <router-link to="/" class="btn btn-primary" style="margin-top: 1rem;">
        <span>Return to Sign In</span>
        <ArrowRight :size="18" />
      </router-link>
    </div>

    <!-- Sign In Alternative Footer -->
    <div v-if="step !== 'completed'" class="auth-footer">
      <span>Remember your credentials?</span>
      <router-link to="/" class="inline-link" style="margin-left: 6px;">
        Sign in
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.back-btn {
  margin-top: 0.2rem;
  font-size: 0.88rem;
  padding: 10px 18px;
}

.otp-container {
  display: flex;
  gap: 8px;
  justify-content: space-between;
  width: 100%;
}

.otp-digit-box {
  width: 100%;
  max-width: 48px;
  height: 52px;
  text-align: center;
  font-size: 1.35rem;
  font-weight: 700;
  font-family: var(--font-logo);
  color: #111827;
  background-color: var(--input-bg);
  border: 1.5px solid transparent;
  border-radius: var(--radius-md);
  outline: none;
  transition: all 0.2s ease;
}

.otp-digit-box:hover {
  background-color: var(--input-bg-hover);
}

.otp-digit-box:focus {
  background-color: var(--input-bg-focus);
  border-color: var(--btn-blue);
  box-shadow: 0 0 0 3px var(--input-focus-ring);
}

.resend-btn {
  background: none;
  border: none;
  color: var(--btn-blue);
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0;
  transition: color 0.15s ease;
}

.resend-btn:disabled {
  color: var(--text-light);
  cursor: not-allowed;
}

.resend-btn:hover:not(:disabled) {
  color: var(--btn-blue-hover);
  text-decoration: underline;
}

.completed-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 0.5rem 0;
}

.success-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: var(--btn-green-light);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
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
