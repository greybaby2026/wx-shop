<template>
    <div class="channel-mp_wechat-index">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="温馨提示：请先前往微信公众号后台申请认证微信公众号-服务号。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <!-- 微信公众号 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信公众号</div>
                <div class="card-content m-t-24">
                    <el-form-item label="名称" prop="name">
                        <el-input class="ls-input" v-model="form.name" show-word-limit />
                    </el-form-item>
                    <el-form-item label="原始ID" prop="original_id">
                        <el-input class="ls-input" v-model="form.original_id" show-word-limit />
                    </el-form-item>
                    <el-form-item label="二维码" prop="qr_code">
                        <material-select :limit="1" v-model="form.qr_code" />
                        <div class="muted xs m-r-16">建议尺寸：宽400px*高400px。jpg，jpeg，png格式</div>
                    </el-form-item>
                </div>
            </div>

            <!-- 公众号开发者信息 -->
            <div class="ls-card m-t-16">
                <div class="card-title">公众号开发者信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="AppID" prop="app_id">
                        <el-input class="ls-input" v-model="form.app_id" show-word-limit />
                    </el-form-item>
                    <el-form-item label="AppSecret" prop="app_secret">
                        <el-input class="ls-input" v-model="form.app_secret" show-word-limit />
                        <div class="muted xs m-r-16">
                            登录微信公众平台，点击开发>基本配置>公众号开发信息，设置AppID和AppSecret
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 服务器配置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">服务器配置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="URL">
                        <el-input class="ls-input m-r-16" v-model="form.url" show-word-limit disabled />
                        <el-button @click="handleCopy(form.url)">复制</el-button>
                        <div class="muted xs">
                            登录微信公众平台，点击开发>基本配置>服务器配置，填写服务器地址（URL）
                        </div>
                    </el-form-item>
                    <el-form-item label="Token" prop="token">
                        <el-input class="ls-input" v-model="form.token" show-word-limit />
                        <div class="muted xs">
                            登录微信公众平台，点击开发>基本配置>服务器配置，设置令牌Token。不填默认为“likeshop”
                        </div>
                    </el-form-item>
                    <el-form-item label="EncodingAESKey" prop="encoding_aes_key">
                        <el-input class="ls-input" v-model="form.encoding_aes_key" show-word-limit />
                        <div class="muted xs">消息加密密钥由43位字符组成，字符范围为A-Z,a-z,0-9</div>
                    </el-form-item>
                    <el-form-item label="消息加密方式" prop="encryption_type">
                        <el-radio-group v-model="form.encryption_type">
                            <el-radio :label="1" class="form__item-encryption"
                                >明文模式 (不使用消息体加解密功能，安全系数较低)</el-radio
                            >
                            <el-radio :label="2" class="form__item-encryption"
                                >兼容模式 (明文、密文将共存，方便开发者调试和维护)</el-radio
                            >
                            <el-radio :label="3" class="form__item-encryption"
                                >安全模式（推荐） (消息包为纯密文，需要开发者加密和解密，安全系数高)</el-radio
                            >
                        </el-radio-group>
                    </el-form-item>
                </div>
            </div>

            <!-- 功能设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">功能设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="业务域名">
                        <el-input class="ls-input m-r-16" v-model="form.business_domain" show-word-limit disabled />
                        <el-button class="m-l-16" @click="handleCopy(form.business_domain)">复制</el-button>
                        <div class="muted xs">登录微信公众平台，点击设置>公众号设置>功能设置，填写业务域名</div>
                    </el-form-item>
                    <el-form-item label="JS接口安全域名">
                        <el-input class="ls-input m-r-16" v-model="form.js_secure_domain" show-word-limit disabled />
                        <el-button class="m-l-16" @click="handleCopy(form.js_secure_domain)">复制</el-button>
                        <div class="muted xs">登录微信公众平台，点击设置>公众号设置>功能设置，填写JS接口安全域名</div>
                    </el-form-item>
                    <el-form-item label="网页授权域名">
                        <el-input class="ls-input m-r-16" v-model="form.web_auth_domain" show-word-limit disabled />
                        <el-button @click="handleCopy(form.web_auth_domain)">复制</el-button>
                        <div class="muted xs">登录微信公众平台，点击设置>公众号设置>功能设置，填写网页授权域名</div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="onResetFrom">重置</el-button>
                <el-button size="small" type="primary" @click="onSubmitFrom('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiMpWeChatConfigEdit, apiMPWeChatConfigInfo } from '@/api/channel/mp_wechat'
import { copyClipboard } from '@/utils/util'

@Component({
    components: {
        MaterialSelect
    }
})
export default class MPWechatIndex extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        name: '', // 公众号名称
        original_id: '', // 原始id
        qr_code: '', // 二维码
        app_id: '', // APP ID
        app_secret: '', // App Secret
        url: '', // URL
        token: '', // Token
        encoding_aes_key: '', // Encoding AES Key
        encryption_type: '', // 消息加密方式: 1-明文模式 2-兼容模式 3-安全模式
        business_domain: '', // 业务域名
        js_secure_domain: '', // JS接口安全域名
        web_auth_domain: '' // 网页授权域名
    }

    // 表单验证
    rules: object = {
        app_id: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        app_secret: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        encryption_type: [{ required: true, message: '必填项不能为空', trigger: 'blur' }]
    }

    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiMPWeChatConfigInfo()
            .then(res => {
                // 表单同步于接口响应数据
                for (const key in res) {
                    if (!this.form.hasOwnProperty(key)) {
                        continue
                    }
                    this.form[key] = res[key]
                }
            })
            .catch(() => {
                this.$message.error('数据加载失败，请刷新重载')
            })
    }

    // 重置表单数据
    onResetFrom() {
        this.initFormData()
        this.$message.info('已重置数据')
    }

    // 提交表单
    onSubmitFrom(formName: string) {
        const refs = this.$refs[formName] as HTMLFormElement

        refs.validate((valid: boolean) => {
            if (!valid) {
                return this.$message.error('请完善信息')
            }
            const loading = this.$loading({ text: '保存中' })
            const params: any = { ...this.form }

            delete params.url
            delete params.business_domain
            delete params.js_secure_domain
            delete params.web_auth_domain

            apiMpWeChatConfigEdit({
                ...params
            })
                .then(() => {
                    this.$message.success('保存成功')
                })
                .catch(() => {
                    this.$message.error('保存失败')
                })
                .finally(() => {
                    loading.close()
                })
        })
    }

    handleCopy(value: string) {
        copyClipboard(value)
            .then(() => {
                this.$message.success('复制成功')
            })
            .catch(err => {
                this.$message.error('复制失败')
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.initFormData()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.channel-mp_wechat-index {
    padding-bottom: 60px;
}

.ls-card {
    .ls-input {
        width: 280px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }

    .form__item-encryption {
        display: flex;
        align-items: center;
        height: 3em;
    }
}
</style>
