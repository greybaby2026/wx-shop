<template>
    <view>
        <u-popup v-model="showOpen" mode="center">
            <view class="container">
                <view
                    class="head"
                    :style="{
                        'background-image': `url(${$getImageUri(
                            '/resource/image/shopapi/default/register-1.png'
                        )})`
                    }"
                    v-if="pageData.style == 1"
                ></view>
                <view
                    class="head"
                    :style="{
                        'background-image': `url(${$getImageUri(
                            '/resource/image/shopapi/default/register-2.png'
                        )})`
                    }"
                    v-if="pageData.style == 2"
                ></view>

                <view
                    class="body"
                    :style="{
                        background:
                            pageData.style == 1
                                ? 'linear-gradient(#ff8750 0%, #ea492b 100%)'
                                : 'white'
                    }"
                >
                    <scroll-view style="height: 420rpx; border-radius: 10rpx" scroll-y="true">
                        <view
                            class="coup-card"
                            :style="{
                                background:
                                    pageData.style == 2
                                        ? 'linear-gradient(#ff8750 0%, #ea492b 100%)'
                                        : 'white'
                            }"
                            v-if="pageData.user_money_status == 1"
                        >
                            <view
                                class="left"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : 'ff4a29'
                                }"
                            >
                                <view>
                                    <u-image
                                        v-if="pageData.style == 1"
                                        width="70rpx"
                                        height="70rpx"
                                        :src="
                                            $getImageUri(
                                                'resource/image/shopapi/default/register-3-2.png'
                                            )
                                        "
                                    ></u-image>
                                    <u-image
                                        v-if="pageData.style == 2"
                                        width="70rpx"
                                        height="70rpx"
                                        :src="
                                            $getImageUri(
                                                'resource/image/shopapi/default/register-3-1.png'
                                            )
                                        "
                                    ></u-image>
                                </view>
                            </view>
                            <view
                                class="right"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : ''
                                }"
                            >
                                <view>
                                    <view class="xl bold">
                                        <text
                                            :style="{
                                                color: pageData.style == 1 ? 'red' : '#f2c252'
                                            }"
                                        >
                                            {{ pageData.user_money_num }}元
                                        </text>

                                        余额奖励</view
                                    >
                                </view>
                            </view>
                        </view>
                        <view
                            class="coup-card"
                            :style="{
                                background:
                                    pageData.style == 2
                                        ? 'linear-gradient(#ff8750 0%, #ea492b 100%)'
                                        : 'white'
                            }"
                            v-if="pageData.user_integral_status == 1"
                        >
                            <view
                                class="left"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : 'ff4a29'
                                }"
                            >
                                <view>
                                    <u-image
                                        v-if="pageData.style == 1"
                                        width="70rpx"
                                        height="70rpx"
                                        :src="
                                            $getImageUri(
                                                'resource/image/shopapi/default/register-4-2.png'
                                            )
                                        "
                                    ></u-image>
                                    <u-image
                                        v-if="pageData.style == 2"
                                        width="70rpx"
                                        height="70rpx"
                                        :src="
                                            $getImageUri(
                                                'resource/image/shopapi/default/register-4-1.png'
                                            )
                                        "
                                    ></u-image>
                                </view>
                            </view>
                            <view
                                class="right"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : ''
                                }"
                            >
                                <view>
                                    <view class="xl bold"
                                        >新人专享
                                        <text
                                            :style="{
                                                color: pageData.style == 1 ? 'red' : '#f2c252'
                                            }"
                                        >
                                            {{ pageData.user_integral_num }}
                                        </text>
                                        积分</view
                                    >
                                </view>
                            </view>
                        </view>
                        <view
                            v-for="item in pageData.coupon_array"
                            :key="item.id"
                            class="coup-card"
                            :style="{
                                background:
                                    pageData.style == 2
                                        ? 'linear-gradient(#ff8750 0%, #ea492b 100%)'
                                        : 'white'
                            }"
                        >
                            <view
                                class="left"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : 'ff4a29'
                                }"
                            >
                                <view>
                                    <template v-if="item.condition_type == 3">
                                        <text class="xxl bold"> {{ item.discount_ratio }}折 </text>
                                    </template>
                                    <template v-else>
                                        <text class="bold"> ¥ </text>
                                        <text class="xxl bold"> {{ item.money }} </text>
                                    </template>

                                    <view
                                        class="muted xs"
                                        :style="{ color: pageData.style == 2 ? '#f9bfaf' : '' }"
                                        >{{ item.condition }}</view
                                    >
                                </view>
                            </view>
                            <view
                                class="right"
                                :style="{
                                    color: pageData.style == 2 ? 'white' : ''
                                }"
                            >
                                <view>
                                    <text class="xl bold line">{{ item.name }} </text>
                                    <view
                                        class="muted xs"
                                        :style="{
                                            color: pageData.style == 2 ? '#f9bfaf' : '',
                                            'text-align': 'center'
                                        }"
                                        >{{ item.use_time_text2 }}</view
                                    >
                                </view>
                            </view>
                        </view>
                    </scroll-view>
                </view>
            </view>
            <view class="close">
                <u-icon name="close-circle" color="white" size="46" @click="updateisReward" />
            </view>
        </u-popup>
    </view>
</template>
<script>
import { apiregisterAward } from '@/api/store'
import { apiupdateRegisterAward } from '@/api/user'
import uImage from '../uview-ui/components/u-image/u-image.vue'
export default {
    components: { uImage },
    props: {
        value: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            showOpen: false,
            pageData: {},
            isReward: 1
        }
    },
    computed: {},
    methods: {
        goPage(link) {
            this.$Router.replaceAll({
                path: link.path,
                query: link.params
            })
            this.showOpen = false
        },
        async getData() {
            this.pageData = await apiregisterAward()
            if (this.pageData.status == 1) {
                this.showOpen = true
            }
        },
        async updateisReward() {
            await apiupdateRegisterAward()
            this.showOpen = false
        }
    },
    created() {
        this.getData()
    }
}
</script>
<style scoped lang="scss">
.container {
    width: 558rpx;
    margin-bottom: 20rpx;

    .head {
        background-size: 100% 100%;
        height: 450rpx;
    }

    .body {
        margin-top: -150rpx;
        height: 500rpx;
        padding: 20rpx;
    }

    .coup-card {
        margin-bottom: 10rpx;
        border-radius: 10rpx;
        height: 130rpx;
        padding: 10rpx;
        display: flex;
        .left {
            width: 30%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff4a29;
            text-align: center;
        }
        .right {
            width: 70%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
}
.close {
    width: 100%;
    text-align: center;
}
.card {
}
</style>
