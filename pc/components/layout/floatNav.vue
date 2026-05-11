<template>
    <div class="float-nav bg-white flex-col row-center">
        <div class="nav-list">
            <nuxt-link to="/shop_cart" class="item flex-col row-center lighter">
                <!-- <el-badge :value="publicData.my.cart" class="badge" type="primary">
                    <span class="iconfont icon-cart-Empty"></span>
                </el-badge>-->
                <div class="sm" style="padding: 0 0px">购物车</div>
            </nuxt-link>
            <el-popover placement="left" width="165" trigger="hover">
                <div
                    style="text-align: center; margin: 0"
                    class="flex-col row-center"
                >
                    <img
                        style="width: 180px; height: 180px"
                        class="m-l-20"
                        :src="server.qr_code"
                        alt
                    />
                </div>
                <div class="item flex-col row-center lighter" slot="reference">
                    <span class="iconfont icon-service"></span>
                    <div class="sm" style="padding: 0 10px">联系客服</div>
                </div>
            </el-popover>
            <!-- <nuxt-link class="item flex-col row-center lighter" to="/user/coupons">
                <el-badge :value="publicData.coupon_num" type="primary">
                    <span class="iconfont icon-coupons"></span>
                </el-badge>
                <div class="sm" style="padding: 0 10px">优惠券</div>
            </nuxt-link>-->
            <nuxt-link
                class="item flex-col row-center lighter"
                to="/user/order"
            >
                <!-- <el-badge :value="publicData.my.order" class="badge" type="primary">
                  <span class="iconfont icon-icon_order"></span>
                </el-badge>-->
                <div class="sm" style="padding: 0 10px">我的订单</div>
            </nuxt-link>
            <nuxt-link
                class="item flex-col row-center lighter"
                to="/user/collection"
            >
                <!-- <el-badge :value="publicData.my.collect" class="badge" type="primary">
                  <span class="iconfont icon-collection"></span>
                </el-badge>-->
                <div class="sm" style="padding: 0 10px">我的收藏</div>
            </nuxt-link>
            <!-- <nuxt-link class="item flex-col row-center lighter" to="/help_center">
                <span class="iconfont icon-survey"></span>
                <div class="sm" style="padding: 0 10px">帮助</div>
            </nuxt-link>-->
        </div>
        <div class="back-top">
            <span class="iconfont icon-top" @click="scrollTop"></span>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            server: {},
        };
    },
    created() {
        this.getService();
    },
    methods: {
        async getService() {
            const { data, code } = await this.$get("service/getConfig");
            if (code == 1) {
                this.server = data;
            }
        },
        scrollTop() {
            cancelAnimationFrame(this.timer);
            const self = this;
            this.timer = requestAnimationFrame(function fn() {
                var oTop =
                    document.body.scrollTop ||
                    document.documentElement.scrollTop;
                if (oTop > 0) {
                    scrollTo(0, oTop - 250);
                    self.timer = requestAnimationFrame(fn);
                } else {
                    cancelAnimationFrame(self.timer);
                }
            });
        },
    },
    computed: {
        ...mapState(["publicData"]),
    },
};
</script>

<style lang="scss" scoped>
.float-nav {
    min-height: 350px;
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 60px;
    z-index: 999;
    box-shadow: -3px 1px 2px rgba(0, 0, 0, 0.04);
    .nav-list {
        // position: absolute;
        // bottom: 0;
        .item {
            padding: 10px 4px;
            text-align: center;
            cursor: pointer;
            &:hover {
                // color: $--color-primary;
                background-color: $--background-color-base;
            }
            .iconfont {
                font-size: 28px;
            }
            ::v-deep .el-badge__content {
                height: 16px;
                line-height: 16px;
                padding: 0 5px;
            }
            .badge {
                width: 30px;
                margin: 0 auto;
            }
        }
    }
    .back-top {
        // position: absolute;
        // bottom: 20px;
        padding: 10px 4px;
        text-align: center;
        cursor: pointer;
    }
}
</style>
