<!-- APP商城 -->
<template>
    <div class="app_store">
        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="140px" size="small">
            <!-- APP下载 -->
            <div class="ls-card m-t-16">
                <div class="card-title">APP下载</div>
                <div class="card-content m-t-24">
                    <!-- 提示 -->
                    <div class="ls-card-alert">
                        <el-alert
                            class="xs"
                            title="苹果APP可通过上架APP至苹果App Store获取下载链接；安卓APP可通过上架APP至应用宝获取下载链接；下载链接也可使用蒲公英等分发渠道的链接。"
                            type="info"
                            :closable="false"
                            show-icon
                        />
                    </div>
                    <el-form-item label="苹果APP下载链接">
                        <el-input class="ls-input m-r-10" size="small" v-model="form.ios_download_url"></el-input>
                    </el-form-item>
                    <el-form-item label="安卓APP下载链接">
                        <el-input class="ls-input m-r-10" size="small" v-model="form.android_download_url"></el-input>
                    </el-form-item>
                    <el-form-item label="APP下载引导文案">
                        <el-input class="ls-input m-r-10" size="small" v-model="form.download_title"></el-input>
                        <div class="muted xs m-r-16">分享APP页面打开后，H5页面顶部会显示APP下载引导文案</div>
                    </el-form-item>

                    <el-form-item label="协议弹窗" prop="pop_agreement">
                        <div class="flex">
                            <el-switch v-model="form.pop_agreement" :active-value="1" :inactive-value="0" />
                            <span class="m-l-16">
                                {{ form.pop_agreement ? '开启' : '关闭' }}
                            </span>
                        </div>
                        <div class="muted xs m-r-16">APP启动后强制弹窗同意服务协议与隐私政策</div>
                    </el-form-item>
                    <!-- <el-form-item label="微信登录" prop="wechat_login">
					    <div class="flex">
					        <el-switch v-model="form.wechat_login" :active-value="1" :inactive-value="0" />
					        <span class="m-l-16">
					            {{ form.wechat_login ? '开启' : '关闭' }}
					        </span>
					    </div>
					    <div class="muted xs m-r-16">选择开启，后则表示APP允许微信授权登录</div>
					</el-form-item> -->
                </div>
            </div>

            <!-- 微信开放平台 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信开放平台</div>
                <div class="card-content m-t-24">
                    <!-- 提示 -->
                    <div class="ls-card-alert">
                        <el-alert
                            class="xs"
                            title="APP需要使用微信授权登录、微信支付等微信生态能力时，需要设置关联微信开发平台；请填写APP在微信开发平台申请的应用ID等信息。"
                            type="info"
                            :closable="false"
                            show-icon
                        />
                    </div>
                    <el-form-item label="AppID" prop="app_id">
                        <el-input class="ls-input m-r-10" size="small" v-model="form.app_id"></el-input>
                    </el-form-item>
                    <el-form-item label="AppSecret" prop="app_secret">
                        <el-input class="ls-input m-r-10" size="small" v-model="form.app_secret"></el-input>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="putAppSettings()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiAppSettings, apiAppSettingsSet } from '@/api/channel/app_store'
import { AppSettings_Req } from '@/api/channel/app_store.d.ts'
@Component({
    components: {}
})
export default class AppStore extends Vue {
    /** S Data **/
    form: AppSettings_Req = {
        ios_download_url: '', // 苹果APP下载链接
        android_download_url: '', // 安卓APP下载链接
        download_title: '', // APP下载引导文案
        app_id: '', // 开放平台appid
        app_secret: '', // 开放平台appSecrets
        pop_agreement: 0 // APP启动后强制弹窗同意隐私协议
        // wechat_login: 0, // 选择开启，后则表示APP允许微信授权登录
    }

    // 表单验证
    formRules = {
        app_id: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: 'blur'
            }
        ],
        app_secret: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: 'blur'
            }
        ],
        pop_agreement: [
            {
                required: true,
                message: '请选择',
                trigger: 'blur'
            }
        ]
        // wechat_login: [
        //     {
        //         required: true,
        //         message: '请选择',
        //         trigger: 'blur'
        //     }
        // ]
    }
    $refs!: {
        formRef: any
    }
    /** E Data **/

    // 获取APP设置
    getAppSettings() {
        apiAppSettings()
            .then((res: any) => {
                this.form = res
            })
            .catch(() => {})
    }

    // 修改APP设置
    putAppSettings() {
        this.$refs.formRef.validate((valid: boolean) => {
            // 预校验
            if (!valid) {
                return this.$message.error('请完善信息')
            }
            apiAppSettingsSet(this.form)
                .then((res: any) => {
                    this.getAppSettings()
                    // this.$message.success('保存成功!')
                })
                .catch(() => {
                    // this.$message.error('保存失败!')
                })
        })
    }
    /** S Life Cycle **/
    created() {
        this.getAppSettings()
    }
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
}

.ls-card-alert {
    border-radius: 8px;
    background-color: #ffffff;
    padding: 0 24px 24px 24px;
    flex: 1;
}

.app_store {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }
}
</style>
