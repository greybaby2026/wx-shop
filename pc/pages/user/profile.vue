<template>
    <div class="user-profile">
        <div class="user-header lg">
            个人资料
        </div>
        <div class="user-container">
            <div class="user-form-item flex">
                <label class="user-form-label nr">头像</label>
                <div class="user-avatar-upload">
                    <el-upload class="avatar-uploader" :headers="{'version':config.version,'token': $store.state.token}" :action="action" :show-file-list="false" :file-list="fileList"
                        :on-success="uploadFileSuccess" 
                        accept="image/jpg,image/jpeg,image/png"
                        :before-upload="beforeAvatarUpload">
                        <div class="avatar">
                            <el-image style="width: 64px;height: 64px;border-radius: 60px" :src="userInfo.avatar" />
                            <div class="mask white"></div>
                        </div>
                    </el-upload>
                </div>
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">用户ID</label>
                <div class="normal nr">{{userInfo.sn}}</div>
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">昵称</label>
                <el-input v-model="userInfo.nickname" class="user-input" suffix-icon="el-icon-edit" />
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">性别</label>
                <el-radio-group v-model="radio">
                    <el-radio :label="1">男</el-radio>
                    <el-radio :label="2">女</el-radio>
                    <!-- <el-radio :label="3">保密</el-radio> -->
                </el-radio-group>
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">手机号</label>
                <div class="normal nr">{{userInfo.mobile}}</div>
                <div style="color: #6699CC;margin-left: 13px;cursor: pointer;" @click="showChangeNumber = true">修改号码</div>
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">注册时间</label>
                <div class="normal nr">{{userInfo.create_time}}</div>
            </div>
            <div class="user-form-item flex">
                <label class="user-form-label nr">登录密码</label>
                <div class="nr" style="color: #6699CC;cursor: pointer;" @click="showPwdPop=true">{{userInfo.has_password =='未设置'?'设置密码':'修改密码'}}</div>
            </div>
            <button class="primary-btn flex row-center white" @click="saveUserInfo">
                保存
            </button>
        </div>

        <el-dialog :title="userInfo.mobile ? '更换手机号': '绑定手机'" :visible.sync="showChangeNumber" width="40%">
            <div>
                <el-form style="width: 50%;margin: 0 auto">
                    <el-form-item>
                        <el-input v-model="telephone" placeholder="请输入新的手机号码" />
                    </el-form-item>
                    <el-form-item>
                        <div class="flex">
                            <el-input v-model="verifyCode" placeholder="短信验证码" />
                            <el-button style="margin-left: 14px;" @click="sndSmsToPhone">
                                <div v-if="canSendNumber">获取验证码</div>
                                <count-down v-else :time="60" format="ss秒" autoStart @finish="canSendNumber = true" />
                            </el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
            <div slot="footer">
                <el-button type="primary" @click="changeUserMobile">确认</el-button>
                <el-button @click="closeChangeNumber">取消</el-button>
            </div>
        </el-dialog>
        <el-dialog title="设置登录密码" :visible.sync="showPwdPop" width="40%">
            <div>
                <el-form style="width: 50%;margin: 0 auto;">
                    <el-form-item>
                        <el-input v-model="userInfo.mobile" placeholder="请输入手机号码" disabled />
                    </el-form-item>
                    <el-form-item>
                        <div class="flex">
                            <el-input v-model="verifyCode" placeholder="短信验证码" />
                            <el-button style="margin-left: 14px;" @click="sndSmsToPhone">
                                <div v-if="canSendPwd">获取验证码</div>
                                <count-down v-else :time="60" format="ss秒" autoStart @finish="canSendPwd = true" />
                            </el-button>
                        </div>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="pwd" type="password" placeholder="请输入密码 (数字与字母自由组合)" />
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="againPwd" type="password" placeholder="再次输入密码" />
                    </el-form-item>
                </el-form>
            </div>
            <div slot="footer">
                <el-button type="primary" @click="setPassWord">确认</el-button>
                <el-button @click="closePwdPop">取消</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import { SMSType, client, FieldType } from "~/utils/type";
