<template>
    <div>
        <div class="ls-card">
            <el-alert
                class="xxl"
                type="info"
                :closable="false"
                show-icon
                title="温馨提示：目前商品采集只支持淘宝、天猫、京东、1688的商品"
            >
            </el-alert>
            <el-form class="m-t-16" ref="form" :model="formData" :rules="formRules" label-width="120px" size="small">
                <el-form-item label="采集商品链接" prop="gather_url">
                    <el-input
                        type="textarea"
                        style="width: 550px"
                        v-model="formData.gather_url"
                        :rows="8"
                        placeholder="请输入淘宝、天猫、京东、1688的采集商品链接"
                    />
                    <div class="muted">
                        批量采集请按回车键换行，如果您已配置99Api key可直接开始采集，如果未配置请
                        <el-button type="text" @click="toApiConfig">前往配置</el-button>
                    </div>
                </el-form-item>
                <el-form-item label="采集商品类型">
                    <el-radio-group v-model="formData.goods_type">
                        <el-radio :label="1">实物商品</el-radio>
                        <el-radio :label="2">虚拟商品</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="发货内容" prop="delivery_content" v-if="formData.goods_type === 2">
                    <el-input
                        type="textarea"
                        style="width: 260px"
                        v-model="formData.delivery_content"
                        :rows="3"
                        placeholder="请输入发货内容"
                    />
                </el-form-item>
                <el-form-item label="采集商品分类" prop="goods_category">
                    <el-cascader
                        v-model="formData.goods_category"
                        style="width: 280px"
                        :options="categoryList"
                        :props="{
                            checkStrictly: true,
                            label: 'name',
                            value: 'id',
                            children: 'sons',
                            emitPath: false
                        }"
                        clearable
                        filterable
                    ></el-cascader>
                    <router-link target="_blank" to="/goods/category_edit" class="m-l-10">
                        <el-button type="text" size="small">新增分类</el-button>
                    </router-link>
                    <span class="primary m-8">|</span>
                    <el-button type="text" size="small" @click="getCategoryList">刷新</el-button>
                </el-form-item>

                <el-form-item>
                    <el-button size="small" type="primary" @click.stop="handleGather">采集</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div class="ls-card m-t-16">
            <div class="ls-content__table">
                <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                    <el-table-column prop="goods_type_desc" label="采集商品类型" min-width="170"> </el-table-column>
                    <el-table-column prop="goods_category_desc" label="采集商品分类" min-width="200"> </el-table-column>
                    <el-table-column prop="gather_num" label="采集数" min-width="150"> </el-table-column>
                    <el-table-column prop="gather_fail_num" label="失败数" min-width="150">
                        <template slot-scope="scope">
                            <div :style="{ color: '#ff3333' }">{{ scope.row.gather_fail_num }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="gather_success_num" label="成功数" min-width="150"> </el-table-column>
                    <el-table-column prop="create_time" label="采集时间" min-width="170"> </el-table-column>

                    <el-table-column fixed="right" label="操作" min-width="140">
                        <template slot-scope="scope">
                            <el-button
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/goods_collection/details',
                                        query: { id: scope.row.id }
                                    })
                                "
                                >查看</el-button
                            >
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除该条采集商品？请谨慎操作。`"
                                @confirm="handleDelete(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="flex row-right m-t-20">
                    <ls-pagination v-model="pager" @change="getLogLists()" />
                </div>
            </div>
        </div>
        <ApiConfig ref="apiConfig" @getGatherKey="getGatherKey" />
        <IsLoading ref="isLoadingRef" title="采集" />
    </div>
</template>

<script lang="ts">
import { apiCategoryLists } from '@/api/goods'
import {
    apiGoodsGatherLogLists,
    apiGoodsGather,
    apiGetGatherKey,
    apiDelGather
} from '@/api/application/goods_collection'
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import ApiConfig from './components/api-config.vue'
import IsLoading from './components/is-loading.vue'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        ApiConfig,
        IsLoading,
        LsDialog,
        LsPagination
    }
})
export default class GoodsCollection extends Vue {
    $refs!: { apiConfig: any; isLoadingRef: any; form: any }
    formData = {
        goods_type: 1,
        goods_category: '',
        gather_url: '',
        delivery_content: ''
    }
    key_99api = ''
    formRules = {
        goods_category: [
            {
                required: true,
                message: '请添加采集商品分类',
                trigger: ['blur', 'change']
            }
        ],
        gather_url: [
            {
                required: true,
                message: '请添加采集商品链接',
                trigger: ['blur']
            }
        ],
        delivery_content: [
            {
                required: true,
                message: '请输入发货内容',
                trigger: ['blur']
            }
        ]
    }
    categoryList = []
    pager = new RequestPaging()
    checking = true

    // 获取商品分类列表
    async getCategoryList() {
        const data = await apiCategoryLists({
            page_type: 1
        })
        this.categoryList = data.lists
    }
    // 打开配置弹窗
    toApiConfig() {
        this.$refs.apiConfig.openDialog(this.key_99api)
    }
    // 获取采集记录列表
    getLogLists(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiGoodsGatherLogLists,
                params: {}
            })
            .then((res: any) => {})
    }
    // 获取采集配置key
    async getGatherKey() {
        try {
            const data = await apiGetGatherKey('')
            this.key_99api = data.key_99api
        } catch (error) {
            throw new Error('error')
        }
    }
    // 删除
    handleDelete(id: number) {
        apiDelGather({ log_id: id }).then(() => {
            this.getLogLists()
            this.$message.success('操作成功')
        })
    }
    // 采集
    handleGather() {
        this.$refs.form.validate(async (valid: boolean, object: any) => {
            // 批量添加商品链接
            let arr = this.formData.gather_url.split(/[(\r\n\s)\r\n\s]+/) // \n代表换行，\r代表回车，可以加个\s代表空格
            arr.forEach((item, idx) => {
                // 删除空项
                if (!item) {
                    arr.splice(idx, 1)
                }
            })
            arr = Array.from(new Set(arr)) // 去重
            // 检查是否配置apiKey
            if (!this.key_99api) {
                return this.$message.error('99Apikey还未配置请前往配置')
            }
            if (valid) {
                this.$refs.isLoadingRef.openDialog()
                try {
                    await apiGoodsGather({
                        ...this.formData,
                        gather_url: arr
                    })
                    this.$message.success('采集完成')
                    const obj = {
                        goods_category: '',
                        gather_url: ''
                    }
                    Object.assign(this.formData, obj)
                } catch (error) {
                    console.log('error', error)
                }
                this.$refs.isLoadingRef.closeDialog()
                this.getLogLists()
            } else {
                return false
            }
        })
    }
    created() {
        this.getCategoryList()
        this.getLogLists()
        this.getGatherKey()
    }
}
</script>

<style lang="scss" scoped></style>
