<!-- 字节小程序 -->
<template>
    <div class="mp-toutiao">
        <!-- 提示 -->
        <div class="ls-card">
            <el-alert
                title="温馨提示：请先前往字节小程序开发平台申请认证字节小程序。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <!-- 主要内容 -->
        <el-form ref="formRef" :model="form" :rules="formRules" label-width="140px" size="small">
            <!-- 微信小程序 -->
            <!-- <div class="ls-card m-t-16">
				<div class="card-title">微信小程序</div>
				<div class="card-content m-t-24">
					<el-form-item label="小程序名称">
						<el-input class="ls-input" v-model="form.name" size="small">
						</el-input>
					</el-form-item>
					<el-form-item label="原始ID">
						<el-input class="ls-input" v-model="form.original_id" size="small"></el-input>
					</el-form-item>
					<el-form-item label="小程序码">
						<material-select :limit="1" v-model="form.qr_code" />
						<div class="flex">
							<div class="muted xs m-r-16">建议尺寸：400*400像素，支持jpg，jpeg，png格式</div>
							<el-popover placement="right" width="200" trigger="hover">
								<el-image
									src="https://img2.baidu.com/it/u=3357699356,1912406716&fm=26&fmt=auto&gp=0.jpg" />
								<el-button slot="reference" type="text">查看示例</el-button>
							</el-popover>
						</div>
					</el-form-item>
				</div>
			</div> -->

            <!-- 开发者ID -->
            <div class="ls-card m-t-16">
                <div class="card-title">开发者ID</div>
                <div class="card-content m-t-24">
                    <el-form-item label="AppID" prop="appid">
                        <el-input class="ls-input" v-model="form.appid" size="small"> </el-input>
                    </el-form-item>
                    <el-form-item label="AppSecret" prop="secret">
                        <el-input class="ls-input" v-model="form.secret" size="small"></el-input>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台，点击开发管理>开发设置->小程序Key，设置AppID和AppSecret
                        </div>
                    </el-form-item>
                    <el-form-item label="SALT" prop="pay_salt">
                        <el-input class="ls-input m-r-10" v-model="form.pay_salt" size="small"> </el-input>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台进入对应的小程序，点击功能管理>支付->担保交易->担保交易设置->支付回调信息配置，设置SALT
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
                            登录字节小程序开发平台进入对应的小程序，点击开发管理>开发设置->服务器域名，填写https协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="socket合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.socket_domain" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.socket_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台进入对应的小程序，点击开发管理>开发设置->服务器域名，填写wss协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="uploadFile合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.upload_file_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.upload_file_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台进入对应的小程序，点击开发管理>开发设置->服务器域名，填写https协议域名
                        </div>
                    </el-form-item>
                    <el-form-item label="downloadFile合法域名">
                        <el-input class="ls-input m-r-10" v-model="form.download_file_domain" size="small" disabled>
                        </el-input>
                        <el-button size="small" @click="onCopy(form.download_file_domain)">复制</el-button>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台进入对应的小程序，点击开发管理>开发设置->服务器域名，填写https协议域名
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
                            登录字节小程序开发平台进入对应的小程序，点击开发管理>开发设置->业务域名，填写业务域名
                        </div>
                    </el-form-item>
                </div>
            </div>
            <!-- 担保交易设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">支付回调信息配置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="URL(服务器地址)">
                        <el-input class="ls-input m-r-10" v-model="form.notify" size="small" disabled> </el-input>
                        <el-button size="small" @click="onCopy(form.notify)">复制</el-button>
                        <div class="muted xs m-r-16">
                            登录字节小程序开发平台进入对应的小程序，点击功能管理>支付->担保交易->担保交易设置->支付回调信息配置，填写URL(服务器地址)
                        </div>
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
import { apiToutiaoSetting, apiToutiaoSettingSet } from '@/api/channel/mp_toutiao'
import { copyClipboard } from '@/utils/util'
@Component({
    components: {
        MaterialSelect
    }
})
export default class WechatApp extends Vue {
    /** S Data **/
    form = {
        name: '', // 小程序名称
        original_id: '', // 原始id
        qr_code: '', // 二维码
        appid: '',
        secret: '',
        pay_salt: '',
        request_domain: '', // request合法域名
        socket_domain: '', // socket合法域名
        upload_file_domain: '', // uploadFile合法域名
        download_file_domain: '', // downloadFile合法域名
        udp_domain: '', // udp合法域名
        business_domain: '', // 业务域名
        url: '',
        token: '',
        encoding_aes_key: '',
        encryption_type: 1, // 消息加密方式 1-明文模式 2-兼容模式 3-安全模式
        data_format: 1 // 数据格式 1-JSON 2-XML
    }

    // 表单验证
    formRules = {
        appid: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: 'blur'
            }
        ],
        secret: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: 'blur'
            }
        ],
        pay_salt: [
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
        apiToutiaoSetting()
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
            apiToutiaoSettingSet(this.form)
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

.mp-toutiao {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
