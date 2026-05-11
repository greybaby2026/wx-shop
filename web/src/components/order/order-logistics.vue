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
            width="60vw"
            :top="top"
            :append-to-body="true"
            center
            :before-close="close"
            :close-on-click-modal="false"
        >
            <div style="height: 70vh; overflow-x: hidden" v-loading="orderData.length == 0">
                <!-- 商品信息 -->
                <div>
                    <div class="nr weight-500 m-b-20">商品信息</div>

                    <el-table :data="orderData.order_goods" ref="paneTable1" size="mini">
                        <el-table-column label="商品信息" min-width="200">
                            <template slot-scope="scope">
                                <div class="flex m-t-10">
                                    <el-image
                                        :src="scope.row.goods_image"
                                        style="width: 58px; height: 58px"
                                        class="flex-none"
                                    ></el-image>
                                    <div class="m-l-8 flex-1">
                                        <div class="line-2">{{ scope.row.goods_name }}</div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="spec_value_str" label="商品规格" min-width="150"></el-table-column>
                        <el-table-column prop="goods_num" label="购买数量" min-width="120"></el-table-column>
                        <el-table-column
                            prop="after_sale_status_desc"
                            label="售后状态"
                            min-width="120"
                        ></el-table-column>
                    </el-table>
                </div>

                <!-- 收货信息 -->
                <div class="m-t-30" v-if="flag == true">
                    <div class="nr weight-500 m-b-20">收货信息</div>

                    <div class="flex">
                        <div class="m-r-24">收货人： {{ orderData.contact }}</div>
                        <div class="m-r-24">收货人手机号码： {{ orderData.mobile }}</div>
                        <div class="m-r-24">收货人地址： {{ orderData.delivery_address }}</div>
                    </div>
                </div>

                <!-- 物流配送 -->
                <div class="m-t-30" v-if="flag == true">
                    <!-- 虚拟订单 -->
                    <template v-if="orderType == 4">
                        <div class="nr weight-500 m-b-20">商品发货</div>
                        <el-form ref="form" :model="form" size="small" label-width="80px">
                            <el-form-item label="发货类型">
                                <el-radio-group v-model="form.delivery_content_type">
                                    <el-radio label="0">固定内容</el-radio>
                                    <el-radio label="1">自定义内容</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="发货内容">
                                <el-input
                                    class="m-t-10"
                                    type="textarea"
                                    style="max-width: 520px; width: 100%"
                                    :rows="7"
                                    placeholder="请输入内容"
                                    v-model="form.delivery_content"
                                    @change="handleChange"
                                    v-if="form.delivery_content_type == 0"
                                ></el-input>
                                <div v-else>
                                    <el-table ref="table" size="mini" :data="form.delivery_content1">
                                        <el-table-column label="名称">
                                            <template #default="scope">
                                                <el-input placeholder="请输入" v-model="scope.row.name"></el-input>
                                            </template>
                                        </el-table-column>
                                        <el-table-column label="内容">
                                            <template #default="scope">
                                                <el-input placeholder="请输入" v-model="scope.row.content"></el-input>
                                            </template>
                                        </el-table-column>
                                        <el-table-column label="操作">
                                            <template #default="scope">
                                                <el-button type="danger" @click="handleDel(scope.$index)"
                                                    >删除</el-button
                                                >
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                    <el-button type="text" @click="handleAdd">添加字段</el-button>
                                </div>
                            </el-form-item>
                        </el-form>
                    </template>
                    <!-- 非虚拟订单 -->
                    <template v-else>
                        <div class="nr weight-500 m-b-20">物流配送</div>
                        <el-form ref="form" :model="form" label-width="80px" size="small">
                            <el-form-item label="配送方式">
                                <el-radio v-model="form.send_type" :label="1">需要物流</el-radio>
                                <el-radio v-model="form.send_type" :label="2">无需物流</el-radio>
                            </el-form-item>

                            <el-form-item label="物流公司" v-if="form.send_type == 1">
                                <el-input
                                    style="max-width: 520px; width: 100%"
                                    placeholder="请输入快递单号"
                                    v-model="form.invoice_no"
                                >
                                    <template slot="prepend">
                                        <div>
                                            <el-select
                                                style="width: 120px"
                                                v-model="form.express_id"
                                                placeholder="请选择"
                                            >
                                                <el-option
                                                    :label="item.name"
                                                    :value="item.id"
                                                    v-for="(item, index) in orderData.express"
                                                    :key="index"
                                                ></el-option>
                                            </el-select>
                                        </div>
                                    </template>
                                </el-input>
                            </el-form-item>

                            <el-form-item label="发货备注">
                                <el-input
                                    class="m-t-10"
                                    style="max-width: 520px; width: 100%"
                                    type="textarea"
                                    :rows="7"
                                    placeholder="请输入内容"
                                    v-model="form.remark"
                                ></el-input>
                            </el-form-item>
                        </el-form>
                    </template>
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
                            <el-table-column label="日期" prop="0" min-width="205"></el-table-column>
                            <el-table-column label="轨迹" prop="1" min-width="405"></el-table-column>
                        </el-table>
                    </div>
                    <div v-else class="nr weight-500 m-t-60 flex row-center">无需物流</div>
                </div>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="handleEvent('cancel')">取消</el-button>
                <el-button
                    size="small"
                    @click="handleEvent('confirm')"
                    v-if="flag == true && express_again == 0"
                    type="primary"
                    >发货</el-button
                >
                <el-button
                    size="small"
                    type="primary"
                    @click="handlesendChange"
                    v-if="flag == true && express_again == 1"
                    >重新发货</el-button
                >
                <el-button
                    size="small"
                    @click="handleEvent('cancel')"
                    v-if="flag == false && express_again == 0"
                    type="primary"
                    >确认</el-button
                >
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiOrderDeliveryInfo, apiOrderLogistics, apiOrderDelivery, apiOrderChangeDelivery } from '@/api/order/order'
@Component
export default class OrderLogistics extends Vue {
    @Prop({ default: '5vh' }) top!: string | number //弹窗的距离顶部位置
    @Prop({ default: '0' }) id!: string | number //订单ID
    @Prop({ default: true }) flag!: Boolean //是发货还是物流查询 true为发货 ，false为物流查询
    @Prop({ default: '' }) isShow!: string
    @Prop() orderType: number | undefined
    @Prop({ default: 0 }) express_again!: number

