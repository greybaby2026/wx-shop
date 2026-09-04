<template>
    <view class="index" :style="[pageStyle, themeCssVars]" :class="themeName">
        <!-- #ifndef  H5 -->
        <u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
            <u-navbar
                :is-back="false"
                :title="styles.title"
                :title-bold="true"
                :is-fixed="false"
                :border-bottom="false"
                :title-color="percent > 0.5 ? '#000' : styles.title_color == 1 ? '#fff' : '#000'"
                :background="{ background: 'rgba(256,256, 256,' + percent + ')' }"
            ></u-navbar>
        </u-sticky>
        <!-- :title-color="styles.title_color == 1 ? '#fff' : '#000'" -->
        <!-- #ifdef MP -->
        <view class="capsule-tips flex" v-if="offTip">
            <text class="m-r-10">添加到“我的小程序”</text>
            <u-icon name="close" color="#FFFFFF" @click="clearIntervalFunc"></u-icon>
        </view>
        <!-- #endif -->
        <!-- #endif -->
        <bubble top="300rpx" :page="1" :discharge="isDischarge"></bubble>
        <lazy-component v-for="(item, index) in pagesData" :key="index">
            <view>
                <template v-if="item.name == 'search' && item.show">
                    <w-search :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'navigation' && item.show">
                    <w-navigation :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'banner' && item.show">
                    <w-banner :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'title' && item.show">
                    <w-title :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'rubik' && item.show">
                    <w-rubik :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'notice' && item.show">
                    <w-notice :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'blank' && item.show">
                    <w-blank :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'separate' && item.show">
                    <w-separate :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'goodsgroup' && item.show">
                    <w-goodsgroup :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'tabs' && item.show">
                    <w-tabs :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'coupon' && item.show">
                    <w-coupon
                        :content="item.content"
                        :styles="item.styles"
                        @refresh="getPageInfo"
                    />
                </template>
                <template v-else-if="item.name == 'seckill' && item.show">
                    <w-seckill :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'presell' && item.show">
                    <w-presell :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'spellgroup' && item.show">
                    <w-spellgroup :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'video' && item.show">
                    <w-video :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'graphic' && item.show">
                    <w-graphic :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'hotarea' && item.show">
                    <w-hotarea :content="item.content" :styles="item.styles" />
                </template>
                <template v-else-if="item.name == 'edit' && item.show">
                    <w-editor :content="item.content" :styles="item.styles" />
                </template>
                <!-- #ifdef MP-WEIXIN -->
                <template v-else-if="item.name == 'mnplive' && item.show">
                    <w-mnplive :content="item.content" :styles="item.styles" />
                </template>
                <!-- #endif -->
            </view>
            <view slot="skeleton" style="height: 20vh"></view>
        </lazy-component>
        <!-- #ifdef H5 -->
        <view class="record_number">
            <a :href="recordLink" style="color: #495770; text-decoration: none">
                {{ recordNumber }}
            </a>
        </view>
        <!-- #endif -->
        <!-- #ifndef APP-PLUS -->
        <tabbar />
        <!-- #endif -->
        <!-- #ifdef APP-PLUS -->
        <tabbar v-show="showTabbar" />
        <!-- #endif -->
        <index-skeleton v-show="loading" />
        <u-back-top
            :scroll-top="scrollTop"
            :top="1000"
            :customStyle="{
                backgroundColor: '#FFF',
                color: '#000',
                boxShadow: '0px 3px 6px rgba(0, 0, 0, 0.1)'
            }"
        >
        </u-back-top>

        <!-- #ifdef APP-PLUS -->
        <lyg-popup
            v-if="appConfig.app_pop_agreement"
            title="用户使用及隐私保护政策提示"
            protocolPath="/bundle/pages/server_explan/server_explan?type=1"
            policyPath="/bundle/pages/server_explan/server_explan?type=2"
            policyStorageKey="has_read_privacy"
            @showopen="handleoPenAd"
        >
        </lyg-popup>
        <!-- #endif -->
        <!-- #ifdef MP -->

        <!-- 用户隐私协议弹框 -->
        <privacy-popup v-model="showPrivacyPopup" @showopen="handleoPenAd"></privacy-popup>
        <!-- #endif -->

        <!-- 无网络提示 -->
        <u-no-network z-index="1200" @retry="handleRetry"></u-no-network>
        <!-- 弹出广告 -->
        <open-advertisement v-if="showopenAd"></open-advertisement>
        <!-- 注册奖励 -->
        <open-registerreward v-if="isReward === 0"></open-registerreward>
        <!-- 系统消息通知弹窗 -->
        <notification-popup />
    </view>
