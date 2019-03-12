import Vue from 'vue';

export const state = {
    mobile: false,
    router: {
        to: '',
        from: '',
    }
}
export const mutations = {
    ['IS_MOBILE'](state) {
        if (screen.width > 450) {
            state.mobile = false;
        } else {
            state.mobile = true;
        }
        // console.log(state.mobile);
    },
    ['ROUTER_NAME'](state, playload) {
        state.router = playload;
        // console.log(playload.from, playload.to);
    },
    ['ROUTER_DEFAULT'](state) {
        state.router.to = '';
        state.router.from = '';
    }
}