import CountDown from "~/components/countDown";
import Cookies from "js-cookie";
import { mapActions } from "vuex";
import config from "~/config/app.js";
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    components: {
        CountDown,
    },
    async asyncData({ $get, $post }) {
        let userInfo = {}, radio = ''
        let res = await $get("user/info");
        if (res.code == 1) {
            userInfo = res.data
            if(res.data.sex == '男') 
                radio = 1
            else if(res.data.sex == '女')
                radio = 2
            else radio = 0
        }
        return {
            userInfo,
            radio
        };
    },
    data() {
        return {
            config: config,
            action: config.config.baseUrl + "/shopapi/Upload/image",
            radio: 1,
            showChangeNumber: false,
            showPwdPop: false,
            telephone: "",
            verifyCode: "",
            pwd: "",
            againPwd: "",
            canSendNumber: true,
            canSendPwd: true,
            fileList: [],
        };
    },
    methods: {
        // 图片上传限制
        beforeAvatarUpload(file) {
          var testmsg = file.name.substring(file.name.lastIndexOf('.') + 1)
          console.log('fdsadsf')
          const extension = testmsg === 'jpg'
          const extension2 = testmsg === 'png'
          const extension4 = testmsg === 'jpeg'
          if (!extension && !extension2) {
            this.$message({
              message: '上传文件只能是 jpg, jpeg, png格式!',
              type: 'warning'
            })
            return false
          }
          return extension || extension2 || extension4
        },
        
        ...mapActions(["getPublicData"]),
        async saveUserInfo() {
            let res = await this.$post("pc/setUserInfo", {
                sex: this.radio,
                nickname: this.userInfo.nickname,
            });
            if (res.code == 1) {
                this.$message.success(res.msg)
                this.getPublicData();
            }
        },
        closeChangeNumber() {
            this.telephone = "";
            this.verifyCode = "";
            this.showChangeNumber = false;
        },
        closePwdPop() {
            this.telephone = "";
            this.verifyCode = "";
            this.showPwdPop = false;
        },
        async sndSmsToPhone() {
            if (this.showChangeNumber == true && !this.telephone) return this.$message.error('请输入手机号')
            let res = '';
            this.showChangeNumber == true
                ? res = await this.$post("user/bindMobileCaptcha", {
                    mobile: this.telephone,
                    key: this.smsType,
                  })
                : res = await this.$post("user/resetPasswordCaptcha", {
                    mobile: this.userInfo.mobile,
                    key: this.smsType,
                  })
            if (res.code == 1) {
                this.showChangeNumber == true
                    ? (this.canSendNumber = false)
                    : (this.canSendPwd = false);
                this.$message.success("发送成功")
            }
        },
        async changeUserMobile() {
            if (!this.telephone) return this.$message.error("请输入新的手机号码")
            if (!this.verifyCode) return this.$message.error("请输入验证码")
            let res = await this.$post("user/bindMobile", {
                mobile: this.telephone,
                code: this.verifyCode,
                client: client,
            });
            if (res.code == 1) {
                this.showChangeNumber = false;
                this.$message.success(res.msg);
                this.getPublicData();
            }
        },
        async setPassWord() {
            if (!this.verifyCode) return this.$message.error("请输入验证码")
            if (!this.pwd) return this.$message.error("请输入密码")
            if (!this.againPwd) return this.$message.error("请输入确认密码");
            if (this.pwd != this.againPwd) return this.$message.error("两次密码输入不一致");
                
            let res = await this.$post("user/resetPassword", {
                mobile: this.userInfo.mobile,
                code: this.verifyCode,
                password: this.pwd,
                client: client,
            });
            if (res.code == 1) {
                this.$message.success('修改成功')
                this.showPwdPop = false;
                this.$store.state.token = null;
                Cookies.remove('token')
                this.$router.replace('/account/login')
            }
        },
        async uploadFileSuccess(res, fileList) {
            let respond = await this.$post("user/setInfo", {
                field: FieldType.AVATAR,
                value: res.data.uri,
            });
            if (respond.code == 1) {
                this.$message.success(respond.msg)
                let userRes = await this.$get("user/info");
                if (userRes.code == 1) {
                    this.userInfo = userRes.data;
                    this.radio = this.userRes.data.sex;
                }
            }
        },
    },
};
</script>

<style lang="scss">
.user-profile {
    padding: 10px;
    .user-header {
        padding: 10px 5px;
        border-bottom: 1px solid #e5e5e5;
    }
    .user-container {
        margin-top: 35px;
        .user-form-item {
            padding-left: 13px;
            margin-top: 24px;
            .user-form-label {
                width: 60px;
                text-align: left;
                margin-right: 24px;
            }
            .user-avatar-upload {
                .avatar-uploader {
                    &:hover {
                        .avatar {
                            .mask {
                                display: flex;
                            }
                            &::after {
                                opacity: 1;
                            }
                        }
                    }
                    .avatar {
                        position: relative;
                        .mask {
                            display: none;
                            position: absolute;
                        }
                        &::after {
                            content: "更换头像";
                            position: absolute;
                            transition: opacity 0.3s ease;
                            opacity: 0;
                            width: 100%;
                            height: 64px;
                            left: 0;
                            top: 0;
                            border-radius: 60px;
                            background-color: rgba(
                                $color: #000000,
                                $alpha: 0.3
                            );
                            color: white;
                            display: flex;
                            flex-direction: row;
                            justify-content: center;
                            align-items: center;
                            font-size: 12px;
                        }
                    }
                }
            }
            .user-input {
                width: 240px;
            }
            .el-radio__input.is-checked + .el-radio__label {
                color: #007aff;
            }
            .el-input__inner:focus {
                border-color: #007aff;
            }
            .el-radio__input.is-checked .el-radio__inner {
                border-color: #007aff;
                background: #007aff;
            }
            .el-radio__inner:hover {
                border-color: #007aff;
            }
        }
        .primary-btn {
            background-color: $--color-primary;
            height: 32px;
            width: 100px;
            margin-top: 32px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            &:focus {
                border: none;
                outline: none;
            }
        }
    }
}
</style>