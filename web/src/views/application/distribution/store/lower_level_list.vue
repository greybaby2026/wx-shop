<template>
    <div class="user-invitation-list">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="下级列表" />
        </div>

        <!-- 主要内容 -->
        <el-form label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">分销商信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="用户信息" prop="">
                        {{ user_info.user_info.nickname }}({{ user_info.user_info.sn }})
                    </el-form-item>
                    <el-form-item label="下级人数" prop="">
                        {{ user_info.fans || '-' }}
                    </el-form-item>
                    <el-form-item label="下级分销商人数 " prop="">
                        {{ user_info.fans_distribution || '-' }}
                    </el-form-item>
                    <el-form-item label="下一级人数" prop="">
                        {{ user_info.fans_one || '-' }}
                    </el-form-item>
                    <el-form-item label="下二级人数  " prop="">
                        {{ user_info.fans_two || '-' }}
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <!-- 下级数据 -->
        <div class="ls-card m-t-16">
            <div class="card-title">下级列表</div>
            <div class="journal-search m-t-24">
                <el-form inline :model="form" label-width="120px" size="small" class="ls-form">
                    <el-form-item label="用户信息">
                        <el-input v-model="form.user_info" placeholder="请输入用户昵称/用户编号"> </el-input>
                    </el-form-item>
                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>

                    <!-- <export-data class="m-l-10" :pageSize="pager.size" :method="apiDistributionGetFansLists" :param="form"></export-data> -->
                </el-form>

                <div style="margin: 0 55px" class="p-b-24">
                    <el-tabs v-model="activeName" @tab-click="getList(1)">
                        <el-tab-pane
                            v-for="(item, index) in tabs"
                            :key="index"
                            :label="`${item.label}(${tabCount[item.name]})`"
                            :name="item.name"
                        >
                        </el-tab-pane>
                    </el-tabs>
                </div>

                <div class="list-table m-t-16" style="margin: 0 55px">
                    <el-table
                        :data="pager.lists"
                        style="width: 100%"
                        v-loading="pager.loading"
                        size="mini"
                        :header-cell-style="{ background: '#f5f8ff' }"
                    >
                        <el-table-column prop="avatar" label="用户头像" min-width="" width="250">
                            <template slot-scope="scope">
                                <div class="flex">
                                    <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                    <span class="m-l-15">{{ scope.row.nickname }}({{ scope.row.sn }})</span>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="level_name" label="分销等级" min-width="" width=""> </el-table-column>
                        <el-table-column prop="earnings" label="已入账佣金" min-width="" width=""> </el-table-column>
                        <el-table-column prop="unreturned_commission" label="待结算佣金" min-width="" width="">
                        </el-table-column>
                        <el-table-column prop="first_leader_info" label="上级分销商" min-width="" width="">
                        </el-table-column>
                        <el-table-column prop="login_time" label="分销状态" min-width="" width="">
                            <template slot-scope="scope">
                                {{ scope.row.is_freeze == 1 ? '已冻结' : '未冻结' }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="distribution_time" label="成为分销商时间" min-width="" width="">
                        </el-table-column>
                    </el-table>
                </div>
                <div class="flex row-right p-t-24 row-right" style="margin: 0 55px">
                    <ls-pagination v-model="pager" @change="getList()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiDistributionGetFans, apiDistributionGetFansLists } from '@/api/distribution/distribution'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import FansPane from '@/components/user/fans-pane.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        FansPane,
        ExportData
    }
})
export default class LsFanList extends Vue {
    apiDistributionGetFansLists = apiDistributionGetFans

    /** S Data **/
    // 邀请人信息
    user_info = {
        user_info: {
            nickname: '',
            sn: ''
        },
        fans: '',
        fans_distribution: '',
        fans_one: '',
        fans_two: ''
    }

    activeName: any = '1' //全部;

    tabs = [
        {
            label: '下一级',
            name: '1'
        },
        {
            label: '下二级',
            name: '2'
        }
    ]

    tabCount = {
        1: '0', //全部
        2: '0' //待支付
    }

    // 查询表单
    form = {
        user_id: '', // 用户id
        user_info: ''
    }

    pager: RequestPaging = new RequestPaging()
    /** E Data **/

    /** S Methods **/
    // 重置
    onReset() {
        this.form.user_info = ''
        this.getList()
    }

    // 用户信息
    getUserInfo() {
        apiDistributionGetFans({ user_id: this.form.user_id }).then((res: any) => {
            this.user_info = res
        })
    }
    // 邀请列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiDistributionGetFansLists,
                params: {
                    level: this.activeName,
                    ...this.form
                }
            })
            .then((res: any) => {
                this.tabCount['1'] = res.extend.one
                this.tabCount['2'] = res.extend.two
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        if (query.id) {
            this.$set(this.form, 'user_id', query.id)
        }

        setTimeout(() => {
            this.getUserInfo()
            this.getList()
        }, 50)
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 133px;
    }

    .ls-input-textarea {
        width: 300px;
    }

    .card-title {
        font-size: 16px;
    }
}

.user-invitation-list {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
