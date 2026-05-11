/**
 * Chat serve
 */

import Socket from "./socket"
import config from '@/config'
import store from '@/store'


const socketServe = new Socket({
    ws: config['ChatWss'],

    params: {
        token: store.getters.token,
        type: 'kefu',
        client: 5,
    },

    open() {

    },

    message(data) {

    },

    error(e) {

    },

    close() {

    }
})







