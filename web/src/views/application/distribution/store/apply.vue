<template>
    <div>
        <div class="ls-card">
            <el-alert
                title="温馨提示：1.用户申请审核通过即可成为分销会员；审核拒绝后可再次发起申请。"
                type="info"
                show-icon
                :closable="false"
            />

            <div class="m-t-16">
                <el-form ref="queryObj" inline :model="queryObj" label-width="100px" size="small">
                    <el-form-item label="用户信息">
                        <el-input
                            style="width: 280px"
                            v-model="queryObj.user_info"
                            placeholder="请输入用户昵称/用户编号"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="申请时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="resetQueryObj">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiDistributionApplyLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <el-tabs v-model="activeName" v-loading="pager.loading" @tab-click="getList()">
                <el-tab-pane
                    v-for="(item, index) in tabs"
                    :key="index"
                    :label="`${item.label}(${tabCount[item.name]})`"
                    :name="item.name"
                >
                    <el-table ref="paneTable" :data="pager.lists" style="width: 100%" size="mini">
                        <el-table-column prop="name" label="用户昵称" min-width="180">
                            <template slot-scope="scope">
                                <div class="flex pointer" @click="toUser(scope.row.user_id)">
                                    <el-image
                                        class="flex-none"
                                        style="width: 58px; height: 58px"
                                        :src="scope.row.avatar"
                                    />
                                    <div class="goods-info m-l-8">
                                        <div class="line-2">{{ scope.row.nickname }}({{ scope.row.sn }})</div>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="real_name" label="真实姓名" min-width="140"></el-table-column>
                        <el-table-column prop="mobile" label="联系手机" min-width="140"></el-table-column>
                        <el-table-column prop="address" label="所属区域" min-width="140"></el-table-column>
                        <el-table-column prop="reason" label="申请原因" min-width="140"></el-table-column>
                        <el-table-column prop="status_desc" label="申请状态" min-width="140"></el-table-column>
                        <el-table-column prop="audit_remark" label="审核说明" min-width="140"></el-table-column>
                        <el-table-column prop="create_time" label="申请时间" min-width="140"></el-table-column>
                        <el-table-column fixed="right" label="操作" min-width="140">
                            <template slot-scope="scope">
                                <el-button
                                    slot="trigger"
                                    class="m-r-10"
                                    type="text"
                                    size="small"
                                    @click="
                                        $router.push({
                                            path: '/distribution/distribution_apply_details',
                                            query: { id: scope.row.id }
                                        })
                                    "
                                    >详情</el-button
                                >

                                <ls-dialog
                                    v-if="scope.row.status_desc == '待审核'"
                                    class="inline m-l-24"
                                    @confirm="confirm(scope.row.id)"
                                >
                                    <el-button slot="trigger" type="text" size="small">审核</el-button>

                                    <el-form :inline="true" label-width="80px" :model="examine">
                                        <el-form-item label="状态">
                                            <el-radio-group v-model="examine.flag">
                                                <el-radio :label="1">通过</el-radio>
                                                <el-radio :label="2">拒绝</el-radio>
                                            </el-radio-group>
                                        </el-form-item>

                                        <el-form-item label="名称">
                                            <el-input
                                                type="textarea"
                                                placeholder="请输入内容"
                                                v-model="examine.audit_remark"
                                                maxlength="30"
                                                show-word-limit
                                            />
                                        </el-form-item>
                                    </el-form>
                                </ls-dialog>
                            </template>
                        </el-table-column>
                    </el-table>

                    <div class="m-t-24 flex row-right">
                        <ls-pagination v-model="pager" @change="getList()"></ls-pagination>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
// 审核以及详情未完成

import { Component, Vue, Watch } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import { disType } from '@/utils/type'
import lsDialog from '@/components/ls-dialog.vue'
import lsPagination from '@/components/ls-pagination.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
import {
    apiDistributionApplyLists,
    apiDistributionApplyRefuse,
    apiDistributionApplyPass
} from '@/api/distribution/distribution'

@Component({
    components: {
        lsDialog,
        lsPagination,
        DatePicker,
        ExportData
    }
})
export default class DistributionApply extends Vue {
    apiDistributionApplyLists = apiDistributionApplyLists

    tabs = [
        {
            label: '全部',
            name: disType[0]
        },
        {
            label: '待审核',
            name: disType[1]
        },
        {
            label: '审核通过',
            name: disType[2]
        },
        {
            label: '审核拒绝',
            name: disType[3]
        }
    ]

    queryObj = {
        user_info: '',
        end_time: '',
        start_time: ''
    }

    examine = {
        flag: 1,
        audit_remark: ''
    }

    lists = []

    tabCount = {
        all: 0, //全部
        wait: 0, //待审核
        pass: 0, //审核通过
        refuse: 0 //已拒绝
    }

    pager = new RequestPaging()
    activeName: any = 'all'

    confirm(id: number) {
        if (this.examine.flag == 1) {
            apiDistributionApplyPass({
                id,
                audit_remark: this.examine.audit_remark
            }).then(() => {
                this.getList()
            })
        } else {
            apiDistributionApplyRefuse({
                id,
                audit_remark: this.examine.audit_remark
            }).then(() => {
                this.getList()
            })
        }
    }

    // 获取申请的列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        const status: any = disType[this.activeName] == '0' ? '' : (disType[this.activeName] as any) - 1
        this.pager
            .request({
                callback: apiDistributionApplyLists,
                params: {
                    status: status,
                    ...this.queryObj
                }
            })
            .then(res => {
                this.tabCount = res?.extend
            })
    }

    // 重置
    resetQueryObj() {
        Object.keys(this.queryObj).map(key => {
            this.$set(this.queryObj, key, '')
        })
        this.getList()
    }

    // 去用户详情
    toUser(id: any) {
        this.$router.push({
            path: '/user/user_details',
            query: { id: id }
        })
    }

    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.pointer:hover {
    color: $--color-primary;
}
</style>
