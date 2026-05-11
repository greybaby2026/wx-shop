<template>
    <view class="">
        <!-- Header -->
        <view class="header bg-white">
            <u-search
                placeholder="输入商品名称"
                @change="change(current)"
                :show-action="false"
                v-model="keyword"
            ></u-search>
        </view>

        <!-- Nav -->
        <view class="nav bg-white">
            <u-tabs
                name="name"
                :show-bar="true"
                :list="list"
                :current="current"
                @change="change"
                item-width="220"
                active-color="#3868f9"
            />
        </view>

        <!-- Section -->
        <view class="section" :style="{ height: height }">
            <swiper :duration="400" style="height: 100%" @change="change" :current="current">
                <swiper-item v-for="(items, index) in list" :key="index">
                    <view v-show="index == current">
                        <mescroll-uni
                            ref="mescrollRef"
                            bottom="200"
                            height="100%"
                            @init="mescrollInit"
                            @up="upCallback"
                            :up="upOption"
                            @down="downCallback"
                        >
                            <block v-for="(item2, index2) in items.lists" :key="index2">
                                <goods-card :data="item2">
                                    <template>
                                        <router-link
                                            :to="'/bundle/pages/spec_edit/spec_edit?id=' + item2.id"
                                        >
                                            <button class="btn hollow br60 flex row-center normal">
                                                编辑商品
                                            </button>
                                        </router-link>
                                    </template>

                                    <template v-if="item2.status == 1">
                                        <button
                                            class="btn primary br60 flex row-center normal"
                                            @click.stop="openFunc(item2, 'warehouse')"
                                        >
                                            放入仓库
                                        </button>
                                    </template>
                                    <template v-if="item2.status == 0">
                                        <button
                                            class="btn primary br60 flex row-center normal"
                                            @click.stop="openFunc(item2, 'on_shelf')"
                                        >
                                            上架销售
                                        </button>
                                    </template>
                                </goods-card>
                            </block>
                        </mescroll-uni>
                    </view>
                </swiper-item>
            </swiper>
        </view>

        <modal height="200rpx" v-model="on_shelf" @confirm="goodsSetting">
            <view class="black nr" style="height: 200rpx">
                确认上架：<text style="color: #ff4141">{{ curData.name }}</text>
            </view>
        </modal>

        <modal height="200rpx" v-model="warehouse" @confirm="goodsSetting">
            <view class="black nr" style="height: 200rpx">
                确认放入仓库：<text style="color: #ff4141">{{ curData.name }}</text>
            </view>
        </modal>

        <u-toast ref="uToast" />
    </view>
</template>

<script>
import MescrollMixin from '@/components/mescroll-uni/mescroll-mixins.js'
import { apiGoodsLists, apiGoodsOperation } from '@/api/goods'
import { debounce } from '@/utils/tools.js'
import { goodStatus } from '@/utils/enum.js'
export default {
    mixins: [MescrollMixin],
    data() {
        return {
            height: '100%',

            keyword: '',
            list: [
                {
                    oName: '销售中',
                    type: 1,
                    lists: [],
                    name: ''
                },
                {
                    oName: '库存预警',
                    type: 2,
                    lists: [],
                    name: ''
                },
                {
                    oName: '已售罄',
                    type: 3,
                    lists: [],
                    name: ''
                },
                {
                    oName: '仓库中',
                    type: 4,
                    lists: [],
                    name: ''
                }
            ],
            current: 0,
            count: 0,
            curData: {},
            on_shelf: false, // 上架

            warehouse: false, //放入仓库

            upOption: {
                empty: {
                    icon: '/static/images/empty/shop.png',
                    tip: '暂无商品！', // 提示
                    fixed: true,
                    top: '200rpx'
                }
            }
        }
    },

    async onShow() {
        // 使用防抖是为了防止v-show的时候出发多条数据，所以使用防抖触发多次的时候可以只成为触发一次，优化性能请求
        this.upCallback = debounce(this.upCallback, 200, this)
        // this.$nextTick(() => {
        //     this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
        // })

        uni.getSystemInfo({
            success: (res) => {
                this.height = res.windowHeight - 107 + 'px'
                console.log(this.height)
            }
        })
    },

    methods: {
        change(event) {
            let index
            event.detail ? (index = event.detail.current) : (index = event)
            this.current = Number(index)
            this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
        },

        async upCallback(page) {
            const index = this.current
            const pageNum = page.num
            const pageSize = page.size
            apiGoodsLists({
                keyword: this.keyword,
                type: this.list[index].type,
                page_no: pageNum,
                page_size: pageSize
            })
                .then(({ lists, page_size, page_no, extend, count }) => {
                    // 如果是第一页需手动置空列表
                    if (pageNum == 1 || this.keyword) this.list[index].lists = []
                    this.list.forEach((item, index) => {
                        item.name = `${item.oName}(${extend[goodStatus[item.type]]})`
                    })
                    // 重置列表数据
                    this.count = count
                    this.list[index].lists = [...this.list[index].lists, ...lists]
                    this.$refs.mescrollRef[index].mescroll.endBySize(page_size, count)
                })
                .catch((err) => {
                    console.log(err)
                    this.mescroll.endErr()
                })
        },

        openFunc(item, action) {
            this.curData = item

            this[action] = true
        },

        // 操作商品
        async goodsSetting() {
            let id = this.curData.id

            await apiGoodsOperation({
                id
            })

            this.$refs.mescrollRef[this.current].mescroll.resetUpScroll()
            this.$refs.uToast.show({
                title: '操作成功',
                type: 'success'
            })
        }
    }
}
</script>

<style lang="scss" scoped>
/*根元素需要有固定的高度*/
page {
    height: 100%;
    box-sizing: border-box;

    .header {
        padding: 16rpx 30rpx;
    }

    .nav {
        padding: 12rpx 0;
    }

    .section {
        // overflow: hidden;

        .title {
            padding: 20rpx;
        }

        .btn {
            margin-top: 20rpx;
            margin-left: 20rpx;
            width: 180rpx;
            height: 64rpx;
            // border-radius: 8rpx;
            font-size: $-font-size-sm;
        }

        .danger {
            color: #ff4141;
            border: 1px solid #ff4141;
        }

        .primary {
            color: #3868f9;
            border: 1px solid #3868f9;
        }

        .hollow {
            color: $-color-lighter;
            border: 1px solid #dbdbdb;
        }
    }
}
</style>
