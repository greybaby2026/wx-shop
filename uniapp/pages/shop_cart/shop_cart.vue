<template>
    <view class="shop-cart" :class="themeName" :style="themeCssVars">
		<!-- #ifndef  H5 -->
		<u-sticky offset-top="0" h5-nav-height="0" bg-color="transparent">
		  	<u-navbar :is-back="false" title="购物车" :title-bold="true" :is-fixed="false" :border-bottom="false"
		  		:background="{ background: 'rgba(256,256, 256, 1)' }"
		  		title-color="#000"></u-navbar>
		</u-sticky>
		<!-- #endif -->
        <view class="main">
            <!-- 有效商品 -->
            <block v-if="isLogin && activeLists.length != 0">
                <view class="cart-header">
                    <span> 共{{ activeLists.length }}件商品 </span>
                    <span v-if="admin" @click="admin = !admin"> 管理 </span>
                    <span v-else @click="admin = !admin"> 完成 </span>
                </view>
                <view class="goods-container">
                    <block v-for="(item, index) in activeLists" :key="index">
                        <u-swipe-action
                            :index="index"
                            :options="option"
                            @open="handleactionOpen"
                            @click="handleactionClick"
                            :show="activeIndex == item.id"
                        >
                            <!-- 商品卡片 -->
                            <view class="cart-list" style="width: 100%">
                                <view class="cart-item bg-white flex">
                                    <!-- 商品头部 -->
                                    <view class="row-between flex select">
                                        <u-checkbox
                                            v-if="admin"
                                            label-size="28rpx"
                                            :active-color="themeColor"
                                            v-model="item.selected"
                                            @change="changeGoodsStatus(item.selected, item.id)"
                                            :label-disabled="false"
                                            shape="circle"
                                            :disabled="
                                                item.sku_is_delete == 1 || item.sku_stock_none == 1
                                            "
                                        >
                                        </u-checkbox>
                                        <u-checkbox
                                            v-else
                                            label-size="28rpx"
                                            :active-color="themeColor"
                                            v-model="item.adminCheck"
                                            :label-disabled="false"
                                            shape="circle"
                                            @change="adminGoodsStatus"
                                        >
                                        </u-checkbox>
                                    </view>
                                    <!-- 商品的主内容 -->
                                    <router-link
                                        :to="`/pages/goods_detail/goods_detail?id=${item.goods_id}`"
                                        style="width: 100%"
                                    >
                                        <view
                                            class="flex"
                                            :style="{ padding: '20rpx', width: '100%' }"
                                        >
                                            <u-image
                                                width="180rpx"
                                                height="180rpx"
                                                :src="
                                                    item.goods_item.image
                                                        ? item.goods_item.image
                                                        : item.goods.image
                                                "
                                            />
                                            <view class="m-l-20" style="flex: 1">
                                                <view class="line-2 nr">{{ item.goods.name }}</view>
                                                <block
                                                    v-if="
                                                        item.sku_is_delete == 0 &&
                                                        item.sku_stock_none == 0
                                                    "
                                                >
                                                    <view class="muted xs line-2 m-t-10">
                                                        {{ item.goods_item.spec_value_str }}
                                                    </view>
                                                    <view class="row-between flex m-t-20">
                                                        <view class="price flex primary">
                                                            <price
                                                                :content="
                                                                    item.goods_item.sell_price
                                                                "
                                                                :color="themeColor"
                                                                mainSize="36rpx"
                                                                minorSize="28rpx"
                                                            ></price>
                                                        </view>
                                                        <view class="cartNum" @tap.stop="">
                                                            <u-number-box
                                                                :disabled="item.cart_status != 0"
                                                                v-model="item.goods_num"
                                                                :min="1"
                                                                async-change
                                                                positiveInteger
                                                                @change="
                                                                    countChange($event, item.id)
                                                                "
                                                            />
                                                        </view>
                                                    </view>
                                                </block>
                                                <block v-else>
                                                    <view
                                                        class="flex row-between"
                                                        style="margin-top: 80rpx"
                                                    >
                                                        <view> 请重新选择商品规格 </view>
                                                        <!-- <router-link
                              :to="`/pages/goods_detail/goods_detail?id=${item.goods_id}&cart_id=${item.id}&reCheck=1`"
                            > -->
                                                        <view
                                                            class="change-sku"
                                                            @tap.stop="
                                                                handlereCheck(
                                                                    item.goods_id,
                                                                    item.id
                                                                )
                                                            "
                                                            >重选</view
                                                        >
                                                        <!-- </router-link> -->
                                                    </view>
                                                </block>
                                            </view>
                                        </view>
                                    </router-link>
                                </view>
                            </view>
                        </u-swipe-action>
                    </block>
                </view>
            </block>
            <!-- 无效商品 -->
            <block v-if="isLogin && inactiveLists.length != 0">
                <view class="disable-header">
                    <span>失效商品{{ inactiveLists.length }}件</span>
                    <span style="color: #dd524d" @click="handleClearAll">清空失效商品</span>
                </view>
                <view class="goods-container">
                    <block v-for="(item, index) in inactiveLists" :key="index">
                        <u-swipe-action
                            :index="index"
                            :options="option"
                            @open="handledisaactionOpen"
                            @click="handledisactionClick"
                            :show="activeIndex == item.id"
                        >
                            <!-- 商品卡片 -->
                            <view class="cart-list" style="width: 100%">
                                <view
                                    class="cart-item bg-white flex"
                                    :class="{
                                        invalid:
                                            item.cart_status == 1 ||
                                            (item.goods.spec_type == 1 &&
                                                item.goods_item.stock == 0)
                                    }"
                                >
                                    <!-- 商品头部 -->
                                    <view class="row-between flex select">
                                        <u-checkbox
                                            v-if="admin"
                                            label-size="28rpx"
                                            :active-color="themeColor"
                                            v-model="item.selected"
                                            @change="changeGoodsStatus(item.selected, item.id)"
                                            :label-disabled="false"
                                            shape="circle"
                                            :disabled="
                                                item.cart_status == 1 ||
                                                (item.goods.spec_type == 1 &&
                                                    item.goods_item.stock == 0)
                                            "
                                        >
                                        </u-checkbox>
                                        <u-checkbox
                                            v-else
                                            label-size="28rpx"
                                            :active-color="themeColor"
                                            v-model="item.adminCheck"
                                            :label-disabled="false"
                                            shape="circle"
                                            @change="adminGoodsStatus"
                                        ></u-checkbox>
                                    </view>
                                    <!-- 商品的主内容 -->
                                    <router-link
                                        :to="`/pages/goods_detail/goods_detail?id=${item.goods_id}`"
                                        style="width: 100%"
                                    >
                                        <view
                                            class="flex"
                                            :style="{ padding: '20rpx', width: '100%' }"
                                        >
                                            <view class="disable-tip">
                                                <u-image
                                                    width="180rpx"
                                                    height="180rpx"
                                                    :src="item.goods.image"
                                                />
                                                <span
                                                    class="disable-text"
                                                    v-if="
                                                        item.goods.spec_type == 1 &&
                                                        item.goods_item.stock == 0
                                                    "
                                                    >库存不足</span
                                                >
                                                <span class="disable-text" v-else>商品下架</span>
                                            </view>
                                            <view class="m-l-20 disable-body" style="flex: 1">
                                                <view class="line-2 nr">{{ item.goods.name }}</view>
                                                <view class="row-between flex m-t-20">
                                                    该商品已不能购买
                                                </view>
                                            </view>
                                        </view>
                                    </router-link>
                                </view>
                            </view>
                        </u-swipe-action>
                    </block>
                </view>
            </block>
            <!-- 购物车空状态 -->
            <view
                class="cart-null bg-white m-b-20"
                style="padding: 80rpx 0 50rpx"
                v-if="isLogin && cartLists.length == 0"
            >
                <image class="img-null" src="/static/images/empty/cart.png"></image>
                <view class="muted m-b-20">购物车暂无任何商品~</view>
                <view class="br60 btn flex row-center" @tap="goShoping">去逛逛</view>
            </view>

            <!-- 是否登录的空状态 -->
            <view v-if="!isLogin" class="login column-center" style="padding: 200rpx 0 50rpx">
                <image class="img-null" src="/static/images/empty/cart.png"></image>
                <view class="muted mt20">登录后才能查看购物车哦</view>
                <navigator class="white br60 row-center btn" url="/pages/login/login">
                    <text>去登录</text>
                </navigator>
            </view>

            <!-- diy组件区 -->
            <block v-for="(item, cIndex) in pagesData" :key="cIndex">
                <template v-if="item.name == 'goodsrecom'">
                    <w-goodsrecom
                        v-show="item.show"
                        :content="item.content"
                        :styles="item.styles"
                    />
                </template>
            </block>
        </view>

        <!-- 底部结算栏 -->
        <view class="footer flex bg-white">
            <!-- left -->
            <checkbox-group class="normal" v-if="admin">
                <u-checkbox
                    label-size="28rpx"
                    @change="changeAllSelect"
                    id="checkAll"
                    value="all"
                    :active-color="themeColor"
                    v-model="isSelectedAll"
                    :label-disabled="false"
                    :disabled="selectAllDisabled"
                    shape="circle"
                >
                    <text class="normal">全选</text>
                </u-checkbox>
            </checkbox-group>
            <checkbox-group class="normal" v-else>
                <u-checkbox
                    label-size="28rpx"
                    @change="adminAllSelect"
                    id="checkAll"
                    value="all"
                    v-model="adminCheckAll"
                    :active-color="themeColor"
                    :label-disabled="false"
                    :disabled="selectAllDisabled"
                    shape="circle"
                >
                    <text class="normal">全选</text>
                </u-checkbox>
            </checkbox-group>
            <!-- right -->
            <view class="all-price nr m-r-20 flex row-right">
                <block v-if="admin">
                    <view>合计：</view>
                    <price
                        :content="total_amount || 0"
                        :color="themeColor"
                        mainSize="36rpx"
                        minorSize="28rpx"
                    ></price>
                </block>
                <block v-else>
                    <view
                        class="right-btn br60 white"
                        :style="{
                            background:
                                cartLists.filter((item) => {
                                    return item.adminCheck == true
                                }).length == 0
                                    ? '#cfcfcf'
                                    : '#dd524d'
                        }"
                        @click="handleAdminDel"
                        >删除</view
                    >
                </block>
            </view>
            <block v-if="admin">
                <view class="right-btn br60 white" v-if="getResultBtn" @tap="goToConfirm"
                    >去结算</view
                >
                <view class="right-btn br60 lighter" style="background: #f2f2f2" v-else
                    >去结算</view
                >
            </block>
        </view>

        <!-- 是否删除商品的弹窗 -->
        <u-modal
            v-model="delPopup"
            :showCancelButton="true"
            comfirm-text="狠心删除"
            :confirm-color="themeColor"
            :show-title="false"
            @confirm="goodsDelete"
        >
            <view style="padding-top: 40rpx" class="flex row-center">
                <view style="margin: 50rpx 0">确认删除该商品吗？</view>
            </view>
        </u-modal>
        <tabbar />

        <!-- 加载中 -->
        <u-mask
            :show="isChangeLoading"
            :mask-click-able="false"
            :custom-style="{ background: 'rgba(0, 0, 0, 0)' }"
        ></u-mask>
        <view class="loading" v-if="isChangeLoading">
            <u-loading size="40" :color="themeColor"></u-loading>
            <view class="white m-t-20"> 加载中... </view>
        </view>
    </view>
