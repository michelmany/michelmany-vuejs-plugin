import {createStore} from 'vuex';
import axios from 'axios';

const SETTINGS_URL = '/wp-json/mmvuejs/v1/settings';
const DATA_URL = '/wp-json/mmvuejs/v1/data';

export default createStore({
    state() {
        return {
            settings: {
                numberOfRows: 5,
                humanReadableDate: true,
                emails: [],
            },
            tableData: [],
            graphData: [],
        };
    },
    mutations: {
        setSettings(state, settings) {
            state.settings = settings;
        },
        updateSettings(state, {key, value}) {
            state.settings[key] = value;
        },
        setTableData(state, data) {
            state.tableData = data;
        },
        setGraphData(state, data) {
            state.graphData = data;
        },
    },
    actions: {
        fetchSettings({commit}) {
            return axios.get(SETTINGS_URL).then(response => {
                commit('setSettings', response.data);
            });
        },
        updateSettings({commit}, settings) {
            return axios.post(SETTINGS_URL, settings).then(response => {
                commit('setSettings', response.data);
            });
        },
        fetchData({commit}) {
            return axios.get(DATA_URL).then(response => {
                commit('setTableData', response.data.table);
                commit('setGraphData', response.data.graph);
            });
        },
    },
});