<template>
    <div class="">
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="分销商详情"></el-page-header>
        </div>

        <!-- 优惠设置 -->
        <div class="ls-card m-t-10 m-b-60">
            <div class="xxl weight-500 m-b-20">分销信息</div>

            <div class="content">
                <el-row :gutter="20">
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">分销等级</div>
                        <div class="flex row-center">
                            <div class="m-l-50 m-r-10">
                                {{ distributionData.level_name }}
                            </div>
                            <distribute-grade :id="id" @update="getDetailsData">
                                <el-button
                                    class=""
                                    slot="trigger"
                                    type="text"
                                    size="small"
                                    :disabled="distributionData.user_info.user_delete"
                                    >等级调整</el-button
                                >
                            </distribute-grade>
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">已入账佣金</div>
                        <div class="m-t-8">
                            {{ distributionData.earningns }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">待结算佣金</div>
                        <div class="m-t-8">
                            {{ distributionData.unreturned_commission }}
                        </div>
                    </el-col>
                    <el-col :span="6" class="statistics-col">
                        <div class="lighter">分销订单</div>
                        <div class="m-t-8">
                            {{ distributionData.distribution_order_num }}
                        </div>
                    </el-col>
                </el-row>
            </div>

            <el-form ref="distributionData" :model="distributionData" label-width="120px" size="small">
                <el-form-item label="用户信息" prop="">
                    <template>
                        {{ distributionData.user_info.nickname }}({{ distributionData.user_info.sn }})
                    </template>
                </el-form-item>

                <el-form-item label="上级分销商" prop="">
                    <template>
                        <div class="flex">
                            <div class="m-r-10">
                                {{ distributionData.first_leader_info }}
                            </div>
                            <ls-invitation-change
                                title="上级分销商调整"
                                :nickname="distributionData.user_info.nickname"
                                :sn="distributionData.user_info.sn"
                                :value="distributionData.user_info.first_leader"
                                :userId="id"
                                @refresh="getDetailsData"
                            >
                                <el-button
                                    slot="trigger"
                                    type="text"
                                    size="small"
                                    icon="el-icon-edit"
                                    :disabled="distributionData.user_info.user_delete"
                                ></el-button>
                            </ls-invitation-change>
                        </div>
                    </template>
                </el-form-item>

                <el-form-item label="下级人数" prop="">
                    <template>
                        {{ distributionData.fans }}
                        <span
                            class="primary m-l-20 pointer"
                            @click="toInvitationList"
                            v-if="!distributionData.user_info.user_delete"
                            >查看下级列表</span
                        >
                    </template>
                </el-form-item>

                <el-form-item label="下级分销商人数" prop="">
                    <template>
                        {{ distributionData.fans_distribution }}
                    </template>
                </el-form-item>

                <el-form-item label="下一级人数" prop="">
                    <template>
                        {{ distributionData.fans_one }}
                    </template>
                </el-form-item>

                <el-form-item label="下二级人数" prop="">
                    <template>
                        {{ distributionData.fans_two }}
                    </template>
                </el-form-item>

                <el-form-item label="分销状态" prop="">
                    <template>
                        {{ distributionData.is_freeze == 0 ? '正常' : '冻结' }}
                    </template>
                </el-form-item>

                <el-form-item label="成为分销商时间" prop="">
                    <template>
                        {{ distributionData.distribution_time }}
                    </template>
                </el-form-item>
            </el-form>

            <div v-if="!distributionData.user_info.user_delete">
                <ls-dialog
                    title="冻结资格"
                    v-if="distributionData.is_freeze == 0"
                    class="inline m-l-10"
                    :content="`确定冻结该用户的分销资格吗？`"
                    @confirm="frozen"
                >
                    <el-button size="mini" slot="trigger" stype="primary">冻结资格</el-button>
                </ls-dialog>

                <ls-dialog
                    v-else
                    title="恢复资格"
                    class="inline m-l-10"
                    :content="`确定恢复该用户的分销资格吗？`"
                    @confirm="frozen"
                >
                    <el-button size="mini" slot="trigger" type="primary">恢复资格</el-button>
                </ls-dialog>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiDistributionDetail, apiDistributionfreeze } from '@/api/distribution/distribution'
import LsInvitationChange from '@/components/user/ls-invitation-change.vue'
import DistributeGrade from '@/components/marketing/distribute-grade.vue'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog,
        DistributeGrade,
        LsInvitationChange
    }
})
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any //当前的ID

    distributionData: any = {
        level_name: '',
        earningns: '',
        unreturned_commission: '',
        distribution_order_num: '',
        user_info: {
            nickname: '凉央未夜11111111111',
            sn: '58565263'
        }
    }

    /** E Data **/

    /** S Method **/

    // 获取分销商详情
    getDetailsData() {
        apiDistributionDetail({ user_id: this.id }).then((res: any) => {
            this.distributionData = { ...res }
        })
    }

    //  冻结或者解冻
    frozen(): void {
        apiDistributionfreeze({
            user_id: this.id
        }).then(() => {
            this.getDetailsData()
        })
    }

    // 跳转邀请人数页面
    toInvitationList() {
        this.$router.push({
            path: '/distribution/lower_level_list',
            query: {
                id: this.id
            }
        })
    }

    /** E Method **/

    created() {
        this.id = this.$route.query.id
        this.id && this.getDetailsData()
    }
}
</script>

<style lang="scss" scoped>
.desc {
    display: block;
    color: #999;
    font-size: 12px;
}
.content {
    padding: 20px 0;
    margin-bottom: 20px;
    background: #f5f5f5;
}
.statistics-col {
    text-align: center;
}
</style>
