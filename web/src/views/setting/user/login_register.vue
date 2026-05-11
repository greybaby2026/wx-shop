<template>
    <div class="login-register">
        <!-- 提示 -->
        <!-- <div class="ls-card">
			<el-alert title="温馨提示：应工信部的要求,请务必填写公安备案号和网站备案号,保存的备案信息,将展示在后台登录页面" type="info" :closable="false"
				show-icon />
		</div> -->

        <el-form ref="formRef" :model="form" label-width="120px" size="small">
            <!-- 注册登录 -->
            <div class="ls-card">
                <div class="card-title">通用设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="注册方式" required>
                        <el-checkbox-group v-model="form.register_way">
                            <el-checkbox label="1">手机号码注册</el-checkbox>
                        </el-checkbox-group>
                        <div class="muted xs">
                            系统通用的注册用户方式，至少勾选一项，微信等渠道有其他授权注册方式，请合理设置
                        </div>
                    </el-form-item>
                    <el-form-item label="手机号码注册需验证码" required>
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.is_mobile_register_code"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.is_mobile_register_code ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            1、用户通过手机号码注册帐号时，是否需要短信验证码验证填写的手机号码，默认关闭
                        </div>
                        <div class="muted xs">2、请开启对应短信验证码功能</div>
                    </el-form-item>
                    <el-form-item label="强制绑定手机">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.coerce_mobile"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.coerce_mobile ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            用户无论通过何种方式注册用户时，是否需要强制绑定手机。绑定手机需要短信验证，请开启对应短信验证码功能
                        </div>
                        <!-- <div class="muted xs">2、强烈建议开启，这样可以统一各渠道的账号。关闭后可能会生成多个账号，账号之间无法合并</div> -->
                    </el-form-item>
                    <el-form-item label="登录方式" required>
                        <!-- 多选框 -->
                        <el-checkbox-group v-model="form.login_way">
                            <el-checkbox label="1">手机号密码登录</el-checkbox>
                            <el-checkbox label="2">手机号验证码登录</el-checkbox>
                        </el-checkbox-group>
                        <div class="muted xs">
                            系统通用的登录方式，至少勾选一项，需要和通用注册方式匹配。
                            微信等渠道有其他授权注册方式，请合理设置
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 微信开放平台 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信开放平台</div>
                <div class="card-content m-t-24">
                    <el-form-item label="微信开放平台">
                        <a class="muted xs item-a" href="https://open.weixin.qq.com/">前往微信开放平台</a>
                        <!-- <div class="muted xs">前往微信开放平台</div> -->
                        <!-- <router-link class="card-title" to="">微信开放平台</router-link> -->
                        <div class="muted xs">1、商城在各渠道使用微信授权登录时，强烈建议配置微信开放平台</div>
                        <div class="muted xs">
                            2、微信开放平台关联公众号、小程序和APP后，可实现各端用户账号统一，识别买家唯一微信身份
                        </div>
                        <div class="muted xs">
                            3、没有配置微信开放平台，同一微信号会生成多个用户，配置微信开放平台后已生成的用户账号无法合并
                        </div>
                        <!-- <div class="muted xs">4、如果不配置微信开放平台，可通过开启注册强制绑定手机，实现用户账号统一效果。</div> -->
                    </el-form-item>
                </div>
            </div>

            <!-- 微信公众号 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信公众号</div>
                <div class="card-content m-t-24">
                    <el-form-item label="微信授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.h5_wechat_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.h5_wechat_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            微信公众号是否支持微信授权登录方式。关闭时，使用注册设置里面的配置方法完成用户注册和登录
                        </div>
                    </el-form-item>
                    <el-form-item label="自动微信授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.h5_auto_wechat_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.h5_auto_wechat_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">微信授权登录开启后，可以勾选是否需要自动授权登录，默认开启。</div>
                    </el-form-item>
                </div>
            </div>

            <!-- 微信小程序 -->
            <div class="ls-card m-t-16">
                <div class="card-title">微信小程序</div>
                <div class="card-content m-t-24">
                    <el-form-item label="微信授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.mnp_wechat_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.mnp_wechat_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            微信小程序是否支持微信授权登录方式。关闭时，使用注册设置里面的配置方法完成用户注册和登录
                        </div>
                    </el-form-item>
                    <el-form-item label="自动微信授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.mnp_auto_wechat_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.mnp_auto_wechat_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">微信授权登录开启后，可以勾选是否需要自动授权登录，默认开启</div>
                    </el-form-item>
                </div>
            </div>
            <!-- 字节小程序 -->
            <div class="ls-card m-t-16" v-show="false">
                <div class="card-title">字节小程序</div>
                <div class="card-content m-t-24">
                    <el-form-item label="字节授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.toutiao_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.toutiao_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            字节小程序是否支持字节授权登录方式。关闭时，使用注册设置里面的配置方法完成用户注册和登录
                        </div>
                    </el-form-item>
                    <el-form-item label="自动字节授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.toutiao_auto_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.toutiao_auto_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">字节授权登录开启后，可以勾选是否需要自动授权登录，默认开启</div>
                    </el-form-item>
                </div>
            </div>
            <!-- H5设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">H5设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="">
                        <div class="muted xs">用户访问H5商城时，使用注册设置里面的配置方法完成用户注册和登录</div>
                    </el-form-item>
                </div>
            </div>

            <!-- APP设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">APP设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="微信授权登录">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.app_wechat_auth"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.app_wechat_auth ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs">
                            APP是否支持微信授权登录方式。关闭时，使用注册设置里面的配置方法完成用户注册和登录
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="setLoginRegister()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import { apiRegisterConfig, apiRegisterConfigSet } from '@/api/setting/user'
@Component({
    components: {}
})
export default class LoginRegister extends Vue {
    /** S Data **/
    form = {
        register_way: [], // 注册方式：1-手机号码注册（注册设置必传）
        login_way: [], //登录方式：1-账号密码登录；2-手机短信验证码登录
        is_mobile_register_code: 0, // 手机号码注册需验证码：0-关闭；1-开启
        coerce_mobile: 0, // 手机号码注册需验证码：0-关闭；1-开启
        h5_wechat_auth: 0, // 微信公众号-微信授权登录：0-关闭；1-开启
        h5_auto_wechat_auth: 1, // 微信公众号-自动微信授权登录:0-关闭；1-开启;
        mnp_wechat_auth: 0, // 小程序-微信授权登录 :0-关闭；1-开启;
        mnp_auto_wechat_auth: 1, // 小程序-自动微信授权登录:0-关闭；1-开启;
        app_wechat_auth: 0, // app-微信授权登录:0-关闭；1-开启;
        scene: '', // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        toutiao_auth: 0,
        toutiao_auto_auth: 0
    }
    /** E Data **/

