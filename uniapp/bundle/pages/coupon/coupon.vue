<template>
    <view :class="themeName">
        <u-tabs
            font-size="28"
            name="tabsName"
            inactive-color="#333"
            :active-color="themeColor"
            :list="lists"
            :is-scroll="false"
            :current="current"
            @change="change"
        >
        </u-tabs>

        <block v-for="(items, index) in lists" :key="index">
            <mescroll-uni
                v-if="items.isShow === true"
                ref="mescrollRef"
                bgColor="white"
                top="100rpx"
                @init="mescrollInit"
                :up="upOption"
                @down="downCallback"
                @up="upCallback"
            >
                <block v-for="(item, cIndex) in list" :key="cIndex">
                    <view class="m-b-24 p-l-24 p-r-24">
                        <coupon-card
                            mode="get"
                            :discount_max_money="item.discount_max_money"
                            :discount_ratio="item.discount_ratio"
                            :condition_type="item.condition_type"
                            :desc="{ title: '使用说明', content: [item.tips] }"
                            :name="item.name"
                            :money="item.money"
                            :condition="item.condition"
                            :time="item.use_time_text2"
                            :scene="item.use_scene"
                            :coupon-id="item.id"
                            :gotten="item.is_get"
                            :button="{
                                name: getBtn,
                                theme: getBtn == '去使用' ? 'primary' : 'disable'
                            }"
                            @button="getCoupon"
                        />
                    </view>
                </block>
            </mescroll-uni>
        </block>
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiCouponGet } from '@/api/activity'
export default {
    mixins: [MescrollMixin],
    data() {
        return {
            lists: [
                {
                    tabsName: '可使用(0)',
                    isShow: true,
                    type: 0
                },
                {
                    tabsName: '已使用(0)',
                    isShow: false,
                    type: 1
                },
                {
                    tabsName: '已过期(0)',
                    isShow: false,
                    type: 2
                }
            ],
            current: 0,

            list: [], //优惠券的数据

            extend: {},

            showTips: [],
            upOption: {
                auto: false, // 不自动加载
                noMoreSize: 4, //如果列表已无数据,可设置列表的总数量要大于半页才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看; 默认5
                empty: {
                    icon: '/static/images/empty/coupon.png',
                    tip: '暂无优惠券数据', // 提示
                    fixed: true,
                    top: '200rpx'
                }
            }
        }
    },

    computed: {
        getBtn() {
            let text = ''
            switch (this.lists[this.current].type) {
                case 0:
                    text = '去使用'
                    break
                case 1:
                    text = '已使用'
                    break
                case 2:
                    text = '已过期'
                    break
            }
            return text
        }
    },

    methods: {
        change(index) {
            for (let i = 0; i < this.lists.length; i++) {
                this.lists[i].isShow = false
            }
            this.current = index
            this.lists[index].isShow = true
        },

        upCallback({ num, size }) {
            apiCouponGet({
                status: this.lists[this.current].type,
                page_no: num,
                page_size: size
            })
                .then((res) => {
                    this.lists[0].tabsName = `可使用(${res.extend.normal})`
                    this.lists[1].tabsName = `已使用(${res.extend.use})`
                    this.lists[2].tabsName = `已过期(${res.extend.invalid})`
                    if (num == 1) this.list = [] //如果是第一页需手动置空列表
                    this.list = this.list.concat(res.lists) //追加新数据
                    this.mescroll.endSuccess(res.lists.length, res.count)
                })
                .catch((err) => {
                    this.mescroll.endErr()
                })
        },

        getCoupon() {
            if (this.current == 0) {
                uni.reLaunch({
                    url: '/pages/index/index'
                })
            }
        }
    }
}
</script>

<style lang="scss">
.coupon-list {
    padding: 0 24rpx;
    overflow: hidden;
}
</style>
