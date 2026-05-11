<template>
    <div>
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="分销申请详情"></el-page-header>
        </div>

        <!-- 用户信息 -->
        <div class="ls-card m-t-10">
            <div class="nr weight-500 m-b-20">用户信息</div>
            <el-form ref="distributionData" :model="distributionData" label-width="120px" size="small">
                <el-form-item label="用户信息：" prop="" required>
                    <template> {{ distributionData.nickname }}（{{ distributionData.sn }}） </template>
                </el-form-item>
            </el-form>
        </div>

        <!-- 申请信息 -->
        <div class="ls-card m-b-60 m-t-10">
            <div class="nr weight-500 m-b-20">申请信息</div>
            <el-form ref="distributionData" :model="distributionData" label-width="120px" size="small">
                <el-form-item label="真实姓名" prop="" required>
                    <template>
                        {{ distributionData.real_name }}
                    </template>
                </el-form-item>

                <el-form-item label="手机号码" prop="" required>
                    <template>
                        {{ distributionData.mobile }}
                    </template>
                </el-form-item>

                <el-form-item label="所属区域" prop="" required>
                    <template>
                        {{ distributionData.address }}
                    </template>
                </el-form-item>

                <el-form-item label="申请原因" prop="" required>
                    <template>
                        {{ distributionData.reason }}
                    </template>
                </el-form-item>

                <el-form-item label="申请状态" prop="" required>
                    <template>
                        {{ distributionData.status_desc }}
                    </template>
                </el-form-item>

                <el-form-item label="审核说明" prop="" required>
                    <template>
                        {{ distributionData.audit_remark }}
                    </template>
                </el-form-item>

                <el-form-item label="审核时间" prop="" required>
                    <template>
                        {{ distributionData.update_time }}
                    </template>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部 -->
        <div class="ls-coupon-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">返回</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiDistributionMemberDetails } from '@/api/distribution/distribution'
@Component
export default class AddSupplier extends Vue {
    /** S Data **/

    id!: any //当前的ID

    distributionData = {}

    /** E Data **/

    /** S Method **/

    // 获取申请分销详情
    getDetailsData() {
        apiDistributionMemberDetails({ id: this.id }).then((res: any) => {
            this.distributionData = { ...res }
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
</style>
