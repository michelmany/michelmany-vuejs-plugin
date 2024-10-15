<script>
import { defineComponent, onMounted, ref } from 'vue';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineController,
} from 'chart.js';

// Register chart.js components
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineController,
);

export default defineComponent({
  name: 'LineChart',
  props: {
    chartData: {
      type: Object,
      required: true,
    },
    chartOptions: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const canvasRef = ref(null);

    onMounted(() => {
      if (canvasRef.value) {
        new ChartJS(canvasRef.value.getContext('2d'), {
          type: 'line',
          data: props.chartData,
          options: props.chartOptions,
        });
      } else {
        console.error('Failed to get canvas context');
      }
    });

    return {
      canvasRef,
    };
  },
});
</script>

<template>
  <div>
    <canvas ref="canvasRef"></canvas>
  </div>
</template>