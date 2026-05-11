<template>
    <div class="get-coupons">
        <div class="banner flex-col m-b-16">
            <ad-item :item="adData.content" v-if="adData.content"></ad-item>
        </div>
        <div class="coupons bg-white">
            <div class="title bold">每日领券</div>
            <div class="list">
                <coupons-list
                    v-if="couponList.length"
                    :list="couponList"
                    :type="3"
                    @reflash="getCouponsList"
                />
                <null-data
                    v-else
                    :img="require('~/assets/images/coupon_null.png')"
                    text="暂无优惠券~"
                ></null-data>
            </div>
        </div>
    </div>
</template>

<script>
import headerMixins from '@/mixins/header'
export default {
    mixins: [headerMixins],
    async asyncData({ query, $get }) {
        const { data } = await $get('coupon/lists')

        // 获取顶部广告图
        const adData = await $get('Pc/getPage', {
            params: {
                type: 4,
            },
        })

        return {
            couponList: data.lists,
            adData: adData.data,
        }
    },

    data() {
        return {
            couponList: [],

            adData: {},
        }
    },

    methods: {
        async getCouponsList() {
            const { data, code } = await this.$get('coupon/lists')
            if (code == 1) {
                this.couponList = data.lists
            }
        },
    },
}
</script>

<style lang="scss">
.get-coupons {
    padding: 16px 0;

    .coupons {
        padding: 0 14px;

        .title {
            padding: 16px 18px;
            font-size: 20px;
        }
    }
}
</style>
