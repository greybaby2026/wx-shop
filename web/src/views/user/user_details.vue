<!-- 用户详情 -->
<template>
    <div class="user-details">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="用户详情" />
        </div>
        <!-- 中间用户数据 -->

        <div class="m-t-16 flex col-top">
            <!-- 基本资料 -->
            <div class="ls-card card-height">
                <div class="card-title">基本资料</div>
                <div class="content m-t-16 m-b-16">
                    <el-row :gutter="20">
                        <el-col :span="4" class="flex-col col-center">
                            <div class="lighter m-b-8">用户头像</div>
                            <img :src="user_info.avatar" width="58" height="58" class="avatar" />
                        </el-col>
                        <el-col :span="4" class="flex-col col-center item">
                            <div class="lighter m-b-8">钱包金额</div>
                            <div class="" style="margin: 7px 0">
                                {{ user_info.total_user_money }}
                            </div>
                        </el-col>
                        <el-col :span="4" class="flex-col col-center item">
                            <div class="lighter m-b-8">可用余额</div>
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.user_money }}
                                </div>
                                <ls-user-change
                                    title="可用余额调整"
                                    :value="user_info.user_money"
                                    :type="1"
                                    :userId="user_id"
                                    @refresh="userDetail"
                                >
                                    <el-button type="text" slot="trigger" size="small" :disabled="user_info.user_delete"
                                        >调整</el-button
                                    >
                                </ls-user-change>
                            </div>
                        </el-col>
                        <el-col :span="4" class="flex-col col-center item">
                            <div class="lighter m-b-8">可提现金额</div>
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.user_earnings }}
                                </div>
                                <ls-user-change
                                    title="可提现金额调整"
                                    :value="user_info.user_earnings"
                                    :type="2"
                                    :userId="user_id"
                                    @refresh="userDetail"
                                >
                                    <el-button type="text" slot="trigger" size="small" :disabled="user_info.user_delete"
                                        >调整</el-button
                                    >
                                </ls-user-change>
                            </div>
                        </el-col>
                        <el-col :span="4" class="flex-col col-center item">
                            <div class="lighter m-b-8">积分</div>
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.user_integral }}
                                </div>
                                <ls-user-change
                                    title="积分调整"
                                    :value="user_info.user_integral"
                                    :type="3"
                                    :userId="user_id"
                                    @refresh="userDetail"
                                >
                                    <el-button type="text" slot="trigger" size="small" :disabled="user_info.user_delete"
                                        >调整</el-button
                                    >
                                </ls-user-change>
                            </div>
                        </el-col>
                        <el-col :span="4" class="flex-col col-center item">
                            <div class="lighter m-b-8">可用优惠券</div>
                            <div class="flex">
                                <div class="" style="margin: 7px 0">
                                    {{ user_info.coupon_num }}
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </div>
                <el-form
                    :rules="userRules"
                    ref="userRef"
                    :model="user_info"
                    label-width="120px"
                    size="small"
                    class="flex col-top"
                >
                    <div class="">
                        <el-form-item label="用户编号" prop="">
                            <div>
                                {{ user_info.sn }}
                            </div>
                        </el-form-item>
                        <el-form-item label="用户昵称" prop="">
                            <div>
                                {{ user_info.nickname }}
                            </div>
                        </el-form-item>
                        <el-form-item label="用户等级" prop="">
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.level_name }}
                                </div>
                                <ls-level-change
                                    title="等级调整"
                                    :value="user_info.level_name"
                                    :userId="user_id"
                                    @refresh="userDetail"
                                >
                                    <el-button type="text" slot="trigger" size="small" :disabled="user_info.user_delete"
                                        >等级调整</el-button
                                    >
                                </ls-level-change>
                            </div>
                        </el-form-item>
                        <el-form-item label="真实姓名" prop="">
                            <div class="flex">
                                <div class="ls-input m-r-10">
                                    {{ user_info.real_name }}
                                </div>
                                <popover-input-user
                                    :width="400"
                                    changeType="input"
                                    type="text"
                                    :value="user_info.real_name"
                                    @confirm="userSetInfo($event, 'real_name')"
                                >
                                    <el-button
                                        class="ls-edit"
                                        type="text"
                                        icon="el-icon-edit"
                                        :disabled="user_info.user_delete"
                                    ></el-button>
                                </popover-input-user>
                            </div>
                        </el-form-item>
                        <el-form-item label="性别" prop="">
                            <div class="flex">
                                <div class="ls-input m-r-10">
                                    {{ user_info.sex }}
                                </div>
                                <popover-input-user
                                    :width="300"
                                    type="text"
                                    :value="user_info.sex"
                                    changeType="sex"
                                    @confirm="userSetInfo($event, 'sex')"
                                >
                                    <el-button
                                        class="ls-edit"
                                        type="text"
                                        icon="el-icon-edit"
                                        :disabled="user_info.user_delete"
                                    ></el-button>
                                </popover-input-user>
                            </div>
                        </el-form-item>
                        <el-form-item label="手机号码" prop="">
                            <div class="flex">
                                <div class="ls-input m-r-10">
                                    {{ user_info.mobile }}
                                </div>
                                <popover-input-user
                                    :width="400"
                                    type="text"
                                    changeType="input"
                                    :value="user_info.mobile"
                                    @confirm="userSetInfo($event, 'mobile')"
                                >
                                    <el-button
                                        class="ls-edit"
                                        type="text"
                                        icon="el-icon-edit"
                                        :disabled="user_info.user_delete"
                                    ></el-button>
                                </popover-input-user>
                            </div>
                        </el-form-item>
                        <el-form-item label="生日" prop="">
                            <div class="flex">
                                <div class="ls-input m-r-10">
                                    {{ user_info.birthday }}
                                </div>
                                <popover-input-user
                                    :width="400"
                                    changeType="time"
                                    type="text"
                                    :value="user_info.birthday"
                                    @confirm="userSetInfo($event, 'birthday')"
                                >
                                    <el-button
                                        class="ls-edit"
                                        type="text"
                                        icon="el-icon-edit"
                                        :disabled="user_info.user_delete"
                                    ></el-button>
                                </popover-input-user>
                            </div>
                        </el-form-item>
                        <el-form-item label="邀请人" prop="">
                            <div>
                                {{ user_info.inviter.name }}
                            </div>
                        </el-form-item>
                        <!-- <el-form-item label="邀请码" prop="">
							<div>
								{{user_info.code}}
							</div>
						</el-form-item> -->
                        <el-form-item label="我邀请的人数" prop="">
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.inviter.num }}
                                </div>
                                <el-button
                                    slot="trigger"
                                    type="text"
                                    size="small"
                                    @click="toInvitationList(user_info)"
                                    :disabled="user_info.user_delete"
                                    >我邀请的人数</el-button
                                >
                            </div>
                        </el-form-item>
                        <el-form-item label="上级分销商" prop="">
                            <div class="flex">
                                <div class="m-r-10">
                                    {{ user_info.first_leader_info.name }}
                                </div>
                                <ls-invitation-change
                                    title="上级分销商调整"
                                    :value="user_info.first_leader_info.name"
                                    :userId="user_id"
                                    @refresh="userDetail"
                                    :nickname="user_info.nickname"
                                    :sn="user_info.sn"
                                >
                                    <el-button
                                        slot="trigger"
                                        type="text"
                                        size="small"
                                        icon="el-icon-edit"
                                        :disabled="user_info.user_delete"
                                    ></el-button>
                                </ls-invitation-change>
                            </div>
                        </el-form-item>
                        <el-form-item label="注册来源">
                            <div>
                                {{ user_info.register_source }}
                            </div>
                        </el-form-item>
                        <el-form-item label="注册时间">
                            <div>
                                {{ user_info.create_time }}
                            </div>
                        </el-form-item>
                        <el-form-item label="最近登录时间">
                            <div>
                                {{ user_info.login_time }}
                            </div>
                        </el-form-item>
                        <el-form-item label="">
                            <el-button @click="handleFrozen" :disabled="user_info.user_delete">{{
                                user_info.disable ? '取消冻结' : '冻结会员'
                            }}</el-button>
                        </el-form-item>
                    </div>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <div class="card-title m-b-30">用户标签</div>
            <el-form
                :rules="userRules"
                ref="userRef"
                :model="user_info"
                label-width="120px"
                size="small"
                class="flex col-top"
            >
                <el-form-item label="用户标签" prop="">
                    <div class="flex">
                        <div class="m-r-10">
                            {{ user_info.labels }}
                        </div>
                        <ls-label-change
                            title="用户标签调整"
                            :value="user_info.labels"
                            :userId="user_id"
                            @refresh="userDetail"
                        >
                            <el-button
                                type="text"
                                slot="trigger"
                                size="small"
                                icon="el-icon-edit"
                                :disabled="user_info.user_delete"
                            ></el-button>
                        </ls-label-change>
                    </div>
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部交易数据 -->
        <div class="ls-card m-t-16">
            <div class="card-title m-b-30">交易数据</div>
            <el-form
                :rules="transactionRules"
                ref="transactionRef"
                :model="transaction"
                label-width="120px"
                size="small"
                class="flex col-top"
            >
                <div class="">
                    <el-form-item label="成交订单数 " prop="">
                        <div>
                            {{ transaction.total_order_num }}
                        </div>
                    </el-form-item>
                    <el-form-item label="消费金额 " prop="">
                        <div>
                            {{ transaction.total_order_amount }}
                        </div>
                    </el-form-item>
                    <el-form-item label="最近消费时间 " prop="">
                        <div>
                            {{ transaction.lately_order_time }}
                        </div>
                    </el-form-item>
                </div>
            </el-form>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiUserDetail, apiUserSetInfo } from '@/api/user/user'
