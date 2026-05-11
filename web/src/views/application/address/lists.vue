<template>
    <div class="address_lists">
        <div class="ls-card">
            <el-alert
                title="温馨提示：用来保存自己的发货、退货地址，您最多可添加50条地址。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="ls-card m-t-24">
            <div class="add-btn">
                <el-button type="primary" size="mini" @click="handleAdd">新增地址 </el-button>
            </div>
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" size="mini" style="width: 100%">
                    <el-table-column label="发货地址" min-width="70">
                        <template slot-scope="scope">
                            <div @click.prevent="handleDefault(scope.row, '1')">
                                <el-radio v-model="scope.row.is_deliver_default == 1" :label="true">默认</el-radio>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="退货地址" min-width="70">
                        <template slot-scope="scope">
                            <div @click.prevent="handleDefault(scope.row, '2')">
                                <el-radio v-model="scope.row.is_return_default == 1" :label="true">默认</el-radio>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="联系人" min-width="70" prop="contact"> </el-table-column>
                    <el-table-column label="所在地区" min-width="70">
                        <template slot-scope="scope">
                            {{ scope.row.province }} {{ scope.row.city }}{{ scope.row.district }}
                        </template>
                    </el-table-column>
                    <el-table-column label="详细地址" min-width="70" prop="address"> </el-table-column>
                    <el-table-column label="电话号码" min-width="70">
                        <template slot-scope="scope">
                            <span v-if="scope.row.phone_code && scope.row.phone_extension && scope.row.phone_number"
                                >{{ scope.row.phone_code }}-{{ scope.row.phone_extension }}-{{
                                    scope.row.phone_number
                                }}</span
                            >
                        </template>
                    </el-table-column>
                    <el-table-column label="手机号码" min-width="70" prop="mobile"> </el-table-column>
                    <el-table-column label="操作">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <el-button type="text" size="mini" @click="handleAdd(scope.row)">编辑 </el-button>

                            <ls-dialog title="删除地址" class="m-l-10 inline" @confirm="handleDel(scope.row)">
                                <el-button type="text" size="mini" slot="trigger">删除 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 flex row-right">
                    <ls-pagination v-model="pager" @change="getList" />
                </div>
            </div>
        </div>
        <addDislog ref="lsDislog" @reflsh="reflsh" v-if="addDislogshow" @colse="handlecolse"></addDislog>
    </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
import { apiaddressLists, apiaddressDefault, apiaddressDel } from '@/api/application/address'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'
import addDislog from './addDislog.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData,
        addDislog
    }
})
export default class ArticleLists extends Vue {
    $refs!: {
        lsDislog: any
    }
    /** S Data **/
    value = true
    // 分页
    pager: RequestPaging = new RequestPaging()
    addDislogshow: Boolean = false
    /** E Data **/

    /** S Methods **/
    handleAdd(row?: any) {
        this.addDislogshow = true
        this.$nextTick(() => {
            this.$refs.lsDislog.openDialog(row.id)
        })
    }
    reflsh() {
        setTimeout(() => {
            this.getList()
        }, 100)
    }
    handlecolse() {
        this.addDislogshow = false

        setTimeout(() => {
            this.getList()
        }, 100)
    }

    // 获取列表
    getList() {
        this.pager
            .request({
                callback: apiaddressLists,
                params: {}
            })
            .catch((err: any) => {})
    }

    handleDefault(row: any, default_type: string) {
        if (default_type == '1') {
            if (row.is_deliver_default == 1) {
                return
            }
            apiaddressDefault({
                id: row.id,
                default_type,
                is_default: 1
                // row.is_deliver_default ? 0 : 1
            }).then(() => {
                this.getList()
            })
        } else {
            if (row.is_return_default == 1) {
                return
            }
            apiaddressDefault({
                id: row.id,
                default_type,
                is_default: 1
            }).then(() => {
                this.getList()
            })
        }
    }

    handleDel(row: any) {
        apiaddressDel({
            id: row.id
        }).then(() => {
            this.getList()
        })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getList()
    }

    /** E Life Cycle **/
}
</script>
