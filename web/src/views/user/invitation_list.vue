<!-- 用户邀请列表 -->
<!-- 推荐列表 -->
<template>
    <div class="user-invitation-list">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="邀请列表" />
        </div>

        <!-- 主要内容 -->
        <el-form label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-title">用户信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="用户信息" prop="">
                        {{ inviter.name }}
                    </el-form-item>
                    <el-form-item label="邀请人数" prop="">
                        {{ inviter.count || '-' }}
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <!-- 提现记录表 -->
        <div class="ls-card m-t-16">
            <div class="card-title">邀请列表</div>
            <div class="journal-search m-t-24">
                <el-form inline :model="form" label-width="120px" size="small" class="ls-form">
                    <!-- <el-form-item label="用户信息">
						<el-input v-model="form.sn" placeholder="请输入用户编号/昵称查询"></el-input>
					</el-form-item> -->
                    <el-form-item label="用户信息">
                        <el-select v-model="isNameSN" placeholder="用户编号" style="width: 120px">
                            <el-option label="用户编号" value="0"></el-option>
                            <el-option label="用户昵称" value="1"></el-option>
                        </el-select>
                        <el-input v-if="isNameSN == 0" v-model="form.sn" placeholder="请输入用户编号查询"> </el-input>
                        <el-input
                            v-if="isNameSN == 1"
                            v-model="form.nickname"
                            placeholder="请输入用户昵称查询"
                        ></el-input>
                    </el-form-item>
                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                    <!-- 导出按钮 -->
                    <export-data
                        class="m-l-10"
                        :userId="form.user_id"
                        :pageSize="pager._size"
                        :method="apiUserInviterLists"
                        :param="form"
                    ></export-data>
                </el-form>

                <div class="list-table m-t-16" style="margin: 0 55px">
                    <el-table
                        :data="pager.lists"
                        style="width: 100%"
                        v-loading="pager.loading"
                        size="mini"
                        :header-cell-style="{ background: '#f5f8ff' }"
                    >
                        <el-table-column prop="sn" label="用户编号" min-width="" width=""> </el-table-column>
                        <el-table-column prop="nickname" label="用户昵称" min-width="" width=""> </el-table-column>
                        <el-table-column prop="avatar" label="用户头像" min-width="" width="">
                            <template slot-scope="scope">
                                <div class="flex">
                                    <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="level_name" label="用户等级" min-width="" width=""> </el-table-column>
                        <el-table-column prop="mobile" label="手机号码" min-width="" width=""> </el-table-column>
                        <el-table-column prop="user_money" label="钱包金额" min-width="" width=""> </el-table-column>
                        <el-table-column prop="total_order_amount" label="消费金额" min-width="" width="">
                        </el-table-column>
                        <el-table-column prop="login_time" label="最近登录时间" min-width="" width="">
                        </el-table-column>
                        <el-table-column prop="create_time" label="注册时间" min-width="" width=""> </el-table-column>
                    </el-table>
                </div>
                <div class="flex row-right m-t-16 row-right" style="margin: 0 55px">
                    <ls-pagination v-model="pager" @change="getList()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiUserInfo, apiUserInviterLists } from '@/api/user/user'
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
    /** S Data **/
    isNameSN = '' // 0-编号， 1-昵称
    // 邀请人信息
    inviter = {
        name: '', // 邀请人名称
        num: '' // 我邀请的人数
    }

    // 查询表单
    form = {
        user_id: '', // 用户id
        sn: '', // 用户编号
        nickname: '' // 用户昵称
    }
    pager: RequestPaging = new RequestPaging()

    apiUserInviterLists = apiUserInviterLists
    /** E Data **/

    // 监听用户信息查询框条件
    @Watch('isNameSN', {
        immediate: true
    })
    getChange(val: any) {
        // 初始值
        this.form.sn = ''
        this.form.nickname = ''
        // this.form.mobile = ''
    }

    /** S Methods **/
    // 重置
    onReset() {
        this.form.sn = ''
        this.form.nickname = ''
        this.getList()
    }

    // 用户信息
    getUserInfo() {
        apiUserInfo({ user_id: this.form.user_id }).then((res: any) => {
            this.inviter = res
        })
    }
    // 邀请列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiUserInviterLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {})
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
        font-size: 14px;
        font-weight: 500;
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
