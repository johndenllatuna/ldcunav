import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import ForgotPasswordView from '../views/ForgotPasswordView.vue'
import ExploreView from '../views/ExploreView.vue'

import MapView from '../views/MapView.vue'
import SearchView from '../views/SearchView.vue'
import SavedView from '../views/SavedView.vue'
import ProfileView from '../views/ProfileView.vue'

import AccountSettingsView from '../views/AccountSettingsView.vue'
import PrivacyView from '../views/PrivacyView.vue'
import TermsView from '../views/TermsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [

    // =========================================
    // DEFAULT
    // =========================================

    {
      path: '/',
      redirect: '/login'
    },

    // =========================================
    // AUTH
    // =========================================

    {
      path: '/login',
      name: 'login',
      component: LoginView
    },

    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },

    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPasswordView
    },

    // =========================================
    // MAIN DASHBOARD
    // =========================================

    {
      path: '/home',
      name: 'home',
      component: HomeView
    },

    {
      path: '/explore',
      name: 'explore',
      component: ExploreView
    },

    // =========================================
    // SIDEBAR / BOTTOM NAV
    // =========================================

    {
      path: '/map',
      name: 'map',
      component: MapView
    },

    {
      path: '/search',
      name: 'search',
      component: SearchView
    },

    {
      path: '/saved',
      name: 'saved',
      component: SavedView
    },

    {
      path: '/profile',
      name: 'profile',
      component: ProfileView
    },

    // =========================================
    // PROFILE SETTINGS
    // =========================================

    {
      path: '/account-settings',
      name: 'account-settings',
      component: AccountSettingsView
    },

    {
      path: '/privacy',
      name: 'privacy',
      component: PrivacyView
    },

    {
      path: '/terms',
      name: 'terms',
      component: TermsView
    }

  ]
})

export default router