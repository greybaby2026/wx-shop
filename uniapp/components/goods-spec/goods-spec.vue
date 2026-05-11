<template>
    <view :class="themeName">
        <u-popup
            v-model="show"
            mode="bottom"
            border-radius="14"
            :closeable="true"
            :safe-area-inset-bottom="true"
        >
            <!-- Header -->
            <view class="header">
                <!-- Goods Image -->
                <u-image
                    width="180"
                    height="180"
                    mode="aspectFill"
                    border-radius="10"
                    :src="goodsOptionsSpec.image || defaultInfo.image"
                    @click="previewGoodsImage"
                />
                <!-- Goods Checked Specifcation -->
                <view class="header-content u-skeleton-rect">
                    <!-- 砍价商品 -->
                    <view v-if="isBargain">
                        <view>
                            最低可砍至<price
                                class="header-content-price"
                                :content="goodsOptionsSpec.floor_price || defaultInfo.bargain_price"
                                main-size="36rpx"
                                minor-size="28rpx"
                                :color="themeColor"
                            />
                        </view>
                        <view class="muted sm">
                            原价<price
                                class="header-content-price"
                                :content="goodsOptionsSpec[priceKey] || defaultInfo.price"
                                main-size="28rpx"
                                minor-size="28rpx"
                                color="#999"
                            />
                        </view>
                    </view>

                    <view class="flex" v-else>
                        <!-- 售价 -->
                        <price
                            class="header-content-price"
                            :content="goodsOptionsSpec[priceKey] || defaultInfo.price"
                            main-size="46rpx"
                            minor-size="32rpx"
                            :color="themeColor"
                        />

                        <!-- 普通商品会员价 用户有登陆且且该商品有设置会员价才显示 -->
                        <view
                            class="vip-price flex m-b-20 m-l-10"
                            v-if="isNoMarking && goodsOptionsSpec.member_price && isLogin"
                        >
                            <view class="price-name xxs">会员价</view>
                            <view style="padding: 0 10rpx">
                                <price
                                    :content="goodsOptionsSpec.member_price"
                                    :weight="500"
                                    color="#7B3200"
                                >
                                </price>
                            </view>
                        </view>
                    </view>

                    <!-- 库存 -->
                    <view class="header-content-label" v-if="stock_show"
                        >库存：
                        <text v-if="goodsOptionsSpec.stock == 0">缺货</text>
                        <text v-else
                            >{{ goodsOptionsSpec.stock || defaultInfo.stock }}
                            {{ defaultInfo.unit || '' }}</text
                        >
                    </view>
                </view>
            </view>
            <!-- Main -->
            <view class="main">
                <!-- Specifcation -->
                <scroll-view scroll-y="true" class="scroll-spec">
                    <view class="spec" v-for="(specItem, specIndex) in specList" :key="specIndex">
                        <!-- Title -->
                        <view class="spec-title">{{ specItem.name }}</view>
                        <!-- Content -->
                        <view class="spec-content">
                            <view
                                v-for="(optionItem, optionIndex) in specItem.spec_list"
                                :key="optionIndex"
                                :class="[
                                    'spec-item',
                                    {
                                        'spec-item--active':
                                            specCheckedList[specIndex].id == optionItem.id
                                    }
                                ]"
                                @click="changeSpecOption(specIndex, optionItem.id)"
                            >
                                {{ optionItem.value }}
                                <text
                                    v-if="specDisabeld(optionItem.id)"
                                    class="spec-text"
                                    style="color: #999999"
                                    >缺货</text
                                >
                            </view>
                        </view>
                    </view>
                </scroll-view>

                <!-- Goods Number -->
                <view class="total">
                    <view class="total-label">
                        数量
                        <span class="muted xs p-l-10" v-if="defaultInfo.limit_type === 2">
                            每人限购{{ defaultInfo.limit_value }}件</span
                        >
                        <span class="muted xs p-l-10" v-if="defaultInfo.limit_type === 3">
                            每笔订单限购{{ defaultInfo.limit_value }}件</span
                        >
                    </view>
                    <u-number-box v-model="goodsTotal" :min="1" :max="addGoodsMax" />
                </view>
            </view>

            <!-- operation -->
            <view class="operation">
                <view class="operation-button disabled" v-if="goodsOptionsSpec.stock == 0">
                    缺货
                </view>
                <template v-else>
                    <view
                        class="operation-button"
                        v-for="(item, index) in buttons"
                        :key="index"
                        :style="[item.style]"
                        @click="onButtonHandle(item.event)"
                        v-show="reCheck != 1"
                    >
                        {{ item.text }}
                    </view>
                    <view
                        class="operation-button"
                        :style="{
                            border: `solid 1px ${this.themeColor}`
                        }"
                        @click="handlereCheck"
                        v-if="reCheck == 1"
                    >
                        确认修改
                    </view>
                </template>
            </view>
        </u-popup>
        <!-- 不能购买弹框 -->
        <u-modal
            :value="showModal"
            :show-confirm-button="false"
            :show-title="false"
            :mask-close-able="false"
        >
            <view class="modal">
                <view class="modal-text-title">很抱歉，购买失败</view>
                <view class="modal-text-content m-t-40 m-b-40">商品不能购买</view>
                <button
                    class="br60 modal-btn"
                    size="md"
                    hover-class="none"
                    @tap="showModal = false"
                >
                    我知道了
                </button>
            </view>
        </u-modal>
    </view>
</template>

<script>
/**
 * @description 商品规格选择Popup弹窗
 * @property {String} mode 商品规格模式 (默认值: normal)
 * @property {Array} specList 规格列表 (必填项)
 * @property {Array} specMap 规格映射表 (必填项)
 * @property {Array} buttons 按钮组 (默认: [{ text: '立即购买', style: {}, event: 'buy' }])
 * @event {Function} [event] 点击按钮 (事件名由按钮组的event提供)
 * @example <goods-spec v-model="show" @buy="handleSubmit" />
 */

import { apicheckCanBuy } from '@/api/goods'
import { mapGetters } from 'vuex'
import { apieditgoodsitem } from '@/api/store'
export default {
    name: 'GoodsSpec',

    props: {
        // 是否营销商品
        isNoMarking: {
            type: Boolean,
            default: false
        },
        // 显示状态
        value: {
            type: Boolean,
            default: false
        },

        // 商品规格模式
        mode: {
            type: String,
            default: 'normal'
        },

        // 按钮组
        buttons: {
            type: Array,
            default: () => [
                {
                    text: '立即购买',
                    style: {},
                    event: 'buy'
                }
            ]
        },

        // 规格列表
        specList: {
            type: Array,
            default: () => []
        },

        // 规格映射表
        specMap: {
            type: Array,
            default: () => []
        },
        // 价格字段名
        priceKey: {
            type: String,
            default: 'sell_price'
        },
        // 默认商品信息
        defaultInfo: {
            type: Object,
            default: () => ({})
        },
        isBargain: {
            type: Boolean,
            default: false
        },
        //重选规格
        reCheck: {
            type: Number,
            default: 0
        },
        //购物车id
        cartId: {
            type: Number,
            default: 0
        },
        stock_show: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {
            specMapTable: new Map(), // 规格值映射表
            specCheckedList: [], // 已选择的规格列表
            goodsTotal: 1, // 商品数量
            goodsOptionsSpec: {}, // 商品已选择的规格
            nullSpec: [],
            // stockoutList: [],
            showModal: false //不能购买弹框
        }
    },

    computed: {
        ...mapGetters(['isLogin']),

        // 弹窗Popup显示状态
        show: {
            get: function () {
                return this.value
            },
            set: function (value) {
                this.$emit('input', value)
            }
        },
        specCheckedIds() {
            return this.specCheckedList
                .map((item) => item.id)
                .filter((item) => item)
                .join(',')
        },
        specDisabeld() {
            return (id) => this.nullSpec.find((item) => item == id)
        },
        addGoodsMax() {
            let maxGoodsNums = 1
            // this.defaultInfo.limit_type === 1
            //   ? this.goodsOptionsSpec.stock
            //   : this.defaultInfo.limit_value;
            if (this.defaultInfo.limit_type === 1) {
                maxGoodsNums = this.goodsOptionsSpec.stock
            } else if (this.defaultInfo.limit_type == 2 || this.defaultInfo.limit_type == 3) {
                if (this.goodsOptionsSpec.stock <= this.defaultInfo.limit_value) {
                    maxGoodsNums = this.goodsOptionsSpec.stock
                } else {
                    maxGoodsNums = this.defaultInfo.limit_value
                }
            }
            return maxGoodsNums
        },
        ...mapGetters['isLogin']
    },

    methods: {
        // 重选规格
        async handlereCheck() {
            try {
                await apieditgoodsitem({
                    cart_id: this.cartId,
                    item_id: this.goodsOptionsSpec.id,
                    goods_num: this.goodsTotal
                })
                // this.$Router.replace({
                //     path: '/pages/shop_cart/shop_cart'
                // })
                uni.switchTab({ url: '/pages/shop_cart/shop_cart' })
            } catch (error) {
                console.log(error)
            }
        },
        // 选择规格
        changeSpecOption(index, value) {
            const { id } = this.specCheckedList[index]
            if (id == value) {
                value = ''
            }
            this.$set(this.specCheckedList[index], 'id', value)
        },

        // 点击按钮
        onButtonHandle(event) {
            apicheckCanBuy({
                item_id: this.goodsOptionsSpec.id,
                num: this.goodsTotal
            }).then(
                (data) => {
                    const isOverLimit = this.checkGoodsLimit(event)
                    if (isOverLimit)
                        return this.$toast({
                            title: `当前商品限购${this.defaultInfo.limit_value}件，已超出！`
                        })
                    const nullSelect = this.specCheckedList.find((item) => !item.id)
                    if (nullSelect) {
                        this.$toast({
                            title: `请选择${nullSelect.name}`
                        })
                        return
                    }
                    this.show = false
                    this.throwSpecEmit(event)
                },
                (err) => {
                    if (!this.isLogin) return this.$Router.push('/pages/login/login')

                    this.showModal = true
                }
            )
        },

        // 抛出事件
        throwSpecEmit(name) {
            this.$emit(name, {
                spec: this.goodsOptionsSpec,
                number: this.goodsTotal
            })
        },

        // 查看商品图片
        previewGoodsImage() {
            const image = this.goodsOptionsSpec.image || this.defaultInfo.image
            uni.previewImage({
                urls: [image]
            })
            this.$emit('closer')
        },
        checkStock() {
            let nullStock = this.specMap.filter((item) => item.stock == 0)
            this.nullSpec = nullStock.reduce((prev, item) => {
                const idsArr = item.spec_value_ids.split(',')
                const checkIdsArr = this.specCheckedIds.split(',')
                const includesIds = idsArr.filter((item) => this.specCheckedIds.includes(item))
                if (!this.specCheckedIds) return prev
                // 全选
                if (item.spec_value_ids == this.specCheckedIds) {
                    return prev.concat(...checkIdsArr)
                }
                // 剩下一个未选
                if (includesIds.length && includesIds.length == this.specCheckedList.length - 1) {
                    return prev.concat(idsArr.filter((item) => !this.specCheckedIds.includes(item)))
                }

                return prev
            }, [])
            // console.log(this.nullSpec)
        },
        // 校验商品是否超出限购
        checkGoodsLimit(event) {
            if (this.defaultInfo.limit_type === 1) return false

            switch (event) {
                case 'cart':
                    if (
                        this.defaultInfo.cart_goods_num + this.goodsTotal >
                        this.defaultInfo.limit_value
                    )
                        return true
                    break
                case 'buy':
                    if (
                        this.defaultInfo.limit_type === 2 &&
                        this.defaultInfo.buy_num + this.goodsTotal > this.defaultInfo.limit_value
                    )
                        return true
                    break
            }
        }
    },

    watch: {
        // 变更规格
        specCheckedList: {
            handler(value) {
                const selectSepc = this.specMapTable.get(this.specCheckedIds)
                console.log(selectSepc, 1111)

                this.checkStock()
                if (this.goodsTotal > selectSepc?.stock) {
                    this.goodsTotal = selectSepc.stock
                }
                if (selectSepc?.stock == 0) {
                    this.goodsTotal = 1
                }
                if (selectSepc) {
                    this.goodsOptionsSpec = selectSepc
                } else {
                    this.goodsOptionsSpec = {}
                }
                this.throwSpecEmit('change')
            },
            deep: true
        },
        // 初始化规格映射表
        specMap: {
            handler(value) {
                // 配置隐射关系spec_value_ids => self
                value.map((item) => this.specMapTable.set(item.spec_value_ids, item))
                const checkItem = value.find((item) => item.stock)
                const checkItemIds = checkItem ? checkItem.spec_value_ids.split(',') : ''

                //初始化选中数据
                this.specCheckedList = this.specList.map((item, index) => ({
                    name: item.name,
                    id: checkItemIds[index] || item.spec_list[0].id
                }))
            },
            immediate: true
        }
    }
}
</script>

<style lang="scss" scoped>
.header {
    display: flex;
    padding: 20rpx;

    &-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-left: 20rpx;

        &-label {
            padding-top: 10rpx;
            font-size: $-font-size-sm;
        }

        &-price {
            margin-bottom: 20rpx;
        }

        // 会员价
        .vip-price {
            margin-top: 5rpx;
            background-color: #ffe9ba;
            color: #ffd4b7;
            line-height: 36rpx;
            border-radius: 6rpx;
            overflow: hidden;

            .price-name {
                background-color: #101010;
                padding: 3rpx 12rpx;
                position: relative;
                overflow: hidden;

                &::after {
                    content: '';
                    display: block;
                    width: 20rpx;
                    height: 20rpx;
                    position: absolute;
                    right: -15rpx;
                    background-color: #ffe9ba;
                    border-radius: 50%;
                    top: 50%;
                    transform: translateY(-50%);
                    box-sizing: border-box;
                }
            }
        }
    }
}

.main {
    padding: 20rpx;
    border-top: $-solid-border;

    .scroll-spec {
        min-height: 320rpx;
        max-height: 600rpx;
    }

    .spec {
        padding: 10rpx 0;

        &-title {
            font-size: $-font-size-nr;
        }

        &-content {
            display: flex;
            flex-wrap: wrap;
            padding-top: 20rpx;
        }
        &-text {
            position: absolute;
            right: -28rpx;
            top: -24rpx;
            background-color: white;
            padding: 4rpx;
            border-radius: 10rpx;
        }
        &-item {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100rpx;
            box-sizing: border-box;
            padding: 8rpx 25rpx;
            margin: 0 20rpx 20rpx 0;
            border-radius: 60px;
            font-size: $-font-size-sm;
            background-color: #f4f4f4;
            position: relative;

            &--active {
                @include font_color();
                @include box_shadow(0, 0, 0, 1px);
                @include background_color(0.2);
            }

            // &--disabeld {
            // 	position: relative;
            // 	@include font_color();
            // 	@include box_shadow(0, 0, 0, 1px);
            // 	@include background_color(0.2);

            // 	&::after {
            // 		position: absolute;
            // 		top: -1em;
            // 		right: -0.5em;
            // 		content: "缺货";
            // 		display: block;
            // 		padding: 2rpx 4rpx;
            // 		border: solid 1px #FFFFFF;
            // 		border-radius: 2px;
            // 		font-size: $-font-size-xxs;
            // 		@include background_color();
            // 		color: #FFFFFF;
            // 	}
            // }
        }
    }

    .total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20rpx 0;

        &-label {
            font-size: $-font-size-nr;
        }
    }
}

.operation {
    display: flex;
    justify-content: space-between;
    padding: 10rpx 20rpx;

    &-button {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 82rpx;
        border-radius: 60px;
        font-size: $-font-size-md;
        color: #ffffff;

        @include background_color();
        &:nth-child(n + 2) {
            margin-left: 20rpx;
        }

        &.disabled {
            background-color: #d7d7d7;
        }
    }
}
.modal {
    padding: 50rpx 0;
    &-text-title {
        text-align: center;
        font-size: large;
        font-weight: 500;
    }
    &-text-content {
        text-align: center;
    }
}
.modal-btn {
    @include background_color();
    color: #fff;
    width: 80%;
    margin: 0 auto;
}
</style>