    @Watch('form.h5_wechat_auth')
    h5WechatAuthWatch(val: any) {
        if (val == 0) {
            this.form.h5_auto_wechat_auth = 0
        }
    }
    @Watch('form.h5_auto_wechat_auth')
    h5AutoWechatAuthWatch(val: any) {
        if (val == 1) {
            this.form.h5_wechat_auth = 1
        }
    }
    @Watch('form.mnp_wechat_auth')
    mnpWechatAuthWatch(val: any) {
        if (val == 0) {
            this.form.mnp_auto_wechat_auth = 0
        }
    }
    @Watch('form.mnp_auto_wechat_auth')
    mnpAutoWechatAuthWatch(val: any) {
        if (val == 1) {
            this.form.mnp_wechat_auth = 1
        }
    }
    @Watch('form.toutiao_auth')
    mnpToutiaoAuthWatch(val: any) {
        if (val == 0) {
            this.form.toutiao_auto_auth = 0
        }
    }
    @Watch('form.mnp_auto_wechat_auth')
    mnpAutoToutiaoAuthWatch(val: any) {
        if (val == 1) {
            this.form.toutiao_auth = 1
        }
    }

    // 获取用户设置
    getLoginRegister() {
        apiRegisterConfig()
            .then((res: any) => {
                // this.$message.success('数据请求成功!')
                this.form = res
            })
            .catch(() => {
                // this.$message.error('数据请求失败!')
            })
    }

    // 修改用户设置登录注册
    setLoginRegister() {
        this.form.scene = 'register' // 场景：user-用户设置；register-注册设置；withdraw-提现设置
        return apiRegisterConfigSet(this.form)
            .then((res: any) => {
                // this.$message.success('保存成功!')
                setTimeout(() => {
                    this.getLoginRegister()
                }, 50)
            })
            .catch(() => {
                // this.$message.error('保存失败!')
            })
    }
    /** S Life Cycle **/
    created() {
        this.getLoginRegister()
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

    .login_limit-unit {
        display: inline-block;
        width: 2em;
        text-align: center;
    }

    .item-a {
        color: #4073fa;
    }
}

.login-register {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