</template>

<script>
import {
    apiCartLists, //商品列表
    apiCartDel, //商品删除
    apiCartCountChange, //商品数量改变
    apiCartSelectChange, //选择商品
    apiCartNum, //购物车数量
    apiGetPage, //获取当前diy页面
    apiclearAll //清空失效商品
} from '@/api/store'
import { OrderTypeEnum } from '@/utils/enum'
import { trottle } from '@/utils/tools'

export default {
    name: 'ShopCart',

    data() {
        return {
            pagesData: [], //diy的组件
            total_amount: '', //合计｜｜总余额
            cartLists: [], //购物车数据
            cartListsId: [], //购物车数据的所有ID
            total_amount: '', //购物车合计金额
            delPopup: false, //是否弹出删除商品的弹窗
            cart_id: 0, //删除的购物车ID
            isSelectedAll: false, //是否全选
            isChangeLoading: false, //是否是操作请求中状态
            option: [
                {
                    text: '删除',
                    style: {
                        backgroundColor: '#dd524d'
                    }
                }
            ], //滑动组件配置
            activeIndex: -1, //滑动组件开启对应的index
            activeLists: [],
            inactiveLists: [],
            admin: true,
            adminCheckAll: false
        }
    },

    methods: {
        //重选按钮
        handlereCheck(goods_id, cart_id) {
            this.$Router.push({
                path: `/pages/goods_detail/goods_detail?id=${goods_id}&cart_id=${cart_id}&reCheck=1`
            })
        },

        // 获取购物车数据列表
        getCartList() {
            return apiCartLists().then((res) => {
                this.total_amount = res.total_amount
                res.lists.forEach((item, index) => {
                    res.lists[index].selected = item.selected == 1 ? true : false
                })
                this.cartLists = res.lists
                this.baseSelect(res.lists)
                this.$store.dispatch('getCartNum')
                this.isChangeLoading = false
                return Promise.resolve()
            })
        },

        // 初始化商品的选中状态
        baseSelect(data) {
            this.cartListsId = []
            const res = data.filter((el) => {
                this.cartListsId.push(el.id)
                return el.selected == false
            })
            this.isSelectedAll = true
            this.goodsListActive.map((item) => {
                if (!item.selected) this.isSelectedAll = false
            })
        },

        // 商品数量的修改
        countChange({ value }, id) {
            this.isChangeLoading = true
            apiCartCountChange({
                cart_id: id,
                goods_num: value
            })
                .then((res) => {
                    this.$store.dispatch('getCartNum')
                })
                .finally(() => {
                    this.getCartList()
                })
        },

        // 弹窗拿到当前需要删除的商品购物车ID
        changeDelPopup(cart_id) {
            this.delPopup = true
            this.cart_id = cart_id
        },

        // 删除购物车的商品
        async goodsDelete() {
            this.isChangeLoading = true
            await apiCartDel({
                cart_id: this.cart_id
            })
            await this.getCartList()
            this.$toast({
                title: '删除成功'
            })
        },

        // 单选
        changeGoodsStatus(status, id) {
            this.isChangeLoading = true
            status = status == 1 ? 0 : 1
            apiCartSelectChange({
                cart_id: [id],
                selected: status
            }).then((res) => {
                this.getCartList()
            })
        },
        //管理状态下单选商品
        async adminGoodsStatus() {
            console.log(this.cartLists)
            await this.$nextTick()
            const temp = this.cartLists.filter((item) => {
                return item.adminCheck == true
            })
            if (temp.length == this.cartLists.length) {
                this.adminCheckAll = true
            } else {
                this.adminCheckAll = false
            }
        },
        // 全选商品
        changeAllSelect() {
            this.isChangeLoading = true
            const status = this.isSelectedAll == true ? 0 : 1
            const cartIDs = this.goodsListActive.map((item) => item.id)
            apiCartSelectChange({
                cart_id: cartIDs,
                selected: status
            }).then((res) => {
                this.getCartList()
            })
        },
        //管理商品状态下全选商品
        adminAllSelect() {
            this.cartLists.forEach((i) => {
                i.adminCheck = !this.adminCheckAll
            })
        },

        // 去购物
        goShoping() {
            this.$Router.replaceAll('/pages/index/index')
        },

        // 去结算
        goToConfirm() {
            const carts = this.cartLists.filter((item) => item.selected).map((item) => item.id)
            if (carts.length == 0) return this.$toast({ title: '请选择商品' })
            this.$Router.push({
                path: '/bundle/pages/goods_order/goods_order',
                query: {
                    from: OrderTypeEnum['GOODS'],
                    payload: {
                        source: 'cart',
                        cart_id: carts
                    }
                }
            })
        },

        // 去首页
        changeTab() {
            this.$Router.replaceAll({
                path: '/pages/index/index'
            })
        },

        getPage() {
            apiGetPage({
                type: 4
            }).then((res) => {
                const {
                    common: { title },
                    common,
                    content
                } = res
                uni.setNavigationBarTitle({
                    title
                })
                this.pagesData = content
            })
        },
        //滑动卡片关闭其他卡片（排他）--有效商品
        handleactionOpen(index) {
            this.activeIndex = this.activeLists[index].id
        },
        //滑动卡片关闭其他卡片（排他）--无效商品
        handledisaactionOpen(index) {
            this.activeIndex = this.inactiveLists[index].id
        },
        //滑动卡片删除按钮处理 --有效商品
        handleactionClick(index) {
            this.changeDelPopup(this.activeLists[index].id)
        },
        //滑动卡片删除按钮处理 --无效商品
        handledisactionClick(index) {
            this.changeDelPopup(this.inactiveLists[index].id)
        },
        //清空失效商品
        async handleClearAll() {
            this.isChangeLoading = true
            const cart_ids = this.inactiveLists.map((item) => {
                return item.id
            })

            await apiclearAll({ cart_ids })
            this.getCartList()
        },
        //管理商品状态下删除
        async handleAdminDel() {
            this.isChangeLoading = true
            console.log(this.cartLists)
            const temp = this.cartLists.filter((item) => {
                return item.adminCheck == true
            })
            const cart_ids = temp.map((i) => {
                return i.id
            })
            if (cart_ids.length == 0) {
                return (this.isChangeLoading = false)
            }
            await apiclearAll({ cart_ids })
            this.getCartList()
        }
    },
    computed: {
        cartNum() {
            return this.$store.state.cart.cartNum
        },
        getResultBtn() {
            const carts = this.cartLists.filter((item) => item.selected).map((item) => item.id)
            if (carts.length == 0) {
                this.isSelectedAll = false
                return false
            } else {
                return true
            }
        },

        // 可售卖商品列表
        goodsListActive() {
            return (
                this.cartLists.filter(
                    (item) =>
                        item.cart_status == 0 && item.sku_is_delete == 0 && item.sku_stock_none == 0
                ) || []
            )
        },

        // 是否可全选操作
        selectAllDisabled() {
            return !!!this.goodsListActive.length
        }
    },
    watch: {
        cartLists() {
            //添加admincheck响应式属性，用于管理状态下删除商品
            this.cartLists.forEach((item) => {
                this.$set(item, 'adminCheck', false)
            })
            //过滤有效商品
            this.activeLists = this.cartLists.filter((i) => {
                return !(i.cart_status == 1 || (i.goods.spec_type == 1 && i.goods_item.stock == 0))
            })
            //过滤无效商品 单规格库存不足或者商品下架 多规格商品下架
            this.inactiveLists = this.cartLists.filter((i) => {
                return i.cart_status == 1 || (i.goods.spec_type == 1 && i.goods_item.stock == 0)
            })
        }
    },
    async onShow() {
        // this.getCartNum()
        await this.getCartList()
        if (this.isLogin) {
            this.getPage()
        }
    },

    created() {
        this.countChange = trottle(this.countChange, 200)
    }
}
</script>
<style lang="scss">
.shop-cart {
    position: relative;
    .loading {
        position: fixed;
        left: 39vw;
        top: 35vh;
        height: 170rpx;
        width: 170rpx;
        border-radius: 20rpx;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
}
.main {
    padding-bottom: 100rpx;
    .cart-header {
        padding: 25rpx 20rpx;
        display: flex;
        justify-content: space-between;
    }
    .goods-container {
        margin: 0 20rpx;
        border-radius: 10rpx;
    }
    .disable-header {
        padding: 25rpx 20rpx;
        margin-top: 30rpx;
        margin-left: 20rpx;
        margin-right: 20rpx;
        display: flex;
        justify-content: space-between;
        background-color: white;
    }
    .disable-body {
        height: 160rpx;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}

.cart-list {
    .cart-item {
        margin: 20rpx 20rpx 0;

        &.invalid {
            opacity: 0.7;
            // background-color: #999;
            // @include background_color();
        }
    }

    .select {
        height: 80rpx;
        // padding: 20rpx;
        // @include border_color();
        // border-style: solid;
    }
}

.cart-null {
    text-align: center;

    .btn {
        @include font_color();
        @include border_color();
        border-width: 1rpx;
        border-style: solid;
        width: 184rpx;
        margin-left: auto;
        margin-right: auto;
        padding: 8rpx 24rpx;
    }
}

.footer {
    left: 0;
    margin-bottom: 100rpx;
    bottom: calc(env(safe-area-inset-bottom));
    width: 100%;
    z-index: 20;
    height: 100rpx;
    position: fixed;
    padding: 0 24rpx;
    box-sizing: border-box;
    background-color: #ffffff;
    box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.1);

    .all-price {
        text-align: right;
        flex: 1;
    }

    .right-btn {
        padding: 13rpx 45rpx;
        @include background_color();
    }
}

.login {
    // height:calc(100vh - var(--window-bottom));
    background: #fff;
    text-align: center;

    .btn {
        width: 280rpx;
        line-height: 70rpx;
        margin: 40rpx auto 0;
        background-color: #09bb07;

        image {
            width: 50rpx;
            height: 50rpx;
        }
    }
}
.disable-tip {
    position: relative;
    .disable-text {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        color: white;
        background: rgba(0, 0, 0, 0.5);
    }
}
.change-sku {
    @include font_color();
    @include border_color();
    width: 112rpx;
    height: 52rpx;
    border-radius: 26rpx;
    border: 1px solid;
    text-align: center;
    line-height: 52rpx;
}
</style>
