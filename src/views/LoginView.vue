<script setup lang="ts">
import { ref } from 'vue'

const email = ref<string>('')
const password = ref<string>('')

function handleSubmit() {
  // TODO: wire up to auth API
  console.log('Login attempted with:', email.value)
}
</script>

<template>
  <div class="login-page">
    <div class="login-card">

      <!-- Compass Logo Placeholder -->
      <div class="logo-wrap">
        <svg
          class="compass-icon"
          viewBox="0 0 100 100"
          xmlns="http://www.w3.org/2000/svg"
          aria-label="Compass logo"
          role="img"
        >
          <!-- Outer ring -->
          <circle cx="50" cy="50" r="46" fill="none" stroke="#6b1212" stroke-width="4" />
          <!-- Inner ring -->
          <circle cx="50" cy="50" r="6" fill="#6b1212" />
          <!-- North needle (maroon) -->
          <polygon points="50,12 44,50 50,44 56,50" fill="#6b1212" />
          <!-- South needle (light maroon) -->
          <polygon points="50,88 44,50 50,56 56,50" fill="#b94b4b" />
          <!-- Cardinal markers -->
          <text x="50" y="10" text-anchor="middle" font-size="8" font-family="'Orbitron', sans-serif" fill="#6b1212" font-weight="700">N</text>
          <text x="50" y="97" text-anchor="middle" font-size="8" font-family="'Orbitron', sans-serif" fill="#6b7280">S</text>
          <text x="96" y="53" text-anchor="middle" font-size="8" font-family="'Orbitron', sans-serif" fill="#6b7280">E</text>
          <text x="4" y="53" text-anchor="middle" font-size="8" font-family="'Orbitron', sans-serif" fill="#6b7280">W</text>
        </svg>
      </div>

      <!-- Heading -->
      <h1 class="title">Welcome Back Licean!</h1>

      <!-- Login Form -->
      <form id="login-form" class="login-form" @submit.prevent="handleSubmit" novalidate>

        <div class="form-group">
          <label for="email-input">Email</label>
          <input
            id="email-input"
            v-model="email"
            type="email"
            placeholder="you@liceo.edu.ph"
            autocomplete="email"
            required
          />
        </div>

        <div class="form-group">
          <label for="password-input">Password</label>
          <input
            id="password-input"
            v-model="password"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
        </div>

        <button id="login-submit-btn" type="submit" class="btn-continue">
          Continue
        </button>

      </form>

      <!-- Sign up link -->
      <p class="signup-text">
        Don't have an account?
        <router-link id="signup-link" to="/register" class="signup-link">Sign up</router-link>
      </p>

    </div>
  </div>
</template>

<style scoped>
.login-page {
  height: 100%;
  padding-top: 4rem;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  padding-left: 1rem;
  padding-right: 1rem;
  background-color: #ffffff;
}

.login-card {
  width: 100%;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

/* ── Logo ─────────────────────────────────────────────── */
.logo-wrap {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
  box-shadow: 0 4px 20px rgba(107, 18, 18, 0.12);
  margin-bottom: 0;
}

.compass-icon {
  width: 100%;
  height: 100%;
}

/* ── Heading ──────────────────────────────────────────── */
.title {
  font-family: 'Orbitron', sans-serif;
  font-size: 1.4rem;
  font-weight: 700;
  color: #111827;
  text-align: center;
  letter-spacing: -0.01em;
  margin-bottom: 0;
}

/* ── Form ─────────────────────────────────────────────── */
.login-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #374151;
  letter-spacing: 0.01em;
}

input {
  width: 100%;
  padding: 10px 16px;
  background-color: #f3f4f6;
  border: none;
  border-radius: 20px;
  font-size: 0.92rem;
  color: #111827;
  outline: none;
  transition: box-shadow 0.2s ease, background-color 0.2s ease;
}

input::placeholder {
  color: #9ca3af;
}

input:focus {
  background-color: #e9ecf1;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
}

/* ── Button ───────────────────────────────────────────── */
.btn-continue {
  width: 100%;
  padding: 11px;
  margin-top: 0.2rem;
  background-color: #2563eb;
  color: #ffffff;
  font-size: 0.95rem;
  font-weight: 600;
  font-family: 'Inter', system-ui, sans-serif;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  letter-spacing: 0.02em;
  transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}

.btn-continue:hover {
  background-color: #1d4ed8;
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
}

.btn-continue:active {
  transform: scale(0.98);
  background-color: #1e40af;
}

/* ── Sign up ──────────────────────────────────────────── */
.signup-text {
  font-size: 0.85rem;
  color: #6b7280;
  text-align: center;
  margin-top: 0.1rem;
}

.signup-link {
  color: #2563eb;
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
  transition: color 0.15s ease;
}

.signup-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}
</style>