import LsDialog from '@/components/ls-dialog.vue'
import LsUserChange from '@/components/user/ls-user-change.vue'
import LsLabelChange from '@/components/user/ls-label-change.vue'
import LsLevelChange from '@/components/user/ls-level-change.vue'
import LsInvitationChange from '@/components/user/ls-invitation-change.vue'
import PopoverInputUser from '@/components/user/popover-input-user.vue'
@Component({
    components: {
        LsDialog,
        LsUserChange,
        LsLabelChange,
        LsLevelChange,
        PopoverInputUser,
        LsInvitationChange
    }
})
export default class UserDetails extends Vue {
    /** S Data **/
    user_id = ''
    isShowWallet = 1 // 1-钱包资产 0-分销

    // 用户信息
    user_info = {
        id: '', // 用户id
        sn: '', // 用户编号
        nickname: '', // 用户昵称
        avatar: '', // 用户头像
        sex: '', // 性别 0-未知 1-男 2-女
        code: '', // 邀请码
        level_name: '', // 等级名称
        create_time: '', // 创建时间
        login_time: '', // 最后的登录时间
        disable: '', // 是否禁用：1-是；0-否（可用于显示加入黑名单和放出黑名单按钮）
        labels: '', // 用户标签
        leader_inviter: '', // 上级邀请人
        fans: '', // 团队人数
        total_user_money: '', // 钱包金额
        user_money: '', // 可用金额
        user_earnings: '', // 可提现金额
        user_integral: '', // 积分
        count_num: '', // 优惠券数量
        inviter: {
            name: '', // 邀请人名称
            num: '' // 我邀请的人数
        },
        first_leader_info: {
            name: '' // 上级分销商名称
        },
        user_delete: ''
    }
    // 交易信息
    transaction = {
        total_order_amount: '', // 	成交订单数
        total_order_num: '', // 	消费金额
        customer_price: ' ', // 	客单价
        lately_order_time: '' // 	上次消费时间
    }

