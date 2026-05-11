<template>
    <div class="window__aside-right">
        <!-- Tabs 面板 -->
        <el-tabs v-model="tabsActiveIndex" :stretch="true">
            <!-- 用户资料 -->
            <el-tab-pane label="用户资料" :name="TabsMap['USER']">
                <div class="tab-content">
                    <div class="user-info" v-if="userInfo.id">
                        <div class="info-header flex">
                            <img class="avatar m-r-10" :src="userInfo.avatar" />
                            <div class="name line-2">{{ userInfo.nickname }}</div>
                        </div>
                        <div class="info-item">
                            <span class="label">用户编号：</span>
                            <span>{{ userInfo.sn }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">用户等级：</span>
                            <span>{{ userInfo.level_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">手机号码：</span>
                            <span>{{ userInfo.mobile }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">累计消费：</span>
                            <span>{{ userInfo.total_order_amount }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">生日：</span>
                            <span>{{ userInfo.birthday }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">注册来源：</span>
                            <span>{{ userInfo.register_source }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">注册时间：</span>
                            <span>{{ userInfo.create_time }}</span>
                        </div>
                    </div>
                    <el-empty v-else :image-size="100"></el-empty>
                </div>
            </el-tab-pane>

            <!-- 订单信息 -->
            <el-tab-pane label="订单信息" :name="TabsMap['ORDER']">
                <el-scrollbar class="ls-scrollbar" style="height: 670px">
                    <loading-more v-model="status" @load="getUserOrder" v-if="toId">
                        <div class="tab-content">
                            <div class="order-info">
                                <el-input
                                    v-model="orderSn"
                                    size="small"
                                    placeholder="请输入订单号搜索"
                                    @keyup.enter.native="refresh"
                                ></el-input>
                                <div class="order-list" v-if="orderLists.length">
                                    <div
                                        class="order-item"
                                        v-for="(item, index) in orderLists"
                                        :key="index"
                                    >
                                        <div
                                            class="order-goods flex col-top"
                                            v-for="(
                                                gitem, gindex
                                            ) in item.order_goods"
                                            :key="gindex"
                                        >
                                            <div class="flex-none">
                                                <el-image
                                                    :src="gitem.goods_image"
                                                    style="
                                                        width: 68px;
                                                        height: 68px;
                                                    "
                                                ></el-image>
                                            </div>
                                            <div class="flex-1 m-l-8" style="min-width: 0">
                                                <div
                                                    class="goods-name line-1"
                                                >{{ gitem.goods_name }}</div>
                                                <div class="muted m-t-5">{{ gitem.spec_value_str }}</div>

                                                <div class="price flex row-between m-t-5">
                                                    <div class="nr">
                                                        ￥{{
                                                            gitem.goods_price
                                                        }}
                                                    </div>
                                                    <div class="muted">x{{ gitem.goods_num }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order-con">
                                            <div class="m-t-15">
                                                <span class="muted">订单类型：</span>
                                                <span>
                                                    {{
                                                        item.order_type_desc
                                                    }}
                                                </span>
                                            </div>
                                            <div class="m-t-15">
                                                <span class="muted">订单编号：</span>
                                                <span>{{ item.sn }}</span>
                                            </div>
                                            <div class="m-t-15">
                                                <span class="muted">订单状态：</span>
                                                <span
                                                    class="order-status"
                                                    :class="{
                                                        'wait-pay':
                                                            item.order_status ==
                                                            0,
                                                    }"
                                                >
                                                    {{
                                                        item.order_status_desc
                                                    }}
                                                </span>
                                            </div>
                                            <div class="m-t-15">
                                                <span class="muted">订单金额：</span>
                                                <span>
                                                    {{
                                                        item.order_amount
                                                    }}
                                                </span>
                                            </div>
                                            <div class="m-t-15">
                                                <span class="muted">下单时间：</span>
                                                <span>
                                                    {{
                                                        item.create_time
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </loading-more>
                    <el-empty v-else :image-size="100"></el-empty>
                </el-scrollbar>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>


<script>
import { apiUserInfo, apiUserOrder, apiServiceList } from '@/api/app'
import LoadingMore from '@/components/LoadingMore'
import { E_Load } from '@/utils/enum'
export default {
    name: 'TheWindowAsideRight',
    components: {
        LoadingMore,
    },
    props: {
        toId: {
            type: [Number, String],
        },
    },

    inject: ['send'],
    data() {
        return {
            /* Tabs 索引 */
            tabsActiveIndex: '',
            /* Tabs 映射 */
            TabsMap: Object.freeze({
                USER: '1',
                ORDER: '2',
            }),
            // 订单编号
            orderSn: '',
            userInfo: {},
            orderLists: [],

            showKefu: false,
            KefuLists: [],
            page: 1,
            size: 15,
            status: E_Load['NORMAL'],
        }
    },
    watch: {
        toId(val) {
            this.page = 1
            this.status = E_Load['LOAD']
            this.orderLists = []
            this.orderSn = ''
            if (!val) {
                this.userInfo = {}
                return
            }
            this.getUserInfo()
            this.getUserOrder()
        },
    },
    methods: {
        refresh() {
            this.page = 1
            this.status = E_Load['LOAD']
            this.orderLists = []
            this.getUserOrder()
        },
        getUserInfo() {
            apiUserInfo({
                user_id: this.toId,
            }).then((data) => {
                this.userInfo = data
            })
        },
        getUserOrder() {
            if (this.status == E_Load['EMPTY']) return
            apiUserOrder({
                page_size: this.size,
                page_no: this.page,
                user_id: this.toId,
                sn: this.orderSn,
            }).then((data) => {
                this.page++
                this.orderLists.push(...data.lists)
                this.status = E_Load['NORMAL']
                this.$nextTick(() => {
                    if (data.lists.length < this.size) {
                        this.status = E_Load['EMPTY']
                    }
                })
            })
        },
    },
    created() {
        /* 初始化Tabs选项 */
        this.tabsActiveIndex = this.TabsMap['USER']
    },
}
</script>


<style lang="scss" scoped>
.window__aside-right {
    overflow: hidden;
    .tab-content {
        padding: 0 10px;
        .user-info {
            .info-header {
                background: #f5f5f5;
                padding: 10px;
                .avatar {
                    width: 38px;
                    height: 38px;
                    border-radius: 50%;
                }
            }
            .info-item {
                padding: 0 10px;
                font-size: 12px;
                margin-top: 16px;
                .label {
                    width: 60px;
                    text-align: right;
                    color: #666;
                    display: inline-block;
                }
            }
        }
        .order-info {
            .order-list {
                font-size: 12px;
                .order-item {
                    padding: 20px 0;
                    &:not(:last-of-type) {
                        border-bottom: 1px solid #f5f5f5;
                    }
                    .order-status {
                        padding: 2px 9px;
                        border-radius: 12px;
                        background: #ebf1ff;
                        color: #4073fa;
                        &.wait-pay {
                            color: #ff2c3c;
                            background: rgba(255, 44, 60, 0.08);
                        }
                    }
                }
            }
        }
    }
}
</style>