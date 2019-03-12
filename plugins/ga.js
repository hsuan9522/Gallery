import Vue from 'vue'
import VueAnalytics from 'vue-analytics'

export default function ({ app }) {

    Vue.use(VueAnalytics, {
        id: 'UA-119917054-2',
        router: app.router
    })
}
