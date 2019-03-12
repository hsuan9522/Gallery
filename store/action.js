import Vue from 'vue';
import mutations from './mutations';

export const isMobile = ({ commit }) => {
    commit('IS_MOBILE');
}

export const routerName = ({ commit }, playload) => {
    commit('ROUTER_NAME', playload);
}

export const routerDefault = ({ commit }) => {
    commit('ROUTER_DEFAULT');
}