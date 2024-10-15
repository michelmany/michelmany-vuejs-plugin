<template>
  <div class="graph-page">
    <h2>Graph</h2>
    <div v-if="processedGraphData.length" class="chart-container">
      <line-chart :chart-data="chartData" :chart-options="chartOptions"></line-chart>
    </div>
    <div v-else>
      <p>Loading data...</p>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex'; // Add mapActions for Vuex actions
import LineChart from './LineChart.vue';

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
            label: 'Value',
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
              text: 'Date',
            },
          },
          y: {
            display: true,
            title: {
              display: true,
              text: 'Value',
            },
          },
        },
      };
    },
  },
  methods: {
    ...mapActions(['fetchData']), // Map the fetchData action to this component
  },
  created() {
    this.fetchData(); // Call fetchData when the component is created
  },
};
</script>