    /** S Data **/
    visible = false //是否

    fullscreenLoading = false //加载方式

    // 物流订单信息
    orderData: any = {
        traces: {}
    }

    // 发货表单
    form: any = {
        send_type: 1, //	是	int	配送方式:1-快递配送;2-无需快递
        express_id: '', //	是(配送方式为1时必选)	int	订单id
        invoice_no: '', //	是(配送方式为1时必选)	int	订单id
        remark: '', //	否	varchar	发货备注
        delivery_content: '', //发货内容
        delivery_content_type: '0',
        delivery_content1: [],
        pay_way: ''
    }

    /** E Data **/

    /** S Method **/

    // 获取订单信息 flag 为 true的时候执行
    getOrderDeliveryInfo() {
        apiOrderDeliveryInfo({ id: this.id }).then(res => {
            this.orderData = res
            this.form.delivery_content = res.delivery_content
            this.fullscreenLoading = false
        })
    }

    // 获取物流查询
    getOrderLogistics() {
        apiOrderLogistics({ id: this.id }).then(res => {
            this.orderData = res
            this.fullscreenLoading = false
        })
    }
    handleAdd() {
        this.form.delivery_content1.push({ name: '', content: '' })
    }
    handleDel(delval: number) {
        this.form.delivery_content1 = this.form.delivery_content1.filter((i: any, index: number) => {
            return index != delval
        })
    }
    // 发货
    orderDelivery() {
        if (this.orderData.order_type == 4) {
            this.form = {
                delivery_content_type: this.form.delivery_content_type,
                delivery_content: this.form.delivery_content,
                delivery_content1: this.form.delivery_content1
            }
        }
        apiOrderDelivery({
            id: this.id,
            ...this.form
        }).then(res => {
            this.$emit('update', '')

            // this.getOrderLogistics()
        })
    }
    // 点击取消
    handleEvent(type: 'cancel' | 'confirm') {
        if (type === 'cancel') {
            this.close()
        }
        if (type === 'confirm') {
            if (this.hasInAfterSalesGoods()) {
                return
            }
            if (this.flag) {
                // 虚拟发货
                if (this.orderData.order_type == 4) {
                    const confirme = this.form.delivery_content1.some((i: any) => {
                        return i.name == '' || i.content == ''
                    })

                    if (this.form.delivery_content.trim() == '' && confirme) {
                        return this.$message.error('请输入发货内容')
                    }
                } else if (this.form.send_type == 1) {
                    if (this.form.express_id == '') {
                        return this.$message.error('请选择快递公司')
                    }
                    if (this.form.invoice_no == '') {
                        return this.$message.error('请填写快递单号')
                    }
                }
            }

            if (
                this.orderData.pay_way == 2 &&
                this.form.send_type == 1 &&
                this.orderData.order_type != 4 &&
                this.checkExpress()
            ) {
                return
            }

            this.orderDelivery()
            this.close()
        }
    }
    //重新发货
    async handlesendChange() {
        try {
            if (this.form.send_type == 1) {
                if (this.form.express_id == '') {
                    return this.$message.error('请选择快递公司')
                }
                if (this.form.invoice_no == '') {
                    return this.$message.error('请填写快递单号')
                }
                const sendName = this.orderData.express.find((item: any) => {
                    return item.id == this.form.express_id
                })
                await this.$confirm(
                    '仅允许修改一次发货信息，请确认快递信息:' + sendName.name + '-' + this.form.invoice_no
                )
                await apiOrderChangeDelivery({
                    id: this.id,
                    ...this.form
                })
            } else if (this.form.send_type == 2) {
                await this.$confirm('仅允许修改一次发货信息，是否修改为无需快递')
                this.form = {
                    express_id: '',
                    invoice_no: '',
                    delivery_content: this.form.delivery_content
                }
                await apiOrderChangeDelivery({
                    id: this.id,
                    ...this.form
                })
            }
        } catch (error) {
            console.log(error)
        }
        this.close()
        this.$emit('update', '')
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

    handleChange(val: string) {
        this.form.delivery_content = val.trim()
    }
    // @Watch('isShow')
    // changeShow(value: any) {
    //
    // }
    checkExpress() {
        let express = this.orderData.express.find((item: any) => {
            return item.id == this.form.express_id
        })

        if (express.code == '') {
            this.$alert(
                `<div>所选的物流公司未填写：<span style="color: red">快递编码</span>，将会影响同步微信小程序发货信息的录入，是否继续发货？</div>`,
                '温馨提示',
                {
                    dangerouslyUseHTMLString: true,
                    distinguishCancelAndClose: false,
                    showCancelButton: true,
                    cancelButtonText: '继续发货',
                    confirmButtonText: '前往设置',
                    showClose: false
                }
            )
                .then(() => {
                    this.toexpress(express.id)
                })
                .catch(() => {
                    this.orderDelivery()
                    this.close()
                    console.log('failer')
                })
            return true
        }
        return false
    }
    hasInAfterSalesGoods() {
        let inAfterSalesGoods = this.orderData.order_goods.filter(
            (goods: Record<string, any>) => goods.after_sale_status === 1
        )

        if (inAfterSalesGoods.length) {
            this.$alert(
                `<div>商品：<span style="color: red">${inAfterSalesGoods[0].goods_name} </span>当前处于售后中，请前往处理</div>`,
                '温馨提示',
                {
                    dangerouslyUseHTMLString: true,
                    distinguishCancelAndClose: true,
                    showCancelButton: true,
                    confirmButtonText: '前往处理'
                }
            )
                .then(() => {
                    this.toAfterSalesDetail(inAfterSalesGoods[0].after_sale_id)
                })
                .catch(() => {
                    console.log('failer')
                })
            return true
        }
        return false
    }

    // 去售后详情
    toAfterSalesDetail(id: any) {
        this.close()
        this.$router.push({
            path: '/order/after_sales_detail',
            query: { id }
        })
    }
    toexpress(id: any) {
        this.close()
        this.$router.push({
            path: '/setting/delivery/express_edit',
            query: { id }
        })
    }
}
</script>

<style lang="scss">
.header-input {
    width: 220px !important;
}
</style>
