<template>
    <view class="store-bind" :class="themeName">
        <!-- 已绑定提示：首绑终身，不可更换（选择模式不展示） -->
        <view class="bound-tip bg-white" v-if="!selectMode && boundStoreName">
            <view class="sm black">
                您已绑定门店：<text class="bound-name">{{ boundStoreName }}</text>
            </view>
            <view class="xs muted m-t-10">门店一经绑定不可更换，如需变更请联系客服</view>
        </view>

        <!-- 门店列表（归属门店由服务端置顶并标记「我的门店」） -->
        <view class="store">
            <mescroll-uni :fixed="false" ref="mescrollRef" :down="{ auto: false }" :up="{
                noMoreSize: 5,
                auto: false,
                empty: {
                    icon: '/static/images/empty/shop.png',
                    tip: '暂无门店~',
                    fixed: true
                }
            }" @init="mescrollInit" @down="downCallback" @up="upCallback">
                <view class="p-20">
                    <view class="store-item bg-white flex" v-for="(item, index) in lists" :key="index"
                        @click="onBind(item)">
                        <view class="flex-1">
                            <view class="m-b-20">
                                <text class="bold lg black">{{ item.name }}</text>
                                <text class="muted xs m-l-10">{{ item.distance }}</text>
                                <text v-if="item.is_my_store" class="my-store-tag m-l-10">我的门店</text>
                            </view>
                            <view class="black xs m-b-20 flex">
                                <u-icon class="md m-r-10" name="map" />
                                <text>{{ item.detailed_address }}</text>
                            </view>
                            <view class="black xs flex">
                                <u-icon class="md m-r-10" name="clock" />
                                <text>{{ item.business_start_time }} - {{ item.business_end_time }}</text>
                            </view>
                        </view>
                        <view class="bind-btn" @click.stop="onBind(item)">
                            {{ selectMode ? '选择' : '绑定' }}
                        </view>
                    </view>
                </view>
            </mescroll-uni>
        </view>
    </view>
</template>

<script>
import { apiSelffetchStore } from '@/api/store'
import { apiUserBindStore, apiUserCentre } from '@/api/user'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'
import store from '@/store'

export default {
    name: 'StoreBind',
    mixins: [MescrollMixin],

    data() {
        return {
            lists: [], // 门店列表
            location: {}, // 我的位置
            boundStoreName: '', // 已绑定门店名（非空则不可再绑定）
            binding: false, // 绑定请求中（防重复提交）
            // 选择模式（?mode=select）：仅用于注册前选店 —— 只回传选择结果，不调用绑定接口
            selectMode: false
        }
    },

    methods: {
        // 上拉加载更多
        upCallback(page) {
            apiSelffetchStore({
                ...this.location,
                page_no: page.num,
                page_size: page.size
            })
                .then(({ lists, count }) => {
                    if (page.num === 1) this.lists = []
                    this.lists = [...this.lists, ...lists]
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch(() => {
                    this.mescroll.endBySize()
                })
        },

        // 获取位置（用于按距离排序，失败不阻断列表展示）
        async getLocation() {
            const [error, data] = await uni.getLocation({
                // #ifdef MP
                type: 'gcj02'
                // #endif
            })
            if (!data) return null
            return {
                latitude: data.latitude,
                longitude: data.longitude
            }
        },

        // 读取当前绑定状态
        getBoundInfo() {
            apiUserCentre()
                .then((res) => {
                    this.boundStoreName = (res && res.is_bind_store) ? (res.bind_store_name || '') : ''
                })
                .catch(() => {})
        },

        // 选择并绑定门店
        onBind(item) {
            // 选择模式（注册前选店）：只回传结果，不绑定（此时还未注册，无 token）
            if (this.selectMode) {
                uni.$emit('storeSelected', item)
                return this.$Router.back()
            }
            // 已绑定其他门店 → 明确告知不可更换（服务端同样会拦截）
            if (this.boundStoreName && item.name !== this.boundStoreName) {
                return this.$toast({ title: `您已绑定${this.boundStoreName}，无法更换门店` })
            }
            if (this.binding) return

            uni.showModal({
                title: '确认绑定门店',
                content: `绑定「${item.name}」后将无法更换，是否确认绑定？`,
                confirmText: '确认绑定',
                success: (res) => {
                    if (res.confirm) this.doBind(item)
                }
            })
        },

        doBind(item) {
            this.binding = true
            apiUserBindStore({ store_id: item.id })
                .then(async (res) => {
                    const msg = (res && res.msg) ? res.msg : '绑定成功'
                    this.$toast({ title: msg })
                    // 刷新用户信息，使「我的」页归属门店与折扣状态立即生效
                    await this.$store.dispatch('getUser').catch(() => {})
                    uni.$emit('storeBound', res)
                    setTimeout(() => {
                        const pages = getCurrentPages()
                        if (pages.length > 1) {
                            this.$Router.back()
                        } else {
                            uni.reLaunch({ url: '/pages/index/index' })
                        }
                    }, 800)
                })
                .catch(() => {})
                .finally(() => {
                    this.binding = false
                })
        }
    },

    async onLoad(options) {
        // 选择模式：供注册页在「注册前」选店使用（未登录，故不查询绑定状态）
        this.selectMode = !!(options && options.mode === 'select')
        if (this.selectMode) {
            uni.setNavigationBarTitle({ title: '选择所属门店' })
        } else {
            // 本页 meta.auth = false（选择模式需在未登录时访问），故绑定模式在此自行校验登录态，
            // 未登录直接去登录页，避免展示一个点了会报「缺 token」的绑定按钮
            if (!store.getters.token) {
                return uni.redirectTo({ url: '/pages/login/login' })
            }
            this.getBoundInfo()
        }
        try {
            const data = await this.getLocation()
            if (data) this.location = data
        } catch (err) {
            console.log(err)
        }
        // 防止取不到 mescroll
        setTimeout(() => {
            this.mescroll && this.mescroll.resetUpScroll()
        }, 200)
    }
}
</script>

<style lang="scss">
page {
    padding: 0;
    margin: 0;
    height: 100%;
}

.store-bind {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.bound-tip {
    padding: 24rpx 30rpx;

    .bound-name {
        font-weight: 500;
        @include font_color();
    }
}

.store {
    flex: 1;
    min-height: 0;

    &-item {
        padding: 30rpx;
        border-radius: 14rpx;

        &:not(:last-of-type) {
            margin-bottom: 20rpx;
        }

        .bind-btn {
            align-self: center;
            flex: none;
            padding: 0 34rpx;
            height: 60rpx;
            line-height: 60rpx;
            border-radius: 60rpx;
            color: #ffffff;
            font-size: 26rpx;
            @include background_color();
        }
    }
}

/* 归属门店标记 */
.my-store-tag {
    display: inline-block;
    padding: 0 10rpx;
    font-size: 20rpx;
    line-height: 30rpx;
    color: #ffffff;
    border-radius: 6rpx;
    @include background_color();
}
</style>
