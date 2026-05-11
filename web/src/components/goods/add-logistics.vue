<template>
    <div class="add-logistics">
        <template v-if="value.type == 1">
            <el-form-item label="物流支持" required>
                <el-checkbox v-model="value.is_express" :false-label="0" :true-label="1">快递发货</el-checkbox>
                <el-checkbox v-model="value.is_selffetch" :false-label="0" :true-label="1">上门自提</el-checkbox>
            </el-form-item>
            <el-form-item label="运费设置" required>
                <el-radio-group v-model="value.express_type">
                    <el-radio :label="1">包邮</el-radio>
                    <el-radio :label="2">统一运费</el-radio>
                    <el-radio :label="3">运费模板</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item v-if="value.express_type == 2" prop="express_money">
                <el-input v-model="value.express_money">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>
            <el-form-item v-if="value.express_type == 3">
                <el-select v-model="value.express_template_id" placeholder="请选择运费模板" prop="express_template_id">
                    <el-option
                        v-for="item in freightList"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    ></el-option>
                </el-select>
            </el-form-item>
        </template>
        <template v-else-if="value.type == 2">
            <el-form-item label="收货地址" required>
                <el-radio-group v-model="value.is_address">
                    <el-radio :label="1">开启</el-radio>
                    <el-radio :label="0">关闭</el-radio>
                </el-radio-group>
                <div class="muted">默认关闭，关闭之后，虚拟商品下单时，结算页将不显示收货地址</div>
            </el-form-item>
            <el-form-item label="配送方式" required>
                <el-checkbox v-model="value.is_virtualdelivery" :false-label="0" :true-label="1">虚拟发货</el-checkbox>
            </el-form-item>
            <el-form-item label="买家付款后" required>
                <el-radio-group v-model="value.after_pay">
                    <el-radio :label="1">自动发货</el-radio>
                    <el-radio :label="2">手动发货</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="发货类型" required prop="delivery_type" v-if="value.after_pay == 1">
                <el-radio-group v-model="value.delivery_type">
                    <el-radio :label="0">固定内容</el-radio>
                    <el-radio :label="1">发货模版</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="发货模版" v-if="value.delivery_type == 1 && value.after_pay == 1 && Reflash">
                <div class="flex">
                    <el-select v-model="value.delivery_template_id" placeholder="请选择品牌" class="m-r-10">
                        <el-option
                            v-for="(item, index) in templateLists"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        >
                            <div class="flex row-between">
                                <span>{{ item.name }}</span>
                                <div>
                                    <ls-dialog
                                        class="inline"
                                        title="编辑模版"
                                        width="800px"
                                        top="20vh"
                                        @confirm="handleConfirmtem(index)"
                                        @open="handleEditopen(item.id)"
                                        :async="true"
                                        ref="dialogtem"
                                        ><i class="el-icon-edit m-r-10" slot="trigger"></i>
                                        <div>
                                            <el-form ref="form" size="mini" label-width="120px">
                                                <el-form-item label="模版名称">
                                                    <el-input
                                                        placeholder="请输入模版名称"
                                                        v-model="templateEdit.name"
                                                    ></el-input>
                                                </el-form-item>
                                                <el-form-item label="发货类型">
                                                    <el-radio-group v-model="templateEdit.type">
                                                        <el-radio :label="0">固定内容</el-radio>
                                                        <el-radio :label="1">自定义内容</el-radio>
                                                    </el-radio-group>
                                                </el-form-item>
                                                <el-form-item label="发货内容">
                                                    <el-input
                                                        placeholder="请输入自动发货内容"
                                                        v-model="templateEdit.content"
                                                        v-if="templateEdit.type == 0"
                                                    ></el-input>
                                                    <div v-else>
                                                        <el-table ref="table" size="mini" :data="templateEdit.content1">
                                                            <el-table-column label="名称">
                                                                <template #default="scope">
                                                                    <el-input
                                                                        placeholder="请输入"
                                                                        v-model="scope.row.name"
                                                                    ></el-input>
                                                                </template>
                                                            </el-table-column>
                                                            <el-table-column label="内容">
                                                                <template #default="scope">
                                                                    <el-input
                                                                        placeholder="请输入"
                                                                        v-model="scope.row.content"
                                                                    ></el-input>
                                                                </template>
                                                            </el-table-column>
                                                            <el-table-column label="操作">
                                                                <template #default="scope">
                                                                    <el-button
                                                                        type="danger"
                                                                        @click="handleEditdel(scope.$index)"
                                                                        >删除</el-button
                                                                    >
                                                                </template>
                                                            </el-table-column>
                                                        </el-table>
                                                        <el-button type="text" @click="handleEditadd"
                                                            >添加字段</el-button
                                                        >
                                                    </div>
                                                </el-form-item>
                                            </el-form>
                                        </div>
                                    </ls-dialog>

                                    <ls-dialog
                                        class="inline"
                                        :content="`确定删除：${item.name}？请谨慎操作。`"
                                        @confirm="handleTemdel(item.id)"
                                        ><i class="el-icon-delete" slot="trigger"></i
                                    ></ls-dialog>
                                </div>
                            </div>
                        </el-option>
                    </el-select>

                    <ls-dialog
                        title="新增模版"
                        width="800px"
                        top="20vh"
                        @confirm="handleConfirm"
                        :async="true"
                        ref="dialog"
                    >
                        <el-button slot="trigger" type="text">新增发货模版</el-button>
                        <div>
                            <el-form ref="form" size="mini" label-width="120px">
                                <el-form-item label="模版名称">
                                    <el-input placeholder="请输入模版名称" v-model="templateForm.name"></el-input>
                                </el-form-item>
                                <el-form-item label="发货类型">
                                    <el-radio-group v-model="templateForm.type">
                                        <el-radio :label="0">固定内容</el-radio>
                                        <el-radio :label="1">自定义内容</el-radio>
                                    </el-radio-group>
                                </el-form-item>
                                <el-form-item label="发货内容">
                                    <el-input
                                        placeholder="请输入自动发货内容"
                                        v-model="templateForm.content"
                                        v-if="templateForm.type == 0"
                                    ></el-input>
                                    <div v-else>
                                        <el-table ref="table" size="mini" :data="templateForm.content1">
                                            <el-table-column label="名称">
                                                <template #default="scope">
                                                    <el-input placeholder="请输入" v-model="scope.row.name"></el-input>
                                                </template>
                                            </el-table-column>
                                            <el-table-column label="内容">
                                                <template #default="scope">
                                                    <el-input
                                                        placeholder="请输入"
                                                        v-model="scope.row.content"
                                                    ></el-input>
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
                        </div>
                    </ls-dialog>
                    <el-button slot="trigger" type="text" style="margin-left: 10px" @click="handleReflash"
                        >刷新</el-button
                    >
                </div>
            </el-form-item>

            <el-form-item label="发货内容" v-if="value.delivery_type == 0 && value.after_pay == 1">
                <!-- prop="delivery_content" -->

                <el-input
                    v-model="value.delivery_content"
                    type="textarea"
                    style="width: 460px"
                    rows="8"
                    show-word-limit
                    placeholder="请输入发货内容"
                    @change="handleChange"
                ></el-input>
            </el-form-item>
            <el-form-item label="发货后" required>
                <el-radio-group v-model="value.after_delivery">
                    <el-radio :label="1">自动完成订单</el-radio>
                    <el-radio :label="2">需要买家确认收货</el-radio>
                </el-radio-group>
            </el-form-item>
        </template>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import {
    addGoodsDeliveryTemplate,
    GoodsDeliveryTemplate,
    editGoodsDeliveryTemplate,
    delGoodsDeliveryTemplate
} from '@/api/goods'
import { Message } from 'element-ui'

@Component({
    components: {
        LsDialog
    }
})
export default class AddLogistics extends Vue {
    $refs!: { dialog: any; dialogtem: any }

    @Prop() value: any
    @Prop({ default: () => ({}) }) lists: any
    templateForm: any = {
        name: '',
        type: 0,
        content: '',
        content1: []
    }
    templateEdit: any = {
        name: '',
        type: 0,
        content: '',
        content1: []
    }
    templateLists: any = []
    Reflash = true
    get freightList() {
        return this.lists.freight_list || []
    }

    handleChange(val: string) {
        this.value.delivery_content = val.trim()
    }
    handleAdd() {
        this.templateForm.content1.push({ name: '', content: '' })
    }
    handleDel(delval: number) {
        this.templateForm.content1 = this.templateForm.content1.filter((i: any, index: number) => {
            return index != delval
        })
    }
    handleEditadd() {
        this.templateEdit.content1.push({ name: '', content: '' })
    }
    handleEditdel(delval: number) {
        this.templateEdit.content1 = this.templateEdit.content1.filter((i: any, index: number) => {
            return index != delval
        })
    }
    handleConfirm() {
        const confirme = this.templateForm.content1.some((i: any) => {
            return i.name == '' || i.content == ''
        })
        if (confirme) {
            return Message({ type: 'error', message: '请完善发货内容' })
        }
        addGoodsDeliveryTemplate(this.templateForm).then(res => {
            this.templateForm = {
                name: '',
                type: 0,
                content: '',
                content1: []
            }
            this.$refs.dialog.close()
        })
    }
    created() {
        GoodsDeliveryTemplate().then(res => {
            this.templateLists = res.lists
        })
    }
    handleReflash() {
        this.Reflash = false
        GoodsDeliveryTemplate().then(res => {
            this.templateLists = res.lists
            this.Reflash = true
        })
    }
    handleTemdel(id: number) {
        delGoodsDeliveryTemplate({ id }).then(res => {})
    }
    handleEditopen(id: number) {
        this.templateEdit = this.templateLists.find((item: any) => {
            return item.id == id
        })
    }
    handleConfirmtem(index: number) {
        const confirme = this.templateEdit.content1.some((i: any) => {
            return i.name == '' || i.content == ''
        })
        if (confirme) {
            return Message({ type: 'error', message: '请完善发货内容' })
        }
        editGoodsDeliveryTemplate(this.templateEdit)
        this.$refs.dialogtem[index].close()
    }
}
</script>
