<template>
    <div>
        <div class="ls-card">
            <!-- Header 头部 -->
            <div class="m-t-16">
                <el-form ref="form" inline :model="goodsSearchData" label-width="100px" size="small">
                    <el-form-item label="用户信息">
                        <el-input
                            style="width: 280px"
                            v-model="goodsSearchData.user_info"
                            placeholder="用户昵称/用户编号"
                        >
                        </el-input>
                    </el-form-item>

                    <el-form-item label="分销等级">
                        <el-select v-model="goodsSearchData.level_id" placeholder="全部">
                            <el-option
                                v-for="item in distributionList"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="分销状态">
                        <el-select v-model="goodsSearchData.is_freeze" placeholder="全部用户">
                            <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="成为分销商时间">
                        <date-picker
                            :start-time.sync="goodsSearchData.start_time"
                            :end-time.sync="goodsSearchData.end_time"
                        />
                    </el-form-item>

                    <el-form-item label="" class="m-l-6">
                        <el-button size="mini" type="primary" @click="getListData(1)">查询</el-button>
                        <el-button size="mini" @click="resetgoodsSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiDistributionMemberLists"
                            :param="goodsSearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-24 ls-card">
            <!-- Section 主体 -->
            <open-store @update="getListData" title="开通分销商">
                <el-button size="mini" slot="trigger" type="primary">开通分销商</el-button>
            </open-store>

            <!-- Tabel 表格 -->
            <el-table
                ref="paneTable"
                class="m-t-24"
                :data="pager.lists"
                v-loading="pager.loading"
                style="width: 100%"
                size="mini"
            >
                <!-- 用户信息 -->
                <el-table-column prop="name" label="用户信息" min-width="180">
                    <template slot-scope="scope">
                        <div class="flex pointer" @click="toUserDetail(scope.row.id)">
                            <el-image class="flex-none" style="width: 58px; height: 58px" :src="scope.row.avatar" />
                            <div class="goods-info m-l-8">
                                <div class="line-2">
                                    {{ scope.row.nickname
                                    }}<span style="color: red" v-if="scope.row.user_delete"> （已注销）</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="user_level_name" label="用户等级" min-width="140"></el-table-column>
                <el-table-column prop="distribution_level_name" label="分销等级" min-width="140"></el-table-column>
                <el-table-column prop="earnings" label="已入账佣金" min-width="140"></el-table-column>
                <el-table-column prop="wait_earnings" label="待结算佣金" min-width="140"></el-table-column>
                <el-table-column prop="first_leader_name" label="上级推荐人" min-width="140"></el-table-column>
                <el-table-column prop="is_freeze_desc" label="分销状态" min-width="140">
                    <!-- <template slot-scope="scope">
                        {{scope.row.is_distribution==1?'开启':'关闭'}}
                    </template> -->
                </el-table-column>
                <el-table-column prop="distribution_time" label="成为分销商时间" min-width="140"></el-table-column>

                <!-- 操作栏目 -->
                <el-table-column fixed="right" label="操作" min-width="140">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-button
                                slot="trigger"
                                class="m-r-10"
                                type="text"
                                size="small"
                                @click="
                                    $router.push({
                                        path: '/distribution/store_detail',
                                        query: { id: scope.row.id }
                                    })
                                "
                                >详情</el-button
                            >

                            <distribute-grade class="m-l-20" @update="resetgoodsSearchData" :id="scope.row.id">
                                <el-button
                                    class="m-l-20"
                                    slot="trigger"
                                    type="text"
                                    size="small"
                                    :disabled="scope.row.user_delete"
                                    >等级调整</el-button
                                >
                            </distribute-grade>

                            <ls-dialog
                                title="冻结资格"
                                v-if="scope.row.is_freeze == 0"
                                class="inline m-l-10"
                                :content="`确定冻结该用户的分销资格吗？：${scope.row.nickname}（${scope.row.id}）`"
                                @confirm="frozen(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small" :disabled="scope.row.user_delete"
                                    >冻结资格</el-button
                                >
                            </ls-dialog>

                            <ls-dialog
                                title="恢复资格"
                                v-if="scope.row.is_freeze == 1"
                                class="inline m-l-10"
                                :content="`确定恢复该用户的分销资格吗？：${scope.row.nickname}（${scope.row.id}）`"
                                @confirm="frozen(scope.row.id)"
                            >
                                <el-button slot="trigger" type="text" size="small" :disabled="scope.row.user_delete"
                                    >恢复资格</el-button
                                >
                            </ls-dialog>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex row-right m-t-24">
                <ls-pagination v-model="pager" @change="getListData()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import DatePicker from '@/components/date-picker.vue'
import OpenStore from '@/components/marketing/open-store.vue'
import ExportData from '@/components/export-data/index.vue'
import DistributeGrade from '@/components/marketing/distribute-grade.vue'
import {
    apiDistributionMemberLists, //列表
    apiDistributionfreeze, //冻结解冻
    apiDistributionGradeLists //分销等级
} from '@/api/distribution/distribution'
@Component({
    components: {
        LsPagination,
        DatePicker,
        LsDialog,
        OpenStore,
        DistributeGrade,
        ExportData
    }
})
export default class DistributionGoods extends Vue {
    /** S Data **/

    apiDistributionMemberLists = apiDistributionMemberLists

    distributionList: any = []

    goodsSearchData = {
        user_info: '',
        level_id: '', //分销会员等级id
        is_freeze: '',
        start_time: '',
        end_time: ''
    }

    options = [
        { value: '', label: '全部' },
        { value: '0', label: '正常' },
        { value: '1', label: '冻结' }
    ]

    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    // 获取分销列表
    getListData(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiDistributionMemberLists,
            params: {
                ...this.goodsSearchData
            }
        })
    }

    // 分销等级
    getGoodsOtherList() {
        apiDistributionGradeLists({}).then((res: any) => {
            this.distributionList = res.lists
        })
    }

    //  冻结或者解冻
    frozen(id: number): void {
        apiDistributionfreeze({
            user_id: id
        }).then(() => {
            this.resetgoodsSearchData()
        })
    }

    // 重置
    resetgoodsSearchData() {
        Object.keys(this.goodsSearchData).map(key => {
            this.$set(this.goodsSearchData, key, '')
        })
        this.getListData()
    }

    // 去用户详情
    toUserDetail(id: any) {
        this.$router.push({
            path: '/user/user_details',
            query: {
                id: id
            }
        })
    }

    /** E Method **/

    created() {
        this.getListData()
        this.getGoodsOtherList()
    }
}
</script>

<style lang="scss" scoped>
.pointer:hover {
    color: $--color-primary;
}
</style>
