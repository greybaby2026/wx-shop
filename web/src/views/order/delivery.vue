<template>
    <div>
        <div class="ls-card">
            <!-- 顶部提示信息 -->
            <el-alert type="info" show-icon :closable="false">
                <template slot="title">
                    温馨提示：1.批量导入支持运单号批量上传与修改，更新的运单号若与最后上传单号相同则不修改订单，且以最后上传单号作发货物流依据，单次最多导入
                    2000 行，超出行无效。2.暂不支持虚拟商品批量导入发货，不支持部分发货，请在页面手动上传。
                </template>
            </el-alert>
        </div>
        <div class="ls-card m-t-10">
            <div>导入</div>
            <div class="m-t-20">
                <el-form ref="form" label-width="80px" size="small">
                    <el-form-item label="数据文件">
                        <div class="flex">
                            <el-input style="width: 300px" v-model="file">
                                <template slot="append">
                                    <Upload :show-progress="true" :multiple="false" @change="handleUpload">
                                        选择文件
                                    </Upload>
                                </template>
                            </el-input>
                            <div class="m-l-10">
                                <el-button type="primary" @click="handleImport">立即导入</el-button>
                                <el-button type="text" @click="handledown">下载模版文件</el-button>
                                <el-button type="text" @click="handlecomp">下载快递公司</el-button>
                            </div>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-card m-t-10">
            <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                <el-table-column prop="filename" label="文件名称" min-width="100"> </el-table-column>
                <el-table-column prop="nums" label="导入订单数" min-width="100"> </el-table-column>
                <el-table-column prop="create_time" label="发货时间" min-width="100"> </el-table-column>
                <el-table-column prop="" label="成功/失败" min-width="100">
                    <template slot-scope="{ row }"> {{ row.success }}/{{ row.fail }} </template>
                </el-table-column>
                <el-table-column prop="" label="操作" min-width="100">
                    <template slot-scope="{ row }">
                        <el-button type="text" size="small" v-if="row.fail > 0" @click="handlefail(row.id)"
                            >导出失败订单</el-button
                        >
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex row-right m-t-16">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import Upload from './upload.vue'
import { Component, Prop, Vue } from 'vue-property-decorator'
import { apiOrderImport, apiDeliveryBatch, apiDeliveryBatchdown, apiDeliveryBatchfail } from '@/api/order/order'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'

@Component({
    components: {
        Upload,
        LsPagination
    }
})
export default class Delivery extends Vue {
    @Prop({
        default: () => ({})
    })
    config!: any
    file = ''
    id = ''
    /** S data **/
    defaultConfig = {
        setTitle: true,
        setBg: true
    }
    /** E data **/
    handleUpload(file: any, response: any) {
        this.file = file.name
        this.id = response.data.id
    }
    handleImport() {
        if (!this.file) {
            return
        }
        const loading = this.$loading({
            lock: true,
            text: '正在导入中...',
            spinner: 'el-icon-loading'
        })
        apiOrderImport({ id: this.id })
            .then(res => {
                this.getList()
            })
            .finally(() => {
                loading.close()
            })
    }
    handledown() {
        const loading = this.$loading({
            lock: true,
            text: '正在下载中...',
            spinner: 'el-icon-loading'
        })
        apiDeliveryBatchdown({ file: 'delivery_batch_template.xls' })
            .then(res => {
                console.log(res)
            })
            .finally(() => {
                loading.close()
            })
    }
    handlecomp() {
        const loading = this.$loading({
            lock: true,
            text: '正在下载中...',
            spinner: 'el-icon-loading'
        })
        apiDeliveryBatchdown({ file: 'delivery_batch_express_company.xls' })
            .then(res => {
                console.log(res)
            })
            .finally(() => {
                loading.close()
            })
    }
    handlefail(id: number) {
        const loading = this.$loading({
            lock: true,
            text: '正在下载中...',
            spinner: 'el-icon-loading'
        })
        apiDeliveryBatchfail({ id: id }).finally(() => {
            loading.close()
        })
    }
    pager = new RequestPaging()

    apiDeliveryBatch = apiDeliveryBatch
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiDeliveryBatch
        })
    }
    handleReset() {
        this.getList()
    }
    created() {
        this.getList()
    }
    activated() {
        this.getList()
    }
}
</script>
