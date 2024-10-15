<template>
  <div class="graph-page">
    <h2 class="mmvuejs-separate-sections mt-0">{{ __('Graph', 'mmvuejs') }}</h2>
    <div v-if="processedGraphData.length" class="chart-container">
      <line-chart :chart-data="chartData" :chart-options="chartOptions"></line-chart>
    </div>
    <div v-else>
      <p>{{ __('Loading data...', 'mmvuejs') }}</p>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex';
import LineChart from './LineChart.vue';

const {__} = wp.i18n;

export default {
  name: 'Graph',
  components: {
    LineChart,
  },
  computed: {
    ...mapState(['graphData']),
    processedGraphData() {
      return this.graphData ? Object.values(this.graphData) : [];
    },
    chartData() {
      return {
        labels: this.processedGraphData.map((item) =>
            new Date(item.date * 1000).toLocaleDateString(),
        ),
        datasets: [
          {
            label: __('Value', 'mmvuejs'),
            data: this.processedGraphData.map((item) => item.value),
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1,
          },
        ],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            display: true,
            title: {
              display: true,
              text: __('Date', 'mmvuejs'),
            },
          },
          y: {
            display: true,
            title: {
              display: true,
              text: __('Value', 'mmvuejs'),
            },
          },
        },
      };
    },
  },
  methods: {
    ...mapActions(['fetchData']),
    __,
  },
  created() {
    this.fetchData();
  },
};
</script>