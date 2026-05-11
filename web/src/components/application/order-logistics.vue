<template>
    <div>
        <div class="ls-dialog__trigger" @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            coustom-class="ls-dialog__content"
            :title="flag == true ? '发货' : '物流查询'"
            :visible="visible"
            width="1050px"
            :top="top"
            center
            :modal-append-to-body="true"
            :append-to-body="true"
            :before-close="close"
            :close-on-click-modal="true"
        >
            <div style="height: 50vh; overflow-x: hidden" v-loading="orderData.length == 0" class="p-l-20 p-r-20">
                <!-- 商品信息 -->
                <div>
                    <div class="nr weight-500 m-b-20 normal">商品信息</div>
                    <el-table :data="getOrderGoods" ref="paneTable" style="width: 100%" size="mini">
                        <el-table-column label="商品信息" min-width="150">
                            <template v-slot="{ row }">
                                <div class="flex m-t-10">
                                    <el-image
                                        :src="row.image"
                                        style="width: 58px; height: 58px"
                                        class="flex-none"
                                    ></el-image>
                                    <div class="m-l-8 flex-1">
                                        <div class="line-2">{{ row.name }}</div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="市场价">
                            <template v-slot="{ row }">￥{{ +row.market_price }}</template>
                        </el-table-column>
                        <el-table-column label="兑换积分">
                            <template v-slot="{ row }">
                                <div class="flex">
                                    {{ row.need_integral }}积分
                                    <template v-if="orderData.exchange_way == 2">+ {{ +row.need_money }}元</template>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="数量" prop="goods_num"></el-table-column>
                        <el-table-column label="运费">
                            <template v-slot="{ row }">￥{{ +row.express_price }}</template>
                        </el-table-column>
                        <el-table-column label="实付">
                            <template v-slot="{ row }">
                                <div class="flex">
                                    {{ row.order_integral }}积分
                                    <template v-if="row.order_amount > 0">+ {{ +row.order_amount }}元</template>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>

                <!-- 收货信息 -->
                <div class="m-t-30" v-if="flag == true">
                    <div class="nr weight-500 m-b-20 normal">收货信息</div>

                    <el-form ref="form" size="mini" label-width="120px">
                        <el-form-item label="收货人姓名：">{{ orderData.address.contact }}</el-form-item>
                        <el-form-item label="收货人手机号：">{{ orderData.address.mobile }}</el-form-item>
                        <el-form-item label="收货人地址：">{{ orderData.delivery_address }}</el-form-item>
                    </el-form>
                </div>

                <!-- 物流配送 -->
                <div class="m-t-30" v-if="flag == true">
                    <div class="nr weight-500 m-b-20">物流配送</div>

                    <div class="flex">
                        <el-form ref="form" size="small" :model="form" label-width="80px">
                            <!-- <el-form-item label="发货方式">
                                <el-radio v-model="form.send_type" :label="1">需要物流</el-radio>
                                <el-radio v-model="form.send_type" :label="2">无需物流</el-radio>
                            </el-form-item> -->

                            <el-form-item label="物流公司" v-if="form.send_type == 1">
                                <el-select style="width: 400px" v-model="form.express_id" placeholder="请选择">
                                    <el-option
                                        :label="item.name"
                                        :value="item.id"
                                        v-for="(item, index) in orderData.express"
                                        :key="index"
                                    ></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="快递单号" v-if="form.send_type == 1">
                                <el-input
                                    style="width: 400px"
                                    placeholder="请输入快递单号"
                                    v-model="form.invoice_no"
                                ></el-input>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>

                <!-- 物流信息 -->
                <div class="m-t-30" v-if="flag == false">
                    <div class="nr weight-500 m-b-20">物流信息</div>

                    <div class="flex">
                        <div class="m-r-24">发货时间： {{ orderData.express_time }}</div>
                        <div class="m-r-24">物流公司： {{ orderData.express_name || '无' }}</div>
                        <div class="m-r-24">物流单号 {{ orderData.invoice_no || '无' }}</div>
                    </div>
                </div>

                <!-- 物流轨迹 -->
                <div class="m-t-30" v-if="flag == false">
                    <div class="nr weight-500 m-b-20">物流轨迹</div>

                    <div v-if="orderData.send_type == 1">
                        <el-table :data="orderData.traces" ref="paneTable" style="width: 100%" size="mini">
                            <el-table-column label="日期" :prop="orderData.express_field == 'code100' ? '0' : '2'" min-width="120"></el-table-column>
                            <el-table-column label="轨迹" prop="1" min-width="405"></el-table-column>
                        </el-table>
                    </div>
                    <div v-else class="nr weight-500 m-t-60 flex row-center">无需物流</div>
                </div>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="handleEvent('cancel')">取消</el-button>
                <el-button size="small" @click="handleEvent('confirm')" v-if="flag == true" type="primary"
                    >发货</el-button
                >
                <el-button size="small" @click="handleEvent('cancel')" v-else type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import {
    apiIntegralOrderDeliveryInfo,
    apiIntegralGoodsLogistics,
    apiIntegralGoodsDelivery
} from '@/api/application/integral_mall'
@Component
export default class OrderLogistics extends Vue {
    @Prop({ default: '15vh' }) top!: string | number //弹窗的距离顶部位置
    @Prop({ default: '0' }) id!: string | number //订单ID
    @Prop({ default: true }) flag!: Boolean //是发货还是物流查询 true为发货 ，false为物流查询
    @Prop({ default: '' }) isShow!: string

    /** S Data **/
    visible = false //是否

    fullscreenLoading = false //加载方式

    // 物流订单信息
    orderData: any = {
        traces: {},
        address: {}
    }

    // 发货表单
    form: any = {
        send_type: 1, //	是	int	配送方式:1-快递配送;2-无需快递
        express_id: '', //	是(配送方式为1时必选)	int	订单id
        invoice_no: '' //	是(配送方式为1时必选)	int	订单id
    }

    /** E Data **/

    /** S Method **/

    // 获取订单信息 flag 为 true的时候执行
    getOrderDeliveryInfo() {
        apiIntegralOrderDeliveryInfo({ id: this.id }).then(res => {
            this.orderData = res
            this.fullscreenLoading = false
        })
    }

    // 获取物流查询
    getOrderLogistics() {
        apiIntegralGoodsLogistics({ id: this.id }).then(res => {
            this.orderData = res
            this.fullscreenLoading = false
        })
    }

    // 发货
    orderDelivery() {
        apiIntegralGoodsDelivery({
            id: this.id,
            ...this.form
        }).then(res => {
            this.$emit('update', '')

            this.getOrderLogistics()
        })
    }

    // 点击取消
    handleEvent(type: 'cancel' | 'confirm') {
        if (type === 'cancel') {
            this.close()
        }
        if (type === 'confirm') {
            if (this.flag) {
                if (this.form.send_type == 1) {
                    if (this.form.express_id == '') {
                        return this.$message.error('请选择快递公司')
                    }
                    if (this.form.invoice_no == '') {
                        return this.$message.error('请填写快递单号')
                    }
                }
            }

            this.orderDelivery()
            this.close()
        }
    }

    // 打开弹窗
    onTrigger() {
        this.fullscreenLoading = true
        this.flag == true ? this.getOrderDeliveryInfo() : this.getOrderLogistics()
        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false
    }

    // 获取商品表格信息
    get getOrderGoods() {
        const { order_goods } = this.orderData
        let goods: any[] = []
        if (order_goods) {
            goods = [order_goods]
        }
        return goods
    }
}
</script>

<style lang="scss">
.header-input {
    width: 220px !important;
}
</style>
