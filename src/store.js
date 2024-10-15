import {createStore} from 'vuex';
import axios from './axios';

export default createStore({
    state() {
        return {
            settings: {
                numberOfRows: 5,
                humanReadableDate: true,
                emails: [],
            },
            tableData: null,
            graphData: {},
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
            return axios.get('/settings').then(response => {
                commit('setSettings', response.data);
            });
        },
        updateSetting({commit}, {key, value}) {
            return axios.post(`/settings/${key}`, {value}).then(response => {
                commit('updateSetting', {key, value: response.data[key]});
            });
        },
        fetchData({commit}) {
            return axios.get('/data').then(response => {
                const apiData = response.data;
                commit('setTableData', apiData.table);
                commit('setGraphData', apiData.graph);
            });
        },
    },
});