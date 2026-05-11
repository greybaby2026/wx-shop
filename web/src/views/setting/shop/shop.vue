<template>
    <div class="setting-shop">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="温馨提示：商城名称、商城Logo、默认图片等信息的配置。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <!-- 后台设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">后台设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="店铺/商城名称" prop="name">
                        <el-input class="ls-input" v-model="form.name" maxlength="12" show-word-limit />
                    </el-form-item>
                    <el-form-item label="后台LOGO" prop="logo">
                        <material-select :limit="1" v-model="form.logo" />
                        <div class="flex">
                            <!-- <div class="muted xs m-r-16">建议尺寸：100*100px / 258*100px，支持jpg，jpeg，png格式</div> -->
                            <div class="muted xs m-r-16">建议尺寸：200*200像素/240*60像素，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" width="500" trigger="hover">
                                <el-image :src="images.adminLogo" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                    <el-form-item label="登录封面图" prop="admin_login_image">
                        <material-select :limit="1" v-model="form.admin_login_image" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：500*500像素，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" trigger="hover">
                                <img :src="images.adminLogin" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                    <el-form-item label="站点图标" prop="favicon">
                        <material-select :limit="1" v-model="form.favicon" />
                        <div class="muted xs m-r-16">
                            网站favicon图标设置，建议尺寸：100*100像素，支持jpg，jpeg，png格式
                        </div>
                    </el-form-item>
                    <el-form-item label="登录安全">
                        <el-radio-group v-model="form.login_restrictions" class="m-r-16">
                            <el-radio :label="0">不限制</el-radio>
                            <el-radio :label="1">限制</el-radio>
                        </el-radio-group>
                        <div class="flex m-t-24" v-show="form.login_restrictions">
                            <div>
                                <span class="p-r-6">输错密码</span>
                                <el-input
                                    v-model="form.password_error_times"
                                    type="number"
                                    style="width: 138px"
                                    #append
                                >
                                    <span class="login_limit-unit">次</span>
                                </el-input>
                            </div>

                            <div class="m-l-20">
                                <span class="p-r-6">限制登录</span>
                                <el-input v-model="form.limit_login_time" type="number" style="width: 138px" #append>
                                    <span class="login_limit-unit">分钟</span>
                                </el-input>
                            </div>
                        </div>
                    </el-form-item>

                    <el-form-item label="文档信息">
                        <el-radio-group v-model="form.document_status" class="m-r-16">
                            <el-radio :label="1">显示</el-radio>
                            <el-radio :label="0">隐藏</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">默认开启，控制工作台商城信息的按钮是否显示</div>
                    </el-form-item>
                </div>
            </div>

            <!-- 移动端设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">移动端设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="移动端LOGO" prop="mobile_logo">
                        <material-select :limit="1" v-model="form.mobile_logo" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：200*200像素，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" trigger="hover">
                                <img :src="images.storeLogin" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- PC端设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">PC端设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="PC商城LOGO" prop="pc_logo">
                        <material-select :limit="1" v-model="form.pc_logo" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：200*200像素/240*56像素，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" width="500" trigger="hover">
                                <el-image :src="images.pcLogo" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                </div>
            </div>
            <!-- 经营设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">经营设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="小程序商城">
                        <div class="flex">
                            <el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
                            <span class="m-l-16">
                                {{ form.status ? '开启' : '关闭' }}
                            </span>
                        </div>
                        <div class="flex">
                            <div class="muted xs m-r-16">默认开启，商城状态为关闭时，将不对外提供服务，请谨慎操作</div>
                            <el-popover placement="right" trigger="hover">
                                <el-image :src="images.storeClose" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                </div>
            </div>
            <!-- 联系方式 -->
            <div class="ls-card m-t-16">
                <div class="card-title">联系方式</div>
                <div class="card-content m-t-24">
                    <el-form-item label="姓名" prop="mall_contact">
                        <el-input class="ls-input" v-model="form.mall_contact" show-word-limit />
                        <div class="muted xs">商城联系人：有订单产生时，可短信通知商城联系人</div>
                    </el-form-item>
                    <el-form-item label="手机号码" prop="mall_contact_mobile">
                        <el-input class="ls-input" v-model="form.mall_contact_mobile" show-word-limit />
                        <div class="muted xs">商城联系人手机：有订单产生时，可短信通知商城联系人</div>
                    </el-form-item>
                </div>
            </div>

            <!-- 退货地址 -->
            <!-- <div class="ls-card m-t-16">
                <div class="card-title">退货地址</div>
                <div class="card-content m-t-24">
                    <el-form-item label="姓名" prop="return_contact">
                        <el-input class="ls-input" v-model="form.return_contact" show-word-limit />
                    </el-form-item>
                    <el-form-item label="手机号码" prop="return_contact_mobile">
                        <el-input class="ls-input" v-model="form.return_contact_mobile" show-word-limit />
                    </el-form-item>
                    <el-form-item label="地区" prop="return_district">
                        <area-select
                            width="280px"
                            :province.sync="form.return_province"
                            :city.sync="form.return_city"
                            :district.sync="form.return_district"
                        />
                    </el-form-item>
                    <el-form-item label="详细地址" prop="return_address">
                        <el-input
                            class="ls-input"
                            v-model="form.return_address"
                            type="textarea"
                            :autosize="{ minRows: 3, maxRows: 6 }"
                            show-word-limit
                        />
                    </el-form-item>
                </div>
            </div> -->
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="onResetFrom">重置</el-button> -->
                <el-button size="small" type="primary" @click="onSubmitFrom('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import AreaSelect from '@/components/area-select.vue'
