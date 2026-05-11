<template>
    <div class="user_setting">
        <!-- 提示 -->
        <!-- <div class="ls-card">
      <el-alert title="温馨提示：应工信部的要求,请务必填写公安备案号和网站备案号,保存的备案信息,将展示在后台登陆页面" type="info" :closable="false"
        show-icon />
    </div> -->
        <!-- 主要内容 -->
        <el-form ref="form" label-width="120px" size="small">
            <!-- 店铺信息 -->
            <div class="ls-card">
                <div class="card-title">基本设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="用户默认头像">
                        <material-select :limit="1" :disabled="false" v-model="form.default_avatar" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：400*400像素，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" width="200" trigger="hover">
                                <el-image :src="images.avater" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 用户关系 -->
            <div class="ls-card m-t-16">
                <div class="card-title">用户关系</div>
                <div class="card-content m-t-24">
                    <el-form-item label="开启邀请下级" required>
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.invite_open"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.invite_open ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">系统是否开启邀请下级功能，关闭功能后用户之间不能建立新的上下级关系。</div>
                    </el-form-item>
                </div>
                <div class="card-content m-t-24">
                    <el-form-item label="邀请下级资格" required>
                        <!-- 多选框 -->
                        <!-- <el-checkbox-group v-model="form.invite_ways">
              <el-checkbox label="1">全部用户可以邀请</el-checkbox>
              <el-checkbox label="2">分销会员可以邀请</el-checkbox>
            </el-checkbox-group> -->
                        <!-- 单选按钮 -->
                        <el-radio-group class="m-r-16" v-model="form.invite_ways">
                            <el-radio :label="1">全部用户可以邀</el-radio>
                            <el-radio :label="2">指定用户</el-radio>
                        </el-radio-group>
                        <div class="" v-show="form.invite_ways == 2">
                            <!-- 多选框 -->
                            <el-checkbox-group v-model="form.invite_appoint_user" @change="handleUserLevel">
                                <el-checkbox v-for="item in userLevelLists" :key="item.id" :label="item.id + ''">{{
                                    item.name
                                }}</el-checkbox>
                            </el-checkbox-group>
                        </div>
                        <!-- <div class="muted xs">可以多选，勾选全部用户可以邀请表示系统所有用户都有邀请下级的资格</div> -->
                    </el-form-item>
                </div>
                <div class="card-content m-t-24">
                    <el-form-item label="成为下级条件" required>
                        <el-radio :label="1" v-model="form.invite_condition">首次绑定邀请码</el-radio>
                        <div class="muted xs">
                            用户登录后首次绑定邀请码建立上下级关系。包括扫码，点击分享链接，输入邀请码等场景。
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="setUserSetting()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiUserLevelList } from '@/api/user/user'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiUserConfig, apiUserConfigSet } from '@/api/setting/user'
@Component({
    components: {
        MaterialSelect
    }
})
export default class UserSetting extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        default_avatar: '', // 默认头像
        scene: '', // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        invite_open: 0, // 邀请下级：0-关闭 1-开启
        invite_ways: 1, // 邀请下级资格：1-全部用户可邀请 2-分销会员可邀请
        invite_appoint_user: [], // 邀请下级指定用户；1-分销会员；2-股东会员；3-代理会员（邀请下级资格为1时留空）
        invite_condition: 1, // 成为下级条件：1-邀请码
        poster: '' // 邀请海报
    }

    // eslint-disable-next-line camelcase
    invite_appoint_user: any = []
    userLevelLists: any = []
    // 图片
    images = {
        avater: require('@/assets/images/setting/img_shili_mine_touxiang.png')
    }
    /** E Data **/

    // 获取用户提现设置
    getUserSetting() {
        apiUserConfig()
            .then((res: any) => {
                this.form = res
                if (this.form.invite_appoint_user === null) {
                    this.form.invite_appoint_user = []
                } else if (typeof this.form.invite_appoint_user[0] === 'string') {
                    // this.form.invite_appoint_user = []
                }
            })
            .catch(() => {
                this.$message.error('数据请求失败!')
            })
    }

    // 修改用户提现设置
    setUserSetting() {
        if (this.form.invite_ways === 1) {
            this.form.invite_appoint_user = []
        }
        this.form.scene = 'user' // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        apiUserConfigSet(this.form).then((res: any) => {
            setTimeout(() => {
                this.getUserSetting()
            }, 50)
        })
    }

    getUserLevelLists() {
        apiUserLevelList({}).then((res: any) => {
            this.userLevelLists = res.lists
        })
    }

    handleUserLevel(event: any) {
        const row = this.userLevelLists.map((item: { id: any }) => item.id)
        this.form.invite_appoint_user = this.getArrEqual(row, event).map(String)
    }

    getArrEqual(arr1: any, arr2: any) {
        const newArr = []
        for (let i = 0; i < arr2.length; i++) {
            for (let j = 0; j < arr1.length; j++) {
                if (arr1[j] == arr2[i]) {
                    newArr.push(arr1[j])
                }
            }
        }
        return newArr
    }

    /** S Life Cycle **/
    created() {
        this.getUserSetting()
        this.getUserLevelLists()
    }
    // mounted() {
    // 	this.form.invite_appoint_user = [1,2,3]
    // }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 280px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }

    .login_limit-unit {
        display: inline-block;
        width: 2em;
        text-align: center;
    }
}
.user_setting {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
