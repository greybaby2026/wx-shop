<!-- 微信小程序 -->
<template>
    <div class="wechat_app">
        <!-- 提示 -->
        <div class="ls-card">
            <el-alert
                title="温馨提示：请先前往微信小程序后台申请认证微信小程序。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <!-- 主要内容 -->
        <el-form ref="formRef" :model="form" :rules="formRules" label-width="140px" size="small">
            <!-- 微信小程序 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信小程序</div>
                <div class="card-content m-t-24">
                    <el-form-item label="小程序名称">
                        <el-input class="ls-input" v-model="form.name" size="small"> </el-input>
                    </el-form-item>
                    <el-form-item label="原始ID">
                        <el-input class="ls-input" v-model="form.original_id" size="small"></el-input>
                    </el-form-item>
                    <el-form-item label="小程序码">
                        <material-select :limit="1" v-model="form.qr_code" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：400*400像素，支持jpg，jpeg，png格式</div>
                            <!-- <el-popover placement="right" width="200" trigger="hover">
								<el-image
									src="https://img2.baidu.com/it/u=3357699356,1912406716&fm=26&fmt=auto&gp=0.jpg" />
								<el-button slot="reference" type="text">查看示例</el-button>
							</el-popover> -->
                        </div>
                    </el-form-item>
                    <el-form-item label="发货信息管理">
                        <el-radio-group v-model="form.express_send_sync" class="m-r-16">
                            <el-radio :label="1">开启</el-radio>
                            <el-radio :label="0">关闭</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">
                            小程序有订单发货管理时，请打开此开关，否则会导致订单资金冻结。
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 开发者ID -->
            <div class="ls-card m-t-16">
                <div class="card-title">开发者ID</div>
                <div class="card-content m-t-24">
                    <el-form-item label="AppID" prop="app_id">
                        <el-input class="ls-input" v-model="form.app_id" size="small"> </el-input>
                    </el-form-item>
                    <el-form-item label="AppSecret" prop="app_secret">
                        <el-input class="ls-input" v-model="form.app_secret" size="small"></el-input>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->开发者ID，设置AppID和AppSecret
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 服务器域名 -->
            <div class="ls-card m-t-16">
                <div class="card-title">服务器域名</div>
                <div class="card-content m-t-24">
                    <el-form-item label="request合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.request_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.request_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写https协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="socket合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.socket_domain" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.socket_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写wss协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="uploadFile合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.upload_file_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.upload_file_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写https协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="downloadFile合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.download_file_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.download_file_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写https协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="udp合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.udp_domain" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.udp_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写udp协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="tpc合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.tcp_domain" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.tcp_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->服务器域名，填写tcp协议域名
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 业务域名 -->
            <div class="ls-card m-t-16">
                <div class="card-title">业务域名</div>
                <div class="card-content m-t-24">
                    <el-form-item label="业务域名">
                        <el-input class="ls-input m-r-10" v-model="form.business_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.business_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            小程序账号登录微信公众平台，点击开发>开发设置->业务域名，填写业务域名
                        </div>
                    </el-form-item>
                </div>
            </div>
            <!-- 一健上传 -->
            <div class="ls-card m-t-16">
                <div class="card-title">小程序代码上传</div>
                <div class="card-content m-t-24">
                    <el-form-item label="代码上传密钥">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.private_key"
                            size="small"
                            type="textarea"
                            rows="4"
                        ></el-input>
                        <div class="muted xs m-r-16">微信公众平台-开发-开发管理-开发设置-小程序代码上传</div>
                    </el-form-item>
                </div>
            </div>
            <!-- 消息推送 -->
            <!-- <div class="ls-card m-t-16">
				<div class="card-title">消息推送</div>
				<div class="card-content m-t-24">
					<el-form-item label="URL">
						<el-input class="ls-input m-r-10" v-model="form.url" size="small" disabled></el-input>
						<el-button size="small" @click="onCopy(form.url)">复制</el-button>
						<div class="muted xs m-r-16">
							小程序账号登录微信公众平台，点击开发>开发设置>消息推送配置，填写服务器地址（URL）
						</div>
					</el-form-item>
					<el-form-item label="Token">
						<el-input class="ls-input m-r-10" v-model="form.token" size="small"></el-input>
						<div class="muted xs m-r-16">
							小程序账号登录微信公众平台，点击开发>开发设置>消息推送配置，设置令牌Token。不填默认为“LikeMall”
						</div>
					</el-form-item>
					<el-form-item label="EncodingAESKey">
						<el-input class="ls-input m-r-10" v-model="form.encoding_aes_key" size="small"></el-input>
						<div class="muted xs m-r-16">
							消息加密密钥由43位字符组成，字符范围为A-Z,a-z,0-9
						</div>
					</el-form-item>
					<el-form-item label="消息加密方式">
						<div>
							<el-radio class="lighter" :label="1" v-model="form.encryption_type">
								明文模式(不使用消息体加解密功能，安全系数较低)
							</el-radio>
						</div>
						<div>
							<el-radio class="lighter" :label="2" v-model="form.encryption_type">
								兼容模式(明文、密文将共存，方便开发者调试和维护)
							</el-radio>
						</div>
						<div>
							<el-radio class="lighter" :label="3" v-model="form.encryption_type">
								安全模式（推荐）(消息包为纯密文，需要开发者加密和解密，安全系数高)
							</el-radio>
						</div>
					</el-form-item>
					<el-form-item label="数据格式">
						<el-radio-group class="m-r-16 " v-model="form.data_format">
							<el-radio :label="1" class="muted">JSON</el-radio>
							<el-radio :label="2" class="muted">XML</el-radio>
						</el-radio-group>
					</el-form-item>
				</div>
			</div> -->
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="putWechatAppSetting()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiWechatMiniSetting, apiWechatMiniSettingSet } from '@/api/channel/wechat_app'
import { WechatMiniSetting_Res, WechatMiniSetting_Req } from '@/api/channel/wechat_app.d'
import { copyClipboard } from '@/utils/util'
@Component({
    components: {
        MaterialSelect
    }
})
export default class WechatApp extends Vue {
    /** S Data **/
    form: WechatMiniSetting_Res = {
        name: '', // 小程序名称
        original_id: '', // 原始id
        qr_code: '', // 二维码
        app_id: '',
        app_secret: '',
        request_domain: '', // request合法域名
        socket_domain: '', // socket合法域名
        upload_file_domain: '', // uploadFile合法域名
        download_file_domain: '', // downloadFile合法域名
        udp_domain: '', // udp合法域名
        tcp_domain: '',
        business_domain: '', // 业务域名
        url: '',
        token: '',
        encoding_aes_key: '',
        encryption_type: 1, // 消息加密方式 1-明文模式 2-兼容模式 3-安全模式
        data_format: 1, // 数据格式 1-JSON 2-XML
        express_send_sync: 0,
        private_key: '' //上传密钥
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
        ]
    }

    $refs!: {
        formRef: any
    }
    /** E Data **/

    // 获取APP设置
    getWechatAppSetting() {
        apiWechatMiniSetting()
            .then((res: any) => {
                this.form = res
                // this.$message.success('获取数据成功!')
            })
            .catch(() => {
                //this.$message.error('获取数据失败!')
            })
    }

    // 修改APP设置
    putWechatAppSetting() {
        this.$refs.formRef.validate((valid: boolean) => {
            // 预校验
            if (!valid) {
                return this.$message.error('请完善信息')
            }
            apiWechatMiniSettingSet(this.form)
                .then((res: any) => {
                    this.getWechatAppSetting()
                    //this.$message.success('保存成功!')
                })
                .catch(() => {
                    //this.$message.error('保存失败!')
                })
        })
    }
    // 复制到剪贴板
    onCopy(value: string) {
        copyClipboard(value)
            .then(() => {
                this.$message.success('复制成功')
            })
            .catch(err => {
                this.$message.error('复制失败')
            })
    }

    /** S Life Cycle **/
    created() {
        this.getWechatAppSetting()
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

.wechat_app {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
