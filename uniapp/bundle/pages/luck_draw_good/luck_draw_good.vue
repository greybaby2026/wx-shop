<template>
    <view :class="themeName" class="p-20">
        <view class="container flex">
            <u-image
                width="150rpx"
                height="150rpx"
                mode="aspectFill"
                :src="goodsInfo.image"
            ></u-image>
            <view class="m-l-20 flex-col row-around" style="height: 150rpx">
                <view class="line-2">
                    {{ goodsInfo.name }}
                </view>
                <view> {{ goodsInfo.sell_price }} </view>
            </view>
        </view>
        <view class="m-t-30 m-b-30"> 请选择规格 </view>
        <view class="nr container flex" @click="showGoodsSpec = true">
            <text class="m-l-20 skuline">已选：{{ specValueStr }}</text>
            <u-icon name="arrow-right" class="muted" style="margin-left: auto" />
        </view>
        <view class="btn" @click="showGoodsSpec = true"> 立即领取 </view>
        <goods-spec
            v-model="showGoodsSpec"
            :isNoMarking="true"
            :defaultInfo="{
                image: goodsInfo.image,
                price: goodsInfo.sell_price,
                stock: goodsInfo.total_stock,
                unit: goodsInfo.unit_name,
                limit_type: 0,
                limit_value: 1,
                buy_num: goodsInfo.buy_num,
                cart_goods_num: goodsInfo.cart_goods_num
            }"
            @closer="showGoodsSpec = false"
            :stock_show="goodsInfo.stock_show"
            :buttons="[
                {
                    event: 'buy',
                    text: '立即领取'
                }
            ]"
            :spec-list="goodsInfo.spec_value"
            :spec-map="goodsInfo.spec_value_list"
            @buy="handleBuy"
            @change="changeGoodsSpec"
        />
        <!-- 加载动画 -->
        <page-status :status="pageStatus">
            <template #error>
                <u-empty text="加载异常"></u-empty>
            </template>
        </page-status>
    </view>
</template>
<script>
import { apiGoodsDetail } from '@/api/goods'
import { PageStatusEnum, OrderTypeEnum } from '@/utils/enum'

export default {
    data() {
        return {
            showGoodsSpec: false,
            goodsInfo: {},
            goodsSpecOptions: {},
            luckId: '',
            pageStatus: PageStatusEnum['LOADING']
        }
    },
    methods: {
        // 更改规格
        changeGoodsSpec(options) {
            this.goodsSpecOptions = options
            console.log(options)
        },
        // 处理 立即购买
        handleBuy({ spec, number }) {
            this.$Router.push({
                path: '/pages/goods_order/goods_order',
                query: {
                    from: OrderTypeEnum['LUCK'],
                    // address_id: this.addressId,
                    payload: {
                        source: 'buy_now',
                        draw_record_id: this.luckId,
                        goods: [
                            {
                                goods_num: number,
                                item_id: spec.id
                            }
                        ]
                    }
                }
            })
        }
    },
    computed: {
        // 已选商品规格
        specValueStr() {
            return this.goodsSpecOptions?.spec?.spec_value_str ?? ''
        }
    },
    onLoad({ id, luckId }) {
        this.luckId = luckId
        apiGoodsDetail({
            id,
            visit: 1
        }).then((data) => {
            this.goodsInfo = data
            this.pageStatus = PageStatusEnum['NORMAL']
        })
    }
}
</script>
<style scoped lang="scss">
.container {
    background-color: white;
    border-radius: 20rpx;
    padding: 20rpx;
}
.btn {
    @include background_color();
    color: white;
    text-align: center;
    padding: 20rpx;
    border-radius: 20rpx;
    margin-top: 30rpx;
}
</style>
