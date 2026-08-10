<script setup lang="ts">
import { ref } from 'vue'

const email = ref<string>('')
const fullName = ref<string>('')
const course = ref<string>('')
const password = ref<string>('')

function handleRegister() {
  // TODO: wire up to auth API
  console.log('Registration attempted with:', {
    email: email.value,
    fullName: fullName.value,
    course: course.value,
    password: password.value,
  })
}
</script>

<template>
  <div class="register-page">
    <div class="register-card">

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
      <h1 class="title">Join the Licean Community!</h1>

      <!-- Registration Form -->
      <form id="register-form" class="register-form" @submit.prevent="handleRegister" novalidate>

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
          <label for="fullname-input">Full Name</label>
          <input
            id="fullname-input"
            v-model="fullName"
            type="text"
            placeholder="Enter full name"
            autocomplete="name"
            required
          />
        </div>

        <div class="form-group">
          <label for="course-input">Course</label>
          <select
            id="course-input"
            v-model="course"
            required
          >
            <option value="" disabled selected>Select your course</option>
            <option value="BSCS">BS Computer Science</option>
            <option value="BSIT">BS Information Technology</option>
            <option value="BSN">BS Nursing</option>
            <option value="BSBA">BS Business Administration</option>
            <!-- Add more courses as needed -->
          </select>
        </div>

        <div class="form-group">
          <label for="password-input">Password</label>
          <input
            id="password-input"
            v-model="password"
            type="password"
            placeholder="••••••••"
            autocomplete="new-password"
            required
          />
        </div>

        <button id="register-submit-btn" type="submit" class="btn-register">
          Register
        </button>

      </form>

      <!-- Sign in link -->
      <p class="signin-text">
        Already have an account?
        <router-link id="signin-link" to="/" class="signin-link">Sign in</router-link>
      </p>

    </div>
  </div>
</template>

<style scoped>
.register-page {
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

.register-card {
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
.register-form {
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

input, select {
  width: 100%;
  padding: 10px 16px;
  background-color: #f3f4f6;
  border: none;
  border-radius: 20px;
  font-size: 0.92rem;
  color: #111827;
  outline: none;
  transition: box-shadow 0.2s ease, background-color 0.2s ease;
  appearance: none; /* For select */
}

/* Custom dropdown arrow for select */
select {
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23111827%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
  background-repeat: no-repeat;
  background-position: right 16px top 50%;
  background-size: 11px auto;
}

input::placeholder, select:invalid {
  color: #9ca3af;
}

input:focus, select:focus {
  background-color: #e9ecf1;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
}

/* ── Button ───────────────────────────────────────────── */
.btn-register {
  width: 100%;
  padding: 11px;
  margin-top: 0.2rem;
  background-color: #22c55e;
  color: #ffffff;
  font-size: 0.95rem;
  font-weight: 600;
  font-family: 'Inter', system-ui, sans-serif;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  letter-spacing: 0.02em;
  transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
}

.btn-register:hover {
  background-color: #16a34a;
  box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45);
}

.btn-register:active {
  transform: scale(0.98);
  background-color: #15803d;
}

/* ── Sign in ──────────────────────────────────────────── */
.signin-text {
  font-size: 0.85rem;
  color: #6b7280;
  text-align: center;
  margin-top: 0.1rem;
}

.signin-link {
  color: #2563eb;
  font-weight: 600;
  text-decoration: none;
  margin-left: 4px;
  transition: color 0.15s ease;
}

.signin-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}
</style>

