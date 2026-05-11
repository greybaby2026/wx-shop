<template>
    <div>
        <template v-if="$store.state.publicData.shop.pc_status">
            <div class="default-layout flex-col">
                <v-header></v-header>
                <main class="content-container wrapper1180 flex-1">
                    <nuxt />
                </main>
                <v-footer></v-footer>
                <float-nav></float-nav>
            </div>
        </template>

        <template v-else>
            <div class="default-layout flex row-center muted xxl">
                商城已关闭
            </div>
        </template>
    </div>
</template>
<script>
import VHeader from "~/components/widgets/header/index";
import VFooter from "~/components/widgets/footer";
import FloatNav from "~/components/widgets/fixed";
export default {
    components: {
        VHeader,
        VFooter,
        FloatNav,
    },

    created() {
        console.log(this.$store.state.publicData.shop.pc_status);
    },
    mounted() {
        const { clarity_app_id } = this.$store.getters.getSiteStatistics;
        if (clarity_app_id) {
            (function (c, l, a, r, i, t, y) {
                c[a] =
                    c[a] ||
                    function () {
                        (c[a].q = c[a].q || []).push(arguments);
                    };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", clarity_app_id);
        }
    },
};
</script>
<style lang="scss" scoped>
.default-layout {
    min-height: 100vh;
    main {
        padding-bottom: 38px;
    }
}
</style>
