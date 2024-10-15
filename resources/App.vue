<template>
  <div class="mmvuejs-admin-header">
    <h1>{{ __('Michel Many VueJS Plugin', 'mmvuejs') }}</h1>
  </div>
  <nav class="mmvuejs-admin-nav container">
    <router-link to="/">{{ __('Table', 'mmvuejs') }}</router-link>
    <router-link to="/graph">{{ __('Graph', 'mmvuejs') }}</router-link>
    <router-link to="/settings">{{ __('Settings', 'mmvuejs') }}</router-link>
  </nav>
  <div class="mmvuejs-admin-body container">
    <router-view></router-view>
  </div>
</template>

<script>
import {mapActions} from 'vuex';
const {__} = wp.i18n;

export default {
  name: 'App',
  methods: {
    ...mapActions(['fetchSettings']),
    __,
  },
  created() {
    this.fetchSettings();
  },
};
</script>