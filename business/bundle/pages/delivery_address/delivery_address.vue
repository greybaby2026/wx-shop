<template>
    <view style="position: relative; height: 100vh">
        <mescroll-uni
            ref="mescrollRef"
            @up="upCallback"
            @down="downCallback"
            @init="mescrollInit"
            bottom="120rpx"
        >
            <u-radio-group v-model="id" @change="radioGroupChange">
                <view
                    v-for="item in lists"
                    :key="item.id"
                    class="card flex"
                    @click.stop="handleclick(item.id)"
                >
                    <view>
                        <u-radio :name="item.id"> </u-radio>
                    </view>
                    <view class="m-l-20">
                        <view>{{ item.contact }}</view>
                        <view>{{ item.province }} {{ item.city }} {{ item.address }}</view>
                        <view>{{ item.address }} </view>
                    </view>
                </view>
            </u-radio-group>
        </mescroll-uni>
        <view class="btn-card">
            <button class="btn" @click="handleAdd">编辑/新增地址</button>
        </view>
    </view>
</template>
<script>
import { apideliveryaddressLists } from '@/api/order'
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
        handleAdd() {
            this.$Router.push({
                path: '/bundle/pages/delivery_address_edit/delivery_address_edit'
            })
        },
        handleclick(id) {
            const val = this.lists.find((i) => {
                return id == i.id
            })
            uni.$emit('address', val, this.type)
            this.$Router.back()
        },
        radioGroupChange() {
            const val = this.lists.find((i) => {
                return this.id == i.id
            })

            uni.$emit('address', val, this.type)
            this.$Router.back()
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
    width: 100%;
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
