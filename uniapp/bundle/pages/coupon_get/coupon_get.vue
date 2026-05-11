<template>
    <view class="coupon-get">
        <mescroll-contain
            height="100%"
            ref="mescroll"
            :up-option="{
                noMoreSize: 10,
                empty: {
                    icon: '/static/images/empty/coupon.png',
                    tip: '暂无优惠券~',
                    fixed: true
                }
            }"
            @upcallback="upCallback"
        >
            <view class="coupon" v-for="item in couponList" :key="item.id">
                <coupon-card
                    mode="get"
                    :discount_max_money="item.discount_max_money"
                    :discount_ratio="item.discount_ratio"
                    :condition_type="item.condition_type"
                    :name="item.name"
                    :money="item.money"
                    :condition="item.condition"
                    :time="item.use_time_text2"
                    :scene="item.use_scene"
                    :coupon-id="item.id"
                    :gotten="!!item.is_receive"
                    :button="{
                        name: !item.is_empty ? '领取' : '已抢光',
                        theme: 'primary',
                        disable: !!item.is_empty || !!item.is_available
                    }"
                    :desc="{
                        content: ''
                    }"
                    @button="getCoupon"
                />
            </view>
        </mescroll-contain>
    </view>
</template>

<script>
import { apiCouponList, apiCouponGet } from '@/api/store'
import { mapGetters, mapState } from 'vuex'

let mescroll = null

export default {
    name: 'CouponGet',

    data() {
        return {
            couponList: [] // 优惠券列表
        }
    },
    computed: {
        ...mapGetters(['userInfo'])
    },
    methods: {
        // 上拉加载更多
        upCallback({ num, size }) {
            apiCouponList({
                page_no: num,
                page_size: size
            })
                .then(({ lists, page_size, count }) => {
                    if (num === 1) this.couponList = []
                    this.couponList = [...this.couponList, ...lists]
                    // mescroll.endBySize(page_size, count)
                    mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    mescroll.endBySize()
                })
        },

        // 领取优惠券
        getCoupon(id) {
            apiCouponGet({ id })
                .then(() => {
                    this.$toast({ title: '领取成功' })
                })
                .catch((err) => {
                    console.log(err)
                    // this.$toast({ title: '领取失败' })
                })

            mescroll.downCallback()
        }
    },
    onShareAppMessage() {
        const { code } = this.userInfo
        const share = {
            path: `/bundle/pages/coupon_get/coupon_get?invite_code=${code}`
        }
        return share
    },
    mounted() {
        mescroll = this.$refs['mescroll']
    }
}
</script>

<style lang="scss" scoped>
.coupon-get {
    padding: 0 20rpx;
}

.coupon {
    &:nth-child(n + 2) {
        margin-top: 20rpx;
    }
}
</style>