</template>
<script>
import { apiIndex } from '@/api/store'
import { apiVisit } from '@/api/app'
import { mapActions, mapGetters } from 'vuex'
import { apiUserCentre } from '@/api/user'
import store from '@/store'

import openAdvertisement from '../../components/open-advertisement/open-advertisement.vue'
import notificationPopup from '@/components/notification-popup/notification-popup.vue'
export default {
    components: { openAdvertisement, notificationPopup },
    data() {
        return {
            isDischarge: false,
            styles: {},
            pagesData: [],
            loading: true,
            scrollTop: 0,
            policyStorageKey: 'had_read_privacy', // 保存是否同意协议缓存信息的key
            showTabbar: true, // 是否展示tabbar。 在app端&开启了强制弹窗同意协议，需同意才展示tabbar
            percent: 0,
            offTip: false,
            showPrivacyPopup: false, //微信用户隐私协议
            showopenAd: false,
            isReward: 1
        }
    },

    methods: {
        ...mapActions(['getConfig', 'getDecorateConfig', 'getUser']),
        handleRetry() {
            this.getConfig()
            this.getDecorateConfig()
            this.getUser()
        },
        getPageInfo() {
            apiIndex()
                .then((res) => {
                    const {
                        common: { title },
                        common,
                        content
                    } = res

                    uni.setNavigationBarTitle({
                        title
                    })
                    this.styles = common
                    this.tempData = content.map((item, index) => {
                        return {
                            index,
                            value: item
                        }
                    })
                    this.splitSetData()
                })
                .finally(() => {
                    uni.stopPullDownRefresh()
                    this.loading = false
                })
        },
        splitSetData() {
            const item = this.tempData.shift()
            if (!item) return
            this.$set(this.pagesData, item.index, Object.freeze(item.value))
            setTimeout(() => {
                this.splitSetData()
            }, 30)
        },
        onPageScroll(e) {
            const top = uni.upx2px(100)
            const { scrollTop } = e
            this.percent = scrollTop / top > 1 ? 1 : scrollTop / top
        },
        clearIntervalFunc() {
            this.offTip = false
        },
        clearTipAuto() {
            let timeid = null
            if (timeid) {
                clearTimeout(timeid)
            }
            timeid = setTimeout(() => {
                this.clearIntervalFunc()
            }, 5000)
        },
        handleoPenAd() {
            this.showopenAd = true
        }
    },
    computed: {
        ...mapGetters(['userInfo', 'appConfig', 'shareConfig']),
        recordNumber() {
            return store.getters.appConfig.record_number
        },
        recordLink() {
            return store.getters.appConfig.record_system_link
        },

        pageStyle() {
            const { background_color, background_image, background_type } = this.styles
            if (background_type == 0) return
            const style =
                background_type == 1
                    ? {
                          'background-color': background_color
                      }
                    : {
                          'background-image': `url(${background_image})`
                      }
            return style
        }

        // // #ifdef APP-PLUS
        // // 是否展示tabbar。 在app端&开启了强制弹窗同意协议，需同意才展示tabbar
        // showTabbar() {
        // 	let isShow = false
        // 	return uni.getStorageSync(this.policyStorageKey);
        // },
        // // #endif
    },
    onPullDownRefresh() {
        this.getPageInfo()
    },
    onPageScroll(e) {
        const { scrollTop } = e
        this.scrollTop = scrollTop
    },
    onLoad() {
        uni.hideTabBar()
        // #ifdef MP-WEIXIN
        wx.getPrivacySetting({
            success: (res) => {
                console.log(res, '------') // 返回结果为: res = { needAuthorization: true/false, privacyContractName: '《xxx隐私保护指引》' }
                if (res.needAuthorization) {
                    // 需要弹出隐私协议
                    this.showPrivacyPopup = true
                } else {
                    this.showopenAd = true
                }
            },
            fail: (err) => {
                console.log(err)
            }
        })
        // #endif
        // #ifdef H5
        this.showopenAd = true
        // #endif
        // #ifdef APP-PLUS
        if (uni.getSystemInfoSync().platform == 'android') {
            this.showopenAd = true
        } else {
            if (!this.appConfig.app_pop_agreement) {
                this.showopenAd = true
            }
        }

        // #endif

        this.getPageInfo()
        // 访客记录
        apiVisit()
        // #ifdef H5
        setTimeout(() => {
            const shareconfig = this.shareConfig.find((i) => {
                return i.type == 2 && i.page == 1
            })
            const { shop_name } = this.appConfig
            const titile = shareconfig.title.replace(/\{name\}/g, shop_name)
            const sharedesc = shareconfig.synopsis.replace(/\{name\}/g, shop_name)
            // 设置分享
            this.$store.dispatch('setWxShare', {
                shareTitle: titile,
                shareImage: shareconfig.image,
                shareDesc: sharedesc
            })
        }, 1000)

        // #endif
    },
    onHide() {
        this.isDischarge = true
    },
    onShow() {
        this.isDischarge = false
        // #ifdef MP
        wx.getUpdateManager().onUpdateReady(function () {
            wx.showModal({
                title: '更新提示',
                content: '新版本已经准备好，是否重启应用？',
                success(res) {
                    if (res.confirm) {
                        // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
                        wx.getUpdateManager().applyUpdate()
                    }
                }
            })
        })
        // #endif
        // 只在首次加载时请求用户中心（避免每次返回首页都请求）
        if (!this.isReward) {
            apiUserCentre().then(({ is_register_award }) => {
                this.isReward = is_register_award
            })
        }
    },
    watch: {},
    onShareAppMessage() {
        const shareconfig = this.shareConfig.find((i) => {
            return i.type == 1 && i.page == 1
        })
        const { shop_name } = this.appConfig
        const { code } = this.userInfo
        const titile = shareconfig.title.replace(/\{name\}/g, shop_name)
        const share = {
            title: titile,
            path: `/pages/index/index?invite_code=${code}`,
            imageUrl: shareconfig.image
        }
        return share
    },
    onShareTimeline() {
        const shareconfig = this.shareConfig.find((i) => {
            return i.type == 1 && i.page == 1
        })
        const { shop_name } = this.appConfig
        const titile = shareconfig.title.replace(/\{name\}/g, shop_name)
        const share = {
            title: titile,
            imageUrl: shareconfig.image
        }
        return share
    },
    onReady() {
        // #ifdef MP

        const _this = this
        wx.checkIsAddedToMyMiniProgram({
            success(res) {
                if (!res.added) {
                    _this.offTip = true
                    _this.clearTipAuto()
                } else {
                    return
                }
            }
        })
        // #endif
    }
}
</script>

<style lang="scss">
.index {
    position: relative;
    background-repeat: no-repeat;
    background-size: 100% auto;
    overflow: hidden;
    min-height: calc(100vh - env(safe-area-inset-bottom));
}

// 胶囊提示
.capsule-tips {
    color: #ffffff;
    padding: 12rpx 18rpx;
    border-radius: 14rpx;
    background: rgba(0, 0, 0, 0.7);
    position: absolute;
    z-index: 9999;
    right: 25rpx;
}
.capsule-tips::after {
    content: '';
    border-bottom: 14rpx solid rgba(0, 0, 0, 0.7);
    border-right: 14rpx solid transparent;
    border-left: 14rpx solid transparent;
    position: absolute;
    top: -14rpx;
    right: 88rpx;
}
.record_number {
    text-align: center;
    padding: 30rpx;
    font-size: 24rpx;
}
</style>