    // 验证规则
    userRules = {
        // name: [{
        // 	required: true,
        // 	message: '请输入规则名称',
        // 	trigger: 'blur'
        // }],
    }
    transactionRules = {}

    /** E Data **/

    /** S Methods **/
    // 获取用户信息
    userDetail() {
        apiUserDetail({
            user_id: this.user_id
        })
            .then((res: any) => {
                this.user_info = res.user_info
                this.transaction = res.transaction
            })
            .catch((res: any) => {})
    }
    // 修改用户信息
    userSetInfo(val: string, type: string) {
        apiUserSetInfo({
            user_id: this.user_id,
            field: type,
            value: val
        })
            .then((res: any) => {
                this.userDetail()
            })
            .catch((res: any) => {})
    }

    // 跳转邀请人数页面
    toInvitationList(item: any) {
        this.$router.push({
            path: '/user/invitation_list',
            query: {
                id: item.id
            }
        })
    }

    // 冻结会员
    handleFrozen() {
        let { disable } = this.user_info as any
        disable = disable == 0 ? 1 : 0
        this.userSetInfo(disable, 'disable')
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        const query: any = this.$route.query
        if (query.id) {
            this.user_id = query.id
        }

        this.userDetail()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.content {
    background: #f5f5f5;
    padding: 11px 0;
}

.item {
    margin-top: 18px;
}

.avatar {
    border-radius: 29px;
}

.ls-card {
    // .ls-input {
    // 	width: 133px;
    // }

    // .ls-input-textarea {
    // 	width: 300px;
    // }
    .card-title {
        font-size: 14px;
        font-weight: 500;
        padding-bottom: 20px;
        border-bottom: 1px solid $--background-color-base;
    }
}

.card-height {
    // height: 600px;
    //box-sizing: border-box;
}

.user-details {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
