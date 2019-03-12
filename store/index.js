import Vue from 'vue';
import Vuex from 'vuex';
import { state, mutations } from './mutations';
import * as getters from './getter';
import * as actions from './action';

Vue.use(Vuex);

const createStore = () => {
    return new Vuex.Store({
        state,
        mutations,
        getters,
        actions,

        // 使用嚴格模式時，禁止直接修改 state
        strict: true
    });
};

export default createStore;