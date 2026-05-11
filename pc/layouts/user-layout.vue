<template>
    <div>
        <template v-if="$store.state.publicData.shop.pc_status">
            <div class="default-layout flex-col">
                <v-header></v-header>
                <main class="content-container flex flex-1">
                    <aslide-nav />
                    <div class="content bg-white flex-1">
                        <nuxt />
                    </div>
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
import AslideNav from "~/components/layout/aslideNav";
import FloatNav from "~/components/widgets/fixed";
export default {
    components: {
        VHeader,
        VFooter,
        AslideNav,
        FloatNav,
    },
};
</script>
<style scoped>
.default-layout {
    min-height: 100vh;
}
.content-container {
    padding: 24px 0;
    width: 1180px;
    margin: 0 auto;
    align-items: flex-start;
}
.content-container > .content {
    width: 100%;
    min-height: 680px;
    margin-left: 10px;
}
</style>
