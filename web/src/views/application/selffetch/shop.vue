<template>
    <div class="admin">
        <div class="ls-card">
            <!-- 头部表单 -->
            <div class="m-t-20">
                <el-form :inline="true" :model="form" size="small">
                    <!-- 自提门店 -->
                    <el-form-item label="门店名称">
                        <el-input v-model="form.name" placeholder="请输入门店名称" />
                    </el-form-item>

                    <!-- 门店状态 -->
                    <el-form-item label="门店状态" class="m-l-24">
                        <el-select v-model="form.status" placeholder="请选择状态">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="启用" :value="1"></el-option>
                            <el-option label="停用" :value="0"></el-option>
                        </el-select>
                    </el-form-item>

                    <!-- 搜索查询 -->
                    <el-form-item class="m-l-24">
                        <el-button type="primary" @click="search">查询</el-button>
                        <el-button @click="resetSearch">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :method="apiSelffetchShopList"
                            :param="form"
                            :pageSize="pager._size"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <!-- 添加自提门店 -->
            <el-button type="primary" size="small" @click="addSelffetchShop">新增自提门店</el-button>

            <!-- 管理员数据列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <el-table-column prop="name" label="门店LOGO" min-width="80">
                        <div class="flex" slot-scope="scope">
                            <el-image :src="scope.row.image" style="width: 40px; height: 40px" fit="fill" />
                            <!-- <span class="m-l-10">{{ scope.row.name }}</span> -->
                        </div>
                    </el-table-column>
                    <el-table-column prop="name" label="门店名称" min-width="120" />
                    <el-table-column prop="contact" label="店长" min-width="100" />
                    <el-table-column prop="mobile" label="联系电话" min-width="120" />
                    <el-table-column prop="detailed_address" label="门店地址" min-width="220" />
                    <el-table-column prop="create_time" label="营业时间" width="180">
                        <template slot-scope="scope">
                            {{ scope.row.business_start_time + '-' + scope.row.business_end_time }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="门店状态" min-width="80">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                @change="changeSwitchStatus($event, scope.row)"
                            />
                        </template>
                    </el-table-column>
                    <!-- <el-table-column
                        sortable
                        prop="create_time"
                        label="创建时间"
                        width="180"
                    /> -->
                    <el-table-column label="操作" min-width="200">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="goSelffetchShopEdit(scope.row)">编辑</el-button>
                            <ls-dialog class="m-l-10 inline" @confirm="onSelffetchShopDelete(scope.row)">
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 pagination">
                    <ls-pagination v-model="pager" @change="getSelffetchShopList" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiSelffetchShopList, apiSelffetchShopStatus, apiSelffetchShopDel } from '@/api/application/selffetch'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class SelffetchShop extends Vue {
    /** S Data **/
    apiSelffetchShopList = apiSelffetchShopList
    // 表单数据
    form = {
        name: '', //名称
        status: '' //角色id
    }
    pager: RequestPaging = new RequestPaging()
    /** E Data **/

    /** S Methods **/
    // 搜索
    search() {
        this.pager.page = 1
        this.getSelffetchShopList()
    }

    // 重置搜索
    resetSearch() {
        Object.keys(this.form).map(key => {
            this.$set(this.form, key, '')
        })
        this.getSelffetchShopList()
    }

    // 获取列表数据
    getSelffetchShopList() {
        // 请求管理员列表
        this.pager
            .request({
                callback: apiSelffetchShopList,
                params: this.form
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 添加
    addSelffetchShop() {
        this.$router.push({
            path: '/selffetch/selffetch_shop_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 删除
    onSelffetchShopDelete(data: any) {
        apiSelffetchShopDel({ id: data.id }).then(() => {
            // 删除成功就请求新列表
            this.getSelffetchShopList()
        })
    }

    // 编辑
    goSelffetchShopEdit(data: any) {
        this.$router.push({
            path: '/selffetch/selffetch_shop_edit',
            query: {
                mode: PageMode.EDIT,
                id: data.id
            }
        })
    }

    // 更改状态
    changeSwitchStatus(value: 0 | 1, data: any) {
        apiSelffetchShopStatus({
            id: data.id,
            status: value
        }).catch(err => {
            this.getSelffetchShopList()
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getSelffetchShopList()
    }

    /** E Life Cycle **/
}
</script>
s

<style lang="scss" scoped>
.pagination {
    padding-right: 5%;
    display: flex;
    justify-content: flex-end;
}
</style>
