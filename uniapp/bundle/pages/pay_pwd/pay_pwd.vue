<template>
    <view :class="themeName">
        <view class="box bg-white m-t-20">
            <!-- 设置支付密码 -->
            <template v-if="type == 1">
                <u-field label-width="160" v-model="password" label="交易密码" placeholder="请输入交易密码">
                </u-field>

                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="请再次确认密码">
                </u-field>
            </template>

            <!-- 修改支付密码 -->
            <template v-if="type == 2">
                <u-field label-width="160" v-model="original" label="原密码" placeholder="请输入原来的交易密码">
                </u-field>

                <u-field label-width="160" v-model="password" label="交易密码" placeholder="请输入新的交易密码">
                </u-field>

                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="请再次确认密码">
                </u-field>

                <view class="flex row-right normal sm m-t-30 m-r-15" @click="type = 3">忘记密码？</view>
            </template>

            <!-- 忘记支付密码 -->
            <template v-if="type == 3">
                <u-field label-width="160" v-model="mobile" label="手机号">
                </u-field>

                <u-field label-width="160" v-model="code" label="短信验证码" placeholder="请输入验证码">
                    <u-button :ripple="true" ripple-bg-color="#909399" :plain="true" shape="circle" size="mini"
                        slot="right" @click="getCode" :custom-style="{'color': themeColor}">
                        {{codeText}}
                    </u-button>
                </u-field>

                <u-field label-width="160" v-model="password" label="新密码" placeholder="请输入新的交易密码">
                </u-field>

                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="请再次确认密码">
                </u-field>
            </template>
            
            <!-- 绑定手机号码 -->
            <template v-if="type == 4">
                <u-field label-width="160" v-model="newMobile" label="手机号" placeholder="请输入手机号码">
                </u-field>
            
                <u-field label-width="160" v-model="code" label="短信验证码" placeholder="请输入验证码">
                    <u-button :ripple="true" ripple-bg-color="#909399" :plain="true" shape="circle" size="mini"
                        slot="right" @click="getCode" :custom-style="{'color': themeColor}">
                        {{codeText}}
                    </u-button>
                </u-field>
            </template>
            
            <!-- 更换手机号 -->
            <template v-if="type == 5">
                <u-field label-width="160" v-model="mobile" label="手机号" disabled>
                </u-field>
            
                <u-field label-width="160" v-model="newMobile" label="新号码" placeholder="请输入新的手机号码">
                </u-field>
                
                <u-field label-width="160" v-model="code" label="短信验证码" placeholder="请输入验证码">
                    <u-button :ripple="true" ripple-bg-color="#909399" :plain="true" shape="circle" size="mini"
                        slot="right" @click="getCode" :custom-style="{'color': themeColor}">
                        {{codeText}}
                    </u-button>
                </u-field>
            </template>
            
            <!-- 设置登录密码 -->
            <template v-if="type == 6">            
                <u-field label-width="160" v-model="password" label="新密码" placeholder="请输入密码">
                </u-field>
            
                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="请再次确认密码">
                </u-field>
            </template>
            
            <!-- 修改登录密码 -->
            <template v-if="type == 7">
                <u-field label-width="160" v-model="original" label="原密码" placeholder="请输入原来的密码">
                </u-field>
            
                <u-field label-width="160" v-model="password" label="新密码" placeholder="6-20位数字+字母或符号组合">
                </u-field>
            
                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="再次输入新密码确认">
                </u-field>
            </template>
            
            <!-- 忘记登录密码 -->
            <template v-if="type == 8">
                <u-field label-width="160" v-model="mobile" label="手机号">
                </u-field>
            
                <u-field label-width="160" v-model="code" label="短信验证码" placeholder="请输入验证码">
                    <u-button :ripple="true" ripple-bg-color="#909399" :plain="true" shape="circle" size="mini"
                        slot="right" @click="getCode" :custom-style="{'color': themeColor}">
                        {{codeText}}
                    </u-button>
                </u-field>
            
                <u-field label-width="160" v-model="password" label="新密码" placeholder="6-20位数字+字母或符号组合">
                </u-field>
            
                <u-field label-width="160" v-model="confirm_password" label="确认密码" placeholder="再次输入新密码确认">
                </u-field>
            </template>

            <view class="btn flex row-center white lg br60" @click="confirm">确认</view>
        </view>
    </view>
</template>

