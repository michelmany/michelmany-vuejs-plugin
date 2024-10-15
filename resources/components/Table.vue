<template>
  <div class="table-page">
    <div class="mmvuejs-separate-sections">
      <div v-if="tableData">
        <h2 class="mt-0">{{ tableData.title }}</h2>
      </div>
      <div v-else>
        <p>{{ __('Loading data...', 'mmvuejs') }}</p>
      </div>
      <table v-if="displayedData.length" class="mmvuejs-table">
        <thead>
        <tr>
          <th v-for="(header, index) in tableHeaders" :key="index">
            {{ header }}
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(row, index) in displayedData" :key="row.id">
          <td>{{ row.id }}</td>
          <td>
            <a :href="row.url" target="_blank">{{ row.url }}</a>
          </td>
          <td>{{ row.title }}</td>
          <td>{{ row.pageviews }}</td>
          <td>{{ formatDate(row.date) }}</td>
        </tr>
        </tbody>
      </table>
      <p v-else>{{ __('No data available.', 'mmvuejs') }}</p>
    </div>

    <!-- Emails List -->
    <div class="emails-list">
      <h2>{{ __('Emails', 'mmvuejs') }}</h2>
      <ul>
        <li v-for="(email, index) in settings.emails" :key="index">{{ email }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex';

const {__} = wp.i18n;

export default {
  name: 'Table',
  computed: {
    ...mapState(['tableData', 'settings']),
    tableHeaders() {
      return this.tableData?.data?.headers || [];
    },
    displayedData() {
      const data = this.tableData?.data?.rows || [];
      // Slice the data according to the numberOfRows setting
      return data.slice(0, this.settings.numberOfRows);
    },
  },
  methods: {
    __,
    ...mapActions(['fetchData']),
    formatDate(timestamp) {
      if (this.settings.humanReadableDate) {
        // Assuming timestamp is in seconds, multiply by 1000
        const date = new Date(timestamp * 1000);
        return date.toLocaleString();
      } else {
        // Return the Unix timestamp
        return timestamp;
      }
    },
  },
  created() {
    this.fetchData();
  },
  watch: {
    settings: {
      deep: true,
      handler() {
        // Vue's reactivity will handle the updates
      },
    },
  },
};
</script>

<style scoped>
.table-page table {
  width: 100%;
  border-collapse: collapse;
}

.table-page th,
.table-page td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
  background: #fff;
}

.emails-list {
  margin-top: 20px;
}
</style>