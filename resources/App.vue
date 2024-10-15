<template>
  <div class="mmvuejs-admin-header">
    <h1>Michel Many VueJS Plugin</h1>
  </div>
  <nav class="mmvuejs-admin-nav container">
    <router-link to="/">Table</router-link>
    <router-link to="/graph">Graph</router-link>
    <router-link to="/settings">Settings</router-link>
  </nav>
  <div class="mmvuejs-admin-body container">
    <router-view></router-view>
  </div>
</template>

<script>
import {mapActions} from 'vuex';

export default {
  name: 'App',
  methods: {
    ...mapActions(['fetchSettings']),
  },
  created() {
    this.fetchSettings();
  },
};
</script>