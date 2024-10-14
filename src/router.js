import {createRouter, createWebHashHistory} from 'vue-router';
import Table from './components/Table.vue';
import Settings from './components/Settings.vue';
import Graph from './components/Graph.vue';

const routes = [
    {path: '/', component: Table},
    {path: '/settings', component: Settings},
    {path: '/graph', component: Graph},
];

const router = createRouter(
    {
        history: createWebHashHistory(),
        routes,
    },
);

export default router;