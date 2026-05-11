<template>
    <div id="app">
        <router-view v-if="routerAlive" />
    </div>
</template>

<script>

import { apiGetConfig } from '@/api/app'
export default {
    data() {
        return {
            routerAlive: true
        }
    },
    provide() {
        return {
            reload: this.reload
        }
    },
    methods: {
        reload() {
            this.routerAlive = false
            this.$nextTick(() => {
                this.routerAlive = true
            })
        },
    },
    created(){
        apiGetConfig().then((data) => {
            this.$store.commit('setConfig', data)
            let favicon = document.querySelector('link[rel="icon"]')
            if (favicon) {
                favicon.href = data.favicon
                return
            }
            favicon = document.createElement('link')
            favicon.rel = 'icon'
            favicon.href = data.favicon
            document.head.appendChild(favicon)
        })
    }
}
</script>

<style lang="scss">
@import './styles/index.scss';
#app {
    min-width: 1200px;
}
</style>