import { apiSettingShopEdit, apiSettingShopInfo } from '@/api/setting/shop'
import { ShopEdit_Req } from '@/api/setting/shop.d.ts'

@Component({
    components: {
        AreaSelect,
        MaterialSelect
    }
})
export default class SettingShop extends Vue {
    /** S Data **/
    // 表单数据
    form: ShopEdit_Req = {
        name: '', // 商城名称
        logo: '', // 商城logo
        admin_login_image: '', // 管理后台登录页图片
        login_restrictions: 0, // 管理后台登录限制: 0-不限制 1-限制
        password_error_times: 0, // 限制密码错误次数
        limit_login_time: 0, // 限制禁止多少分钟不能登录
        status: 0, // 商城状态: 0-关闭 1-开启
        mall_contact: '', // 联系人
        mall_contact_mobile: '', // 联系人手机号
        return_contact: '', // 退货联系人
        return_contact_mobile: '', // 退货联系人手机号
        return_province: 0, // 退货省份id
        return_city: 0, // 退货城市id
        return_district: 0, // 退货区域id
        return_address: '', // 退货详细地址
        favicon: '', // 站点图标
        mobile_logo: '', // 移动端logo
        pc_logo: '', // pclogo
        document_status: 1 // 0-隐藏；1-显示；
    }

    // 图片
    images = {
        storeLogin: require('@/assets/images/setting/img_shili_store_login.png'),
        storeClose: require('@/assets/images/setting/img_shili_store_close.png'),
        adminLogin: require('@/assets/images/setting/img_shili_admin_login.png'),
        adminLogo: require('@/assets/images/setting/admin_logo_example.png'),
        pcLogo: require('@/assets/images/setting/pc_logo_example.png')
    }

    // 手机表单验证
    ruleMobile: Array<object> = [{ required: true, message: '必填项不能为空', trigger: 'blur' }]

    // 地区表单验证方法
    validatorArea: Function = (rule: object, value: string, callback: Function) => {
        if (this.form.return_province) {
            callback()
        } else {
            callback(new Error())
        }
    }

    // 表单验证
    rules: object = {
        name: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        logo: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        admin_login_image: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        favicon: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        mobile_logo: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        pc_logo: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        mall_contact: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        return_contact: [{ required: true, message: '必填项不能为空', trigger: 'blur' }],
        mall_contact_mobile: this.ruleMobile,
        return_contact_mobile: this.ruleMobile,
        return_district: [
            {
                required: true,
                message: '必填项不能为空',
                validator: this.validatorArea,
                trigger: 'blur'
            }
        ],
        return_address: [{ required: true, message: '必填项不能为空', trigger: 'blur' }]
    }
    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiSettingShopInfo()
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
            const params = { ...this.form }

            // 根据需求删除冗余参数
            if (!params.login_restrictions) {
                delete params.limit_login_time
                delete params.password_error_times
            }

            apiSettingShopEdit({
                ...params
            })
                .then(() => {
                    this.$store.dispatch('getConfig')
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
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.initFormData()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.setting-shop {
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

    .login_limit-unit {
        display: inline-block;
        width: 2em;
        text-align: center;
    }
}
</style>
