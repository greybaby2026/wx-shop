<!-- 拼团记录 -->
<template>
    <div class="user-withdrawal">
        <div class="ls-card">
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="form" label-width="70px" size="small" class="ls-form">
                    <el-form-item label="活动名称">
                        <el-input v-model="form.name" placeholder=""> </el-input>
                    </el-form-item>
                    <el-form-item label="商品信息">
                        <el-input v-model="form.goods_info" placeholder="请输入商品名称/查询编号"> </el-input>
                    </el-form-item>
                    <el-form-item label="发起用户">
                        <el-input v-model="form.user_info" placeholder="请输入用户名称/用户编号"> </el-input>
                    </el-form-item>
                    <el-form-item label="拼团状态">
                        <el-select v-model="form.status" placeholder="拼团状态" style="width: 120px">
                            <el-option label="全部" value=" "></el-option>
                            <el-option label="拼团中" value="0"></el-option>
                            <el-option label="拼团成功" value="1"></el-option>
                            <el-option label="拼团失败" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="发起时间">
                        <el-date-picker
                            v-model="tableData"
                            type="datetimerange"
                            align="right"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始时间"
                            end-placeholder="结束时间"
                            :picker-options="pickerOptions"
                            @change="splitTime"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        >
                        </el-date-picker>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                    <!-- 导出按钮 -->
                    <export-data
                        class="m-l-10"
                        :method="apiTeamRecord"
                        :param="form"
                        :pageSize="pager.size"
                    ></export-data>
                </el-form>
            </div>
        </div>
        <!-- 提现记录表 -->
        <div class="ls-withdrawal__centent ls-card m-t-16">
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    v-loading="pager.loading"
                    size="mini"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <el-table-column prop="found_sn" label="记录编号"> </el-table-column>
                    <el-table-column prop="name" label="活动名称"> </el-table-column>
                    <el-table-column prop="" label="团长信息">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                <div class="m-l-10">
                                    {{ scope.row.nickname }}
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="" label="拼团商品">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image
                                    class="flex-none"
                                    :src="scope.row.goods_image"
                                    style="width: 34px; height: 34px"
                                ></el-image>
                                <div class="m-l-10">
                                    {{ scope.row.goods_name }}
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="people" label="成团人数"> </el-table-column>
                    <el-table-column prop="join" label="参团人数"> </el-table-column>
                    <el-table-column prop="status_text" label="拼团状态">
                        <!-- <template slot-scope="scope">
						    <el-tag size="medium" type="success" v-if="scope.row.status == 1">{{scope.row.status_desc}}</el-tag>
						    <el-tag size="medium" type="success" v-if="scope.row.status == 2">{{scope.row.status_desc}}</el-tag>
						    <el-tag size="medium" type="info" v-if="scope.row.status == 3">{{scope.row.status_desc}}</el-tag>
						</template> -->
                    </el-table-column>
                    <el-table-column prop="kaituan_time" label="开团时间"> </el-table-column>
                    <el-table-column prop="" label="操作">
                        <template slot-scope="scope">
                            <ls-dialog
                                v-if="scope.row.status == 0"
                                class="inline"
                                :title="`确定结束拼团：${scope.row.found_sn}`"
                                content="结束拼团会设置拼团失败，系统自动关闭未支付订单，请谨慎处理。"
                                @confirm="onStop(scope.row.id)"
                            >
                                <el-button type="text" slot="trigger" size="small">结束拼团 </el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiTeamRecord, apiTeamCancel } from '@/api/marketing/combination'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsPagination,
        ExportData,
        LsDialog
    }
})
export default class AccountLog extends Vue {
    /** S Data **/
    // 日期选择器
    pickerOptions = {
        shortcuts: [
            {
                text: '最近一周',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近一个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近三个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
                    picker.$emit('pick', [start, end])
                }
            }
        ]
    }
    tableData = []
    // 顶部查询表单
    pager: RequestPaging = new RequestPaging()
    // 顶部查询表单
    form = {
        name: '', // 活动名称
        goods_info: '', // 商品信息，支持商品名称、商品编码
        user_info: '', // 用户信息，支持用户昵称、用户编号
        activity_info: '', // 活动名称
        status: ' ', // 砍价记录状态
        start_time: '',
        end_time: ''
    }

    apiTeamRecord = apiTeamRecord
    /** E Data **/

    /** S Methods **/
    splitTime() {
        if (this.tableData != null) {
            this.form.start_time = this.tableData[0]
            this.form.end_time = this.tableData[1]
        }
    }
    // 重置
    onReset() {
        this.form = {
            name: '',
            goods_info: '', // 商品信息，支持商品名称、商品编码
            user_info: '', // 用户信息，支持用户昵称、用户编号
            activity_info: '', // 活动名称
            status: ' ', // 砍价记录状态
            start_time: '',
            end_time: ''
        }
        this.tableData = []
        this.getList()
    }
    // 记录
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiTeamRecord,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {})
    }

    // 结束砍价
    onStop(id: any) {
        apiTeamCancel({
            id: id
        }).then((res: any) => {
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

<style lang="scss" scoped></style>