<script>
    import {
        setPassword, //设置/修改交易密码
        apiresetPayPasswordCaptcha,// 发送验证码-找回支付密码
        apiresetPayPassword,//重置支付密码
        apiBindMobileCaptcha,//发送验证码-绑定手机号码
        apiBindMobile,//绑定手机号
        apiSetPassword,//设置登录密码
        apiChangePassword,//修改登录密码
        apiResetPassword,//重置登录密码
        apiResetPasswordCaptcha,//发送验证码 重置登录密码
    } from "@/api/user.js"
    export default {
        data() {
            return {
                type: 3,
                flag: false, //是否以及获取了验证码
                codeText: '获取验证码',
                code: '', //验证码
                original: '', //原密码
                password: '', //密码
                confirm_password: '' ,//确认密码
                newMobile: '',//新手机号
            }
        },

        computed: {
            mobile() {
                if (this.userInfo.mobile) {
                    return '+86 ' + this.userInfo.mobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')
                }
            },
        },

        methods: {
            getCode() {
                if(this.flag === false) {
                    let number = 60;
                    this.flag = true;
                    let mobile = this.userInfo.mobile
                    
                    if(this.type == 3) {
                        apiresetPayPasswordCaptcha({mobile}).then(res => {
                            let timer = setInterval(() => {
                                if(number >= 1) {
                                    this.codeText = (number-=1) + ''
                                } else {
                                    this.flag = false;
                                    this.codeText = '获取验证码'
                                    clearTimeout(timer)
                                }
                            }, 1000)
                        }).catch(err => {
                            this.flag = false;
                        })
                    } else if(this.type == 4 || this.type == 5) {
                        let phone = this.newMobile
                        apiBindMobileCaptcha({mobile:phone}).then(res => {
                            let timer = setInterval(() => {
                                if(number >= 1) {
                                    this.codeText = (number-=1) + ''
                                } else {
                                    this.flag = false;
                                    this.codeText = '获取验证码'
                                    clearTimeout(timer)
                                }
                            }, 1000)
                        }).catch(err => {
                            this.flag = false;
                        })
                    } else if(this.type == 8) {
                        apiResetPasswordCaptcha({mobile}).then(res => {
                            let timer = setInterval(() => {
                                if(number >= 1) {
                                    this.codeText = (number-=1) + ''
                                } else {
                                    this.flag = false;
                                    this.codeText = '获取验证码'
                                    clearTimeout(timer)
                                }
                            }, 1000)
                        }).catch(err => {
                            this.flag = false;
                        })
                    }
                } else {
                    return false;
                }
            },

            confirm() {
                if(this.type == 1 || this.type == 2 || this.type == 3 || this.type == 6 || this.type == 7) {
                    if (this.password == '') return this.$toast({
                        title: '请输入密码'
                    })
                    if (this.password !== this.confirm_password) return this.$toast({
                        title: '两次输入的密码不一致'
                    })
                }
                
                if(this.type == 3 || this.type == 8 || this.type == 4||this.type == 5) {
                    if (this.code == '') return this.$toast({
                        title: '请输入验证码'
                    })
                }
                
                if(this.type == 2 || this.type == 7) {
                    if (this.original == '') return this.$toast({
                        title: '请输入原密码'
                    })
                }
                
                if (this.type == 1) {
                    setPassword({
                        pay_password: this.password
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 2) {
                    
                    setPassword({
                        origin_pay_password: this.original,
                        pay_password: this.password,
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 3) {
                    apiresetPayPassword({
                        code: this.code,
                        mobile: this.userInfo.mobile,
                        pay_password: this.password
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 4||this.type == 5) {
                    let mobile = this.newMobile;
                    apiBindMobile({code: this.code, mobile: mobile}).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 6) {
                    apiSetPassword({
                        password: this.password
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 7) {
                    apiChangePassword({
                        old_password: this.original,
                        password: this.password,
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                } else if (this.type == 8) {
                    apiResetPassword({
                        code: this.code,
                        mobile: this.userInfo.mobile,
                        password: this.password
                    }).then(res => {
                        setTimeout(() => {
                            uni.navigateBack(1)
                        }, 1000)
                    })
                }
            }
        },

        onLoad() {
            const options = this.$Route.query;
            this.type = options.type;
            if (this.type == 1) {
                uni.setNavigationBarTitle({
                    title: '设置支付密码'
                })
            } else if (this.type == 2) {
                uni.setNavigationBarTitle({
                    title: '修改支付密码'
                })
            } else if (this.type == 3) {
                uni.setNavigationBarTitle({
                    title: '忘记支付密码'
                })
            } else if (this.type == 4) {
                uni.setNavigationBarTitle({
                    title: '绑定手机号'
                })
            } else if (this.type == 5) {
                uni.setNavigationBarTitle({
                    title: '更换手机号'
                })
            } else if (this.type == 6) {
                uni.setNavigationBarTitle({
                    title: '设置登录密码'
                })
            } else if (this.type == 7) {
                uni.setNavigationBarTitle({
                    title: '修改登录密码'
                })
            } else if (this.type == 8) {
                uni.setNavigationBarTitle({
                    title: '重置登录密码'
                })
            }
        }
    }
</script>

<style lang="scss">
    .box {
        padding: 10rpx 10rpx;
        border-radius: 20rpx;
    }

    .btn {
        height: 84rpx;
        margin: 85rpx 20rpx 85rpx 20rpx;
        @include background_color();
    }
</style>
