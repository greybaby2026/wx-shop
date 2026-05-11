<template>
    <div class="footer-container">
        <div class="footer-box flex row-center">
            <div class="server-box flex row-between">
                <div class="flex">
                    <span class="iconfont icon-ziying" style="font-size: 50px;color: white" />
                    <div class="m-l-8 xxl name">自营商城</div>
                </div>
                <div class="flex">
                    <span class="iconfont icon-zhengpin" style="font-size: 50px;color: white" />
                    <div class="m-l-8 xxl name">正品保障</div>
                </div>
                <div class="flex">
                    <span class="iconfont icon-fuwu" style="font-size: 50px;color: white" />
                    <div class="m-l-8 xxl name">专属服务</div>
                </div>
                <div class="flex">
                    <span class="iconfont icon-peisong" style="font-size: 50px;color: white" />
                    <div class="m-l-8 xxl name">售后无忧</div>
                </div>
                <div class="flex">
                    <span class="iconfont icon-shouhou" style="font-size: 50px;color: white" />
                    <div class="m-l-8 xxl name">极速配送</div>
                </div>
            </div>
        </div>
        <footer class="footer-wrap flex row-center">
            <a class="white" target="_blank" :href="publicData.shop.record_system_link">{{publicData.shop.copyright}} {{publicData.shop.record_number}}</a>
        </footer>
    </div>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState(["publicData"]),
    },
};
</script>

<style lang="scss" scoped>
.footer-container {
    margin-top: 38px;
    .footer-box {
        height: 120px;
        background-color: #222;
        min-width: 1180px;
        .server-box {
            width: 1180px;
            padding: 36px 40px;
            border-bottom: 1px solid #707070;
            .name {
                color: #e5e5e5;
            }
        }
    }
    .footer-wrap {
        min-width: 1180px;
        background-color: #222;
        height: 60px;
        color: #ccc;
    }
}
</style>