<template>
    <view class="pages" :class="themeName">
        <u-notice-bar
            :show="showTips"
            mode="horizontal"
            :list="list"
            :font-size="26"
            :close-icon="true"
            :speed="100"
            @close="showTips = false"
        ></u-notice-bar>
        <view class="user-spread">
            <view class="header p-t-30" :style="[infoStyle]">
                <view class="user-info flex m-l-30">
                    <view class="user-avatar">
                        <u-image
                            width="110rpx"
                            height="110rpx"
                            border-radius="60"
                            :src="user.avatar"
                        />
                    </view>
                    <view class="user-message m-l-20 white">
                        <view class="m-b-10">
                            <view class="xxl bold m-r-20">{{ user.nickname }}</view>
                        </view>
                        <view
                            class="avatar-tag white xxs text-center m-b-10"
                            v-if="distributionInfo.level_name"
                        >
                            {{ distributionInfo.level_name }}
                        </view>
                        <view class="xs flex">
                            上级分销商：{{ leaderInfo.name ? leaderInfo.name : '无' }}
                            <view
                                v-if="leaderInfo.name == '-'"
                                class="br60 white write-btn flex row-center m-l-30 xxs"
                                @tap="showBind = true"
                                >填写
                            </view>
                        </view>
                    </view>
                </view>
            </view>

            <view class="main">
                <!-- 填表格 -->
                <view v-if="status == 0" class="user-apply">
                    <view class="user-apply-top flex-col col-center">
                        <view class="title xxl normal bold">申请成为分销会员</view>
                        <view class="apply-form">
                            <view class="form-item">
                                <u-field
                                    v-model="real_name"
                                    :label-width="150"
                                    label="真实姓名："
                                    :border-bottom="false"
                                    placeholder="请输入您的真实姓名"
                                >
                                </u-field>
                            </view>

                            <view class="form-item">
                                <u-field
                                    v-model="mobile"
                                    :label-width="150"
                                    label="手机号码："
                                    :border-bottom="false"
                                    placeholder="请输入您的手机号码"
                                >
                                </u-field>
                            </view>
                            <view class="form-item" @tap="showRegion = true">
                                <u-field
                                    v-model="region"
                                    disabled
                                    right-icon="arrow-right"
                                    :label-width="150"
                                    label="现住省份："
                                    :border-bottom="false"
                                    placeholder="请选择省、市、区"
                                >
                                </u-field>
                            </view>
                            <view class="form-item">
                                <u-field
                                    v-model="reason"
                                    :label-width="150"
                                    type="textarea"
                                    label="申请原因："
                                    placeholder="（必填）"
                                    :field-style="{ 'min-height': '160rpx' }"
                                />
                            </view>
                        </view>
                    </view>
                    <view
                        class="m-t-40 muted xs flex row-left"
                        v-if="distributionInfo.protocol_show == 1"
                    >
                        <view class="" style="width: 34rpx">
                            <u-checkbox v-model="checked" :active-color="themeColor" shape="circle">
                            </u-checkbox>
                        </view>
                        <view class="m-l-15">
                            本人已阅读并同意<span :style="{ color: themeColor }" @click="goTo()"
                                >《分销协议》</span
                            >
                        </view>
                    </view>
                    <button size="lg" class="apply-btn white br60 m-t-15" @tap="handleApply">
                        立即申请
                    </button>
                    <view class="m-t-20 xxs lighter flex row-center"
                        >提交成功后，我们将会在1-2个工作日内给您回复</view
                    >
                </view>

                <!-- 申请状态 -->
                <view v-if="status == 2" class="apply-result">
                    <view class="apply-result-top flex-col col-center">
                        <!-- <view class="title xxl normal bold">申请成为分销会员</view> -->
                        <image
                            class="apply-result-img"
                            :src="
                                applyObject.status == 2
                                    ? '/static/images/icon_fail.png'
                                    : '/static/images/icon_wait.png'
                            "
                        />
                        <view class="m-t-10 bold" style="line-height: 40rpx">{{
                            applyObject.status_tips
                        }}</view>
                        <view
                            v-if="applyObject.status == 2 && applyObject.audit_remark"
                            class="apply-fail-reason sm"
                        >
                            失败原因：{{ applyObject.audit_remark }}
                        </view>

                        <view class="apply-result-info">
                            <view class="info-item flex">
                                <view class="label">真实姓名：</view>
                                <view class="info-text ml20">{{ applyObject.real_name }}</view>
                            </view>
                            <view class="info-item flex">
                                <view class="label">手机号码：</view>
                                <view class="info-text ml20">{{ applyObject.mobile }}</view>
                            </view>
                            <view class="info-item flex">
                                <view class="label">现住省份：</view>
                                <view class="info-text ml20"
                                    >{{ applyObject.province }} {{ applyObject.city }}
                                    {{ applyObject.district }}
                                </view>
                            </view>
                            <view class="info-item flex">
                                <view class="label">申请原因：</view>
                                <view class="info-text ml20">{{
                                    applyObject.reason ? applyObject.reason : '-'
                                }}</view>
                            </view>
                        </view>
                    </view>
                    <template v-if="applyObject.status == 2">
                        <button size="lg" class="apply-btn white br60 m-t-30" @tap="status = 0">
                            重新申请
                        </button>
                        <view class="m-t-20 xxs lighter flex row-center"
                            >提交成功后，我们将会在1-2个工作日内给您回复</view
                        >
                    </template>
                </view>

                <!-- 已申请 -->
                <view v-if="status == 1" class="spread-info">
                    <view class="user-assets">
                        <view class="user-assets-header flex row-between">
                            <view class="flex bold">
                                可提现佣金：
                                <view class="money">
                                    <text class="sm">￥</text>
                                    <text class="xl">{{ distributionInfo.able_withdrawal }}</text>
                                </view>
                            </view>
                        </view>
                        <view class="user-assets-content flex flex-wrap">
                            <view class="user-item flex-col col-center">
                                <view class="user-assets-name flex"> 今日预估收益 </view>
                                <view class="assets m-l-20">
                                    <view class="money">
                                        <text class="sm">￥</text>
                                        <text class="xl">{{
                                            distributionInfo.today_earnings
                                        }}</text>
                                    </view>
                                </view>
                            </view>
                            <view class="user-item flex-col col-center">
                                <view class="nr user-assets-name flex"> 本月预估收益 </view>
                                <view class="assets m-l-20">
                                    <view class="money">
                                        <text class="sm">￥</text>
                                        <text class="xl">{{
                                            distributionInfo.month_earnings
                                        }}</text>
                                    </view>
                                </view>
                            </view>
                            <view class="user-item flex-col col-center">
                                <view class="nr user-assets-name flex"> 累计获得收益 </view>
                                <view class="assets">
                                    <view class="money">
                                        <text class="sm">￥</text>
                                        <text class="xl">{{
                                            distributionInfo.history_earnings
                                        }}</text>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                    <!-- <view class="m-t-20 user-fans flex bg-white md">
						<router-link class="flex-1" to="/bundle/pages/user_fans/user_fans">
							<view class="my-fans flex row-center">
								我的粉丝 <view class="m-l-10 fans-num">{{distributionInfo.fans}}</view>
								<u-icon class="m-l-10" name="arrow-right" size="28rpx" color="#666" />
							</view>
						</router-link>
					</view>
					<view class="user-invite m-t-20 bg-white flex-col col-center">
						<view class="sm">我的邀请码</view>
						<view class="flex bold m-t-20" style="font-size: 42rpx;">
							{{distributionInfo.code}}
							<view class="invite-copy-btn m-l-10 xxs" @click="$copy(distributionInfo.code)">点击复制</view>
						</view>
					</view> -->

                    <view class="bg-white m-t-20 flex flex-1 p-t-28 p-b-14 fans-invite">
                        <!-- 我的粉丝 -->
                        <router-link
                            class="flex-1 flex-col col-center row-center"
                            to="/bundle/pages/user_fans/user_fans"
                        >
                            <view class="my-fans flex row-center"> 我的粉丝 </view>
                            <view class="text-center m-t-20 m-b-44" style="font-size: 42rpx">{{
                                distributionInfo.fans
                            }}</view>
                        </router-link>
                        <!-- 我的邀请码 -->
                        <view class="flex-1 flex-col col-center row-center">
                            <view class="sm">我的邀请码</view>
                            <view class="flex bold m-t-20" style="font-size: 42rpx">
                                {{ distributionInfo.code }}
                            </view>
                            <view
                                class="invite-copy-btn xxs m-t-6"
                                @click="$copy(distributionInfo.code)"
                                >点击复制</view
                            >
                        </view>
                    </view>

                    <view class="usual-tools bg-white m-t-20">
                        <view class="usual-tools-header flex lg bold"> 常用工具 </view>
                        <view class="usual-content flex">
                            <router-link
                                class="usual-item"
                                to="/bundle/pages/user_spread_order/user_spread_order"
                            >
                                <view class="flex-col col-center">
                                    <image
                                        src="/bundle/static/images/icon_fenxiao.png"
                                        class="usual-item-img"
                                    ></image>
                                    <view class="nr normal m-t-20" style="line-height: 40rpx"
                                        >分销订单</view
                                    >
                                </view>
                            </router-link>
                            <router-link
                                class="usual-item"
                                to="/bundle/pages/commission_details/commission_details"
                            >
                                <view class="flex-col col-center">
                                    <image
                                        src="/bundle/static/images/icon_yongjin.png"
                                        class="usual-item-img"
                                    ></image>
                                    <view class="nr normal m-t-20" style="line-height: 40rpx"
                                        >佣金明细</view
                                    >
                                </view>
                            </router-link>
                            <router-link
                                class="usual-item"
                                to="/bundle/pages/monthly_bill/monthly_bill"
                            >
                                <view class="flex-col col-center">
                                    <image
                                        src="/bundle/static/images/icon_zhangdan.png"
                                        class="usual-item-img"
                                    ></image>
                                    <view class="nr normal m-t-20" style="line-height: 40rpx"
                                        >月度账单</view
                                    >
                                </view>
                            </router-link>
                        </view>
                    </view>
                </view>
            </view>
            <u-popup v-model="showBind" mode="center" closeable border-radius="30">
                <view class="inviteform-contain flex-col col-center">
                    <view class="title xl">填写邀请码</view>
                    <view class="input-row flex">
                        <u-field
                            v-model="inviteCode"
                            :label-width="140"
                            label="邀请码："
                            :border-bottom="false"
                            placeholder="请输入邀请码"
                        >
                        </u-field>
                    </view>
                    <view class="btn bg-primary white flex row-center" @tap="handleBind">确定</view>
                </view>
            </u-popup>
            <u-picker
                v-model="showRegion"
                :confirm-color="themeColor"
                cancel-color="#999"
                mode="region"
                @confirm="regionChange"
            >
            </u-picker>
        </view>
        <loading-view v-show="loading"></loading-view>
    </view>
</template>

<script>
import {
    apiDistributionIndex,
    apiDistributionApply,
    apiDistributionApplyDetail,
    apiDistributionCode
} from '@/api/user'
import { copy } from '@/utils/tools'
import { mapActions, mapGetters } from 'vuex'

export default {
    data() {
        return {
            loading: true,
            // 提示
            showTips: true,
            list: ['成为分销会员，推广下级可获得额外收益，推广越多收益越多'],
            // 地区
            region: '',
            showRegion: false,
            // 用户信息
            user: {},
            // 上级信息
            leaderInfo: {},
            // 分销主页数据
            distributionInfo: {},
            // 申请状态 0 ==> 未申请 2 ==> 申请进度 1 ==> 已申请
            status: 0,
            showBind: false,
            inviteCode: '',

            // 审核信息对象
            applyObject: {},
            real_name: '',
            province: '',
            city: '',
            district: '',
            reason: '',
            mobile: '',

            checked: false // 是否同意分销协议
        }
    },
    onLoad(options) {
        // 获取上级及个人信息
        this.getDistributionIndex()
        // #ifdef H5
        setTimeout(() => {
            const shareconfig = this.shareConfig.find((i) => {
                return i.type == 2 && i.page == 4
            })
            const { nickname } = this.userInfo
            const titile = shareconfig.title.replace(/\{nickname\}/g, nickname)
            const sharedesc = shareconfig.synopsis.replace(/\{nickname\}/g, nickname)
            // 设置分享
            this.$store.dispatch('setWxShare', {
                shareTitle: titile,
                shareImage: shareconfig.image,
                shareDesc: sharedesc
            })
        }, 1000)

        // #endif
    },
    onShareAppMessage() {
        const shareconfig = this.shareConfig.find((i) => {
            return i.type == 1 && i.page == 4
        })
        const { nickname } = this.userInfo

        const { code } = this.userInfo
        const titile = shareconfig.title.replace(/\{nickname\}/g, nickname)
        const share = {
            title: titile,
            path: `/pages/index/index?invite_code=${code}`,
            imageUrl: shareconfig.image
        }
        return share
    },
    onShareTimeline() {
        const shareconfig = this.shareConfig.find((i) => {
            return i.type == 1 && i.page == 4
        })
        const { nickname } = this.userInfo
        const titile = shareconfig.title.replace(/\{nickname\}/g, nickname)
        const share = {
            title: titile,
            imageUrl: shareconfig.image
        }
        return share
    },

    computed: {
        ...mapGetters(['userInfo', 'appConfig', 'shareConfig']),

        infoStyle() {
            const { top_apply_image } = this.distributionInfo
            if (!top_apply_image) {
                return {}
            }
            return {
                'background-image': `url(${this.distributionInfo.top_apply_image})`,
                'background-repeat': 'no-repeat',
                'background-size': 'cover'
            }
        }
    },

    methods: {
        // 分销会员信息
        async getDistributionIndex() {
            apiDistributionIndex().then((res) => {
                this.distributionInfo = res
                this.user = res.user
                this.leaderInfo = res.leader
                this.status = res.is_distributon
                if (this.status == 0) {
                    // 非分销
                    this.getApplyDetail()
                    return
                }
                this.loading = false
            })
        },
        // 申请分销
        handleApply() {
            if (this.distributionInfo.protocol_show == 1 && this.status == 0 && !this.checked) {
                return this.$toast({ title: '请先勾选同意分销协议' })
            }

            const { real_name, province, city, district, reason, mobile, region } = this
            if (!real_name) {
                this.$toast({
                    title: '请填写真实姓名'
                })
                return
            }

            if (!region.length) {
                this.$toast({
                    title: '请选择省市区'
                })
                return
            }
            apiDistributionApply({
                real_name,
                province,
                city,
                district,
                reason,
                mobile,
                region
            }).then(() => {
                this.getApplyDetail()
            })
        },
        // 最新分销会员申请详情
        getApplyDetail() {
            apiDistributionApplyDetail()
                .then((res) => {
                    switch (res.status) {
                        case 0:
                        case 2:
                            this.applyObject = res
                            this.status = 2
                    }
                })
                .finally(() => {
                    this.loading = false
                })
        },
        // 地区选择
        regionChange(region) {
            const { city, province, area } = region
            this.region = province.label + ' ' + city.label + ' ' + area.label
            this.province = province.value
            this.city = city.value
            this.district = area.value
        },

        // 填写邀请码
        handleBind() {
            apiDistributionCode({
                code: this.inviteCode
            }).then((res) => {
                this.getDistributionIndex()
                this.showBind = false
            })
        },

        // 去到分销协议页面
        goTo() {
            this.$Router.push({
                path: '/bundle/pages/server_explan/server_explan',
                query: {
                    type: 3
                }
            })
        }
    }
}
</script>
<style lang="scss">
.user-spread {
    .header {
        @include background_image(90deg, 0%, 100%, '../../static/images/spread_bg.png');
        background-repeat: no-repeat;
        background-size: 100% 100%;
        height: 300rpx;

        .user-info {
            .avatar-tag {
                bottom: -10rpx;
                display: inline-block;
                background-color: #f79c0c;
                border: 1rpx solid #ffffff;
                border-radius: 100rpx;
                line-height: 34rpx;
                padding: 0 10rpx;
            }

            .user-message {
                .write-btn {
                    height: 42rpx;
                    width: 100rpx;
                    background-color: #ff838d;
                }
            }
        }
    }

    .main {
        margin-top: -90rpx;
        z-index: 2;
        padding: 0 20rpx;

        .apply-btn {
            @include background_color();
        }

        .user-apply {
            .user-apply-top {
                background-color: white;
                padding: 40rpx 0 0rpx;
                border-radius: 20rpx;

                .apply-form {
                    width: 100%;
                    margin-top: 60rpx;

                    .form-item {
                        border: $-solid-border;
                        margin: 0 30rpx 30rpx;
                        border-radius: 6rpx;
                    }
                }
            }
        }

        .apply-result {
            .apply-result-top {
                background-color: white;
                padding: 36rpx 14rpx 50rpx;
                border-radius: 20rpx;

                .apply-result-img {
                    width: 100rpx;
                    height: 100rpx;
                    margin: 60rpx 0 24rpx;
                }

                .apply-fail-reason {
                    color: #ff2c3c;
                    margin-top: 10rpx;
                }
            }

            .apply-result-info {
                margin-top: 42rpx;
                width: 100%;

                .info-item {
                    margin-left: 60rpx;
                    margin-bottom: 28rpx;
                    line-height: 30rpx;

                    .label {
                        width: 160rpx;
                        color: $-color-lighter;
                    }
                }
            }
        }

        .spread-info {
            .user-assets {
                background-color: #fff;
                border-radius: 20rpx;
                padding: 10rpx 20rpx 22rpx;
                background: linear-gradient(90deg, #fbefdb 0%, #fed09e 100%);
                color: #8f430e;
                .money {
                    color: #ff2c3c;
                    font-weight: 500;
                }
                .user-assets-header {
                    border-bottom: 1rpx dashed #8f430e;
                    padding-bottom: 4rpx;
                    height: 80rpx;
                }

                .user-assets-content {
                    margin-top: 30rpx;

                    .user-item {
                        flex: 1;
                        position: relative;

                        .user-assets-name {
                            text-align: left;
                            align-self: flex-start;
                        }

                        .assets {
                            margin-top: 14rpx;
                            text-align: left;
                            align-self: flex-start;
                        }
                    }
                }
            }

            // .user-fans {
            // 	border-radius: 10rpx;

            // 	.my-fans {
            // 		height: 120rpx;

            // 		.fans-num {
            // 			color: #FF2C3C;
            // 		}
            // 	}
            // }

            // .user-invite {
            // 	padding: 26rpx 0 57rpx;
            // 	border-radius: 10rpx;

            // 	.invite-copy-btn {
            // 		line-height: 30rpx;
            // 		padding: 6rpx 18rpx;
            // 		background: linear-gradient(90deg, #FEE4B4 0%, #FBCB96 100%);
            // 		color: #8F430E;
            // 		border-radius: 4rpx;
            // 		margin-left: 12rpx;
            // 	}
            // }

            .fans-invite {
                border-radius: 20rpx;
                .invite-copy-btn {
                    line-height: 30rpx;
                    padding: 6rpx 18rpx;
                    background: linear-gradient(90deg, #fee4b4 0%, #fbcb96 100%);
                    color: #8f430e;
                    border-radius: 4rpx;
                    margin-left: 12rpx;
                }
            }

            .usual-tools {
                border-radius: 20rpx;
                padding: 0 25rpx;

                .usual-tools-header {
                    height: 100rpx;
                    border-bottom: $-solid-border;
                }

                .usual-content {
                    padding: 40rpx 0;

                    .usual-item {
                        width: 25%;

                        .usual-item-img {
                            width: 56rpx;
                            height: 56rpx;
                            flex: none;
                        }
                    }
                }
            }
        }
    }

    /* 弹窗 */
    .inviteform-contain {
        padding-left: 30rpx;
        padding-right: 30rpx;
        padding-bottom: 30rpx;
        width: 580rpx;
        border-radius: 6rpx;
        background-color: $-color-white;
        .title {
            padding: 26rpx 0rpx;
        }
        .input-row {
            border: $-solid-border;
            border-radius: 4rpx;
        }
        .btn {
            height: 80rpx;
            padding: 0 180rpx;
            border-radius: 10rpx;
            margin-top: 60rpx;
            @include background_color();
        }
    }
}
</style>
