<template>
    <view class="store-list" :class="themeName">
        <!-- Location -->
        <!-- #ifndef APP-PLUS -->
        <map
            v-if="location.latitude"
            style="width: 750rpx; height: 400rpx"
            show-location
            :latitude="location.latitude"
            :longitude="location.longitude"
            :markers="markers"
        />
        <!-- #endif -->
        <!-- Store List -->
        <view class="store">
            <mescroll-uni
                :fixed="false"
                ref="mescrollRef"
                :down="{ auto: false }"
                :up="{
                    noMoreSize: 5,
                    auto: false,
                    empty: {
                        icon: '/static/images/empty/shop.png',
                        tip: '暂无门店~',
                        fixed: true
                    }
                }"
                @init="mescrollInit"
                @down="downCallback"
                @up="upCallback"
            >
                <view class="p-20">
                    <view
                        class="store-item bg-white flex"
                        v-for="(item, index) in lists"
                        :key="index"
                        @click="onSelect(item)"
                    >
                        <!-- Info -->
                        <view class="flex-1">
                            <view class="m-b-20">
                                <text class="bold lg black">{{ item.name }}</text>
                                <text class="muted xs m-l-10">{{ item.distance }}</text>
                            </view>
                            <view class="black xs m-b-20 flex">
                                <u-icon class="md m-r-10" name="map" />
                                <text>{{ item.detailed_address }}</text>
                            </view>
                            <view class="black xs flex">
                                <u-icon class="md m-r-10" name="clock" />
                                <text
                                    >{{ item.business_start_time }}
                                    <text> - </text>
                                    {{ item.business_end_time }}</text
                                >
                            </view>
                        </view>
                        <!-- Phone -->
                        <view @click.stop="onCall(item.mobile)">
                            <u-icon class="store-item-phone" name="phone-fill" />
                        </view>
                    </view>
                </view>
            </mescroll-uni>
        </view>
    </view>
</template>

<script>
import { apiSelffetchStore } from '@/api/store'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins'

export default {
    name: 'StoreList',
    mixins: [MescrollMixin],

    data() {
        return {
            lists: [], // 商家列表
            location: {}, // 我的位置
            markers: [] // 店铺位置
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
                .then(({ lists, page_size, count }) => {
                    if (page.num === 1) this.lists = []
                    this.lists = [...this.lists, ...lists]

                    this.markers = lists.map((item) => ({
                        id: item.id,
                        latitude: item.latitude,
                        longitude: item.longitude,
                        iconPath: '../../static/images/icon_marker.png',
                        width: 34,
                        height: 34,
                        callout: {
                            content: item.name,
                            padding: 5,
                            borderRadius: 4,
                            fontSize: 13,
                            display: 'ALWAYS'
                        }
                    }))
                    this.mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    this.mescroll.endBySize()
                })
        },

        // 拨打电话
        onCall(phone) {
            uni.makePhoneCall({
                phoneNumber: phone
            })
        },

        // 获取位置
        async getLocation() {
            const [error, data] = await uni.getLocation({
                // #ifdef MP

                type: 'gcj02'
                // #endif
            })
            // #ifdef MP
            if (!data) return this.getAuthorize()
            // #endif
            // #ifdef APP-PLUS
            if (!data)
                return this.$toast({
                    title: '获取位置失败'
                })
            // #endif
            return {
                latitude: data.latitude,
                longitude: data.longitude
            }
        },

        // 位置授权
        async getAuthorize() {
            const [error, data] = await uni.showModal({
                title: '您已拒绝地理位置权限',
                content: '是否进入权限管理，调整授权？'
            })

            if (data.confirm) {
                const [error, data] = await uni.openSetting()
                if (data) this.getLocation()
            } else if (data.cancel) {
                this.$Router.back()
            }
        },

        // 选择门店
        onSelect(data) {
            uni.$emit('changeStore', data)
            this.$Router.back()
        }
    },

    async onLoad() {
        try {
            const data = await this.getLocation()
            if (!data) throw new Error('位置获取失败')
            this.location = data
            // 防止获取不到mescroll
            setTimeout(() => {
                this.mescroll.resetUpScroll()
            }, 200)
        } catch (err) {
            console.log(err)
            this.$toast({
                title: err.message
            })
        }
    }
}
</script>

<style lang="scss">
page {
    padding: 0;
    margin: 0;
    height: 100%;
}

.store-list {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
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
        &-phone {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60rpx;
            height: 60rpx;
            margin-right: 10rpx;
            border: 1px solid $-color-black;
            border-radius: 50%;
        }
    }
}
</style>
