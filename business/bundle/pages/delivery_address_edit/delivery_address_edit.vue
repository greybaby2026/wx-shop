<template>
    <view style="position: relative; height: 100vh">
        <mescroll-uni
            ref="mescrollRef"
            @up="upCallback"
            @down="downCallback"
            @init="mescrollInit"
            bottom="120rpx"
        >
            <view v-for="item in lists" :key="item.id" class="card">
                <view class="flex row-between"
                    >{{ item.contact }}
                    <u-icon color="#707070" name="edit-pen" @click="toAdd(item.id)"></u-icon>
                </view>
                <view class="m-t-10 m-b-10"
                    >{{ item.province }} {{ item.city }} {{ item.address }}</view
                >
                <view>{{ item.address }} </view>
                <view class="flex row-between m-t-20">
                    <view class="muted">
                        <u-radio-group
                            v-model="item.is_deliver_default"
                            @change="deliveryChangeradio($event, item)"
                        >
                            <u-radio :name="1"> </u-radio>
                        </u-radio-group>

                        <span @click.stop="deliveryChange(item)"> 默认发货地址 </span>
                    </view>
                    <view class="muted">
                        <u-radio-group
                            v-model="item.is_return_default"
                            @change="returnChangeradio($event, item)"
                        >
                            <u-radio :name="1"> </u-radio>
                        </u-radio-group>
                        <span @click.stop="returnChange(item)"> 默认退货地址 </span>
                    </view>
                    <view class="muted" @click="handleDel(item.id)"> 删除 </view>
                </view>
            </view>
        </mescroll-uni>
        <view class="btn-card">
            <button class="btn" @click="toAdd('')">添加新地址</button>
        </view>
    </view>
</template>
<script>
import { apideliveryaddressLists, apideliveryaddressedit, apideliveryaddressdel } from '@/api/order'
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'

export default {
    mixins: [MescrollMixin],

    data() {
        return {
            id: '',
            lists: [],
            type: ''
        }
    },
    methods: {
        toAdd(id) {
            this.$Router.push({
                path: '/bundle/pages/delivery_address_add/delivery_address_add',
                query: {
                    id: id
                }
            })
            uni.$once('addressedit', () => [this.$refs.mescrollRef.mescroll.resetUpScroll()])
        },
        handleDel(id) {
            apideliveryaddressdel({
                id
            }).then(() => {
                this.$refs.mescrollRef.mescroll.resetUpScroll()
            })
        },
        deliveryChange(item) {
            apideliveryaddressedit({
                id: item.id,
                default_type: 1,
                is_default: item.is_deliver_default ? '0' : '1'
            }).then(() => {
                this.$refs.mescrollRef.mescroll.resetUpScroll()
            })
        },
        deliveryChangeradio(e, item) {
            console.log(e)

            apideliveryaddressedit({
                id: item.id,
                default_type: 1,
                is_default: item.is_deliver_default
            }).then(() => {
                this.$refs.mescrollRef.mescroll.resetUpScroll()
            })
        },
        returnChangeradio(e, item) {
            console.log(e)

            apideliveryaddressedit({
                id: item.id,
                default_type: 2,
                is_default: item.is_return_default
            }).then(() => {
                this.$refs.mescrollRef.mescroll.resetUpScroll()
            })
        },
        returnChange(item) {
            apideliveryaddressedit({
                id: item.id,
                default_type: 2,
                is_default: item.is_return_default ? '0' : '1'
            }).then(() => {
                this.$refs.mescrollRef.mescroll.resetUpScroll()
            })
        },
        handleclick(id) {
            const val = this.lists.find((i) => {
                return id == i.id
            })
        },

        upCallback(page) {
            const pageNum = page.num
            const pageSize = page.size
            apideliveryaddressLists({
                page_no: pageNum,
                page_size: pageSize
            })
                .then(({ lists, count }) => {
                    if (pageNum == 1) this.lists = []
                    this.lists = [...this.lists, ...lists]
                    this.$refs.mescrollRef.mescroll.endSuccess(lists.length, count)
                })
                .catch((err) => {
                    this.$refs.mescrollRef.mescroll.endErr()
                })
        }
    },
    onLoad() {
        const id = this.$Route.query.id
        const type = this.$Route.query.type
        this.id = id
        this.type = type
    },
    onShow() {}
}
</script>
<style lang="scss" scoped>
page {
    background-color: white;
}
.card {
    background-color: #f7f7f7;
    padding: 30rpx;
    border-radius: 10rpx;
    margin: 20rpx;
}
.btn-card {
    position: absolute;
    bottom: 0rpx;
    padding-bottom: 30rpx;
    z-index: 999;
    background-color: white;
    left: 10rpx;
    right: 10rpx;
}
.btn {
    background-color: $-color-primary;
    height: 90rpx;
    line-height: 90rpx;
    color: white;
}
</style>
