<template>
    <div class="channel-mp_wechat-menu">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="温馨提示：点击保存并发布菜单后，菜单才会发布至微信公众号，需提前设置好公众号相关配置。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <div class="m-t-16" style="display: flex">
            <!-- 效果预览 -->
            <div class="mp_wechat__phone">
                <div class="mp_wechat__phone-menu mp_wechat__phone-active">
                    <div class="mp_wechat__phone-menu-item" v-for="(item, index) in form.menu" :key="index">
                        <div class="mp_wechat__phone-menu-item--title">
                            <!-- <i
                                class="el-icon-arrow-down"
                                v-show="form.menu[index].has_menu"
                            ></i> -->
                            <span>{{ item.name }}</span>
                        </div>
                        <div class="mp_wechat__phone-submenu" v-show="form.menu[index].has_menu">
                            <div
                                class="mp_wechat__phone-submenu-item"
                                v-for="(item, index) in form.menu[index].sub_button"
                                :key="index"
                            >
                                {{ item.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 菜单配置 -->
            <div class="ls-card m-l-16 mp_wechat__form">
                <div class="mp_wechat__form--title">菜单配置</div>
                <div class="m-t-16">
                    <el-button type="primary" plain size="small" @click="onMenuAdd">
                        <i class="el-icon-plus"></i>
                        <span>新增主菜单（{{ form.menu.length || 0 }}/3）</span>
                    </el-button>
                </div>
                <div class="mp_wechat__form--content m-t-24">
                    <div class="menu-item" v-for="(item, index) in form.menu" :key="index">
                        <div class="menu-item__delete">
                            <!-- 删除菜单 -->
                            <ls-dialog class="m-l-10 inline" top="35vh" @confirm="handleMenuDel(index)">
                                <!-- <div slot="trigger" class="menu-item__delete-btn">删除</div> -->
                                <i slot="trigger" class="el-icon-delete primary pointer"></i>
                            </ls-dialog>
                        </div>

                        <el-form ref="form" :model="form.menu[index]" :rules="rules" label-position="top" size="small">
                            <!-- 主菜单名称 -->
                            <el-form-item label="主菜单" prop="name">
                                <el-input class="ls-input" v-model="form.menu[index].name" show-word-limit />
                            </el-form-item>

                            <!-- 主菜单类型 -->
                            <el-form-item label="主菜单类型">
                                <el-radio-group v-model="form.menu[index].has_menu">
                                    <el-radio :label="false">不配置子菜单</el-radio>
                                    <el-radio :label="true">配置子菜单</el-radio>
                                </el-radio-group>
                            </el-form-item>

                            <!-- 没有子菜单 -->
                            <div v-show="!form.menu[index].has_menu">
                                <MPWechatMenuForm
                                    mode="index"
                                    :url.sync="form.menu[index].url"
                                    :appid.sync="form.menu[index].appid"
                                    :type.sync="form.menu[index].type"
                                    :pagepath.sync="form.menu[index].pagepath"
                                />
                            </div>

                            <!-- 存在子菜单 -->
                            <div v-show="form.menu[index].has_menu">
                                <ul>
                                    <li
                                        class="flex"
                                        v-for="(subItem, subIndex) in form.menu[index].sub_button"
                                        :key="subIndex"
                                        style="padding: 8px"
                                    >
                                        <span style="margin-right: auto">{{ subItem.name }}</span>
                                        <!-- 编辑子菜单 -->
                                        <ls-dialog
                                            top="25vh"
                                            class="inline"
                                            title="子菜单"
                                            :async="true"
                                            :disabled="true"
                                            :clickModalClose="false"
                                            :ref="`SubMenuDialogEdit-${index}-${subIndex}`"
                                            @close="$refs[`SubMenuFormEdit-${index}-${subIndex}`][0].resetFrom()"
                                            @confirm="onSubMenuEdit(index, subIndex)"
                                        >
                                            <MPWechatMenuForm
                                                :name="subItem.name"
                                                :url="subItem.url"
                                                :appid="subItem.appid"
                                                :type="subItem.type"
                                                :pagepath="subItem.pagepath"
                                                :ref="`SubMenuFormEdit-${index}-${subIndex}`"
                                            />
                                            <i
                                                class="el-icon-edit-outline pointer"
                                                slot="trigger"
                                                @click="$refs[`SubMenuDialogEdit-${index}-${subIndex}`][0].open()"
                                            ></i>
                                        </ls-dialog>
                                        <!-- 删除子菜单 -->
                                        <ls-dialog
                                            class="m-l-10 inline"
                                            top="35vh"
                                            @confirm="handleSubMenuDel(index, subIndex)"
                                        >
                                            <i class="el-icon-delete m-l-16 pointer" slot="trigger"></i>
                                        </ls-dialog>
                                    </li>
                                </ul>

                                <ls-dialog
                                    top="25vh"
                                    class="inline"
                                    :async="true"
                                    :disabled="true"
                                    :clickModalClose="false"
                                    title="子菜单"
                                    :ref="`SubMenuDialogAdd-${index}`"
                                    @confirm="handleSubMenuAdd(form.menu[index], index)"
                                >
                                    <MPWechatMenuForm :ref="`SubMenuFormAdd-${index}`" />
                                    <el-button
                                        type="text"
                                        size="small"
                                        slot="trigger"
                                        @click="$refs[`SubMenuDialogAdd-${index}`][0].open()"
                                        >添加子菜单（{{ form.menu[index].sub_button.length || 0 }}/5）</el-button
                                    >
                                </ls-dialog>
                            </div>
                        </el-form>
                    </div>
                </div>
            </div>
        </div>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="initMPWeChatMenuData">重置</el-button> -->
                <el-button size="small" type="normal" @click="onFromSave">保存</el-button>
                <el-button size="small" type="primary" @click="onFromPublish">保存并发布</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiMpWeChatMenuDetail, apiMpWeChatMenuSave, apiMpWeChatMenuPublish } from '@/api/channel/mp_wechat'
import { MPWeChatMenu } from '@/api/channel/mp_wechat.d.ts'
import LsDialog from '@/components/ls-dialog.vue'
import MPWechatMenuForm from '@/components/channel/mp-wechat/menu-form.vue'

interface MenuItem extends MPWeChatMenu {
    has_menu: boolean
}

interface MenuFrom {
    menu: Array<MenuItem>
}

@Component({
    components: {
        LsDialog,
        MPWechatMenuForm
    }
})
export default class MPWechatMenu extends Vue {
    /** S Data **/
    form: MenuFrom = {
        menu: []
    }

    rules = {
        name: [
            {
                required: true,
                message: '必填项不能为空',
                trigger: ['blur', 'change']
            },
            {
                min: 1,
                max: 12,
                message: '长度限制12个字符',
                trigger: ['blur', 'change']
            }
        ]
    }

    /** E Data **/

    /** S Methods **/
    // 添加主菜单
    onMenuAdd() {
        if (this.form.menu.length >= 3) {
            return this.$message.info('主菜单仅限有3项!')
        }
        this.form.menu.push({
            name: '',
            type: '',
            has_menu: false,
            key: '', // 关键字
            url: '', // 网页URL
            appid: '', // 小程序AppID
            pagepath: '', // 小程序路径
            sub_button: [] // 二级菜单
        })
    }

    // 添加子菜单
    handleSubMenuAdd(menu: any, index: number) {
        const refForm: any = this.$refs[`SubMenuFormAdd-${index}`]
        const refDialog: any = this.$refs[`SubMenuDialogAdd-${index}`]

        const { valid, data } = refForm[0].validateForm()
        if (!valid) {
            return this.$message.error('表单验证失败!')
        }

        menu.sub_button.push({ ...data })
        this.$message.success('添加成功')

        refForm[0].clearFrom()
        refDialog[0].close()
    }

    // 子菜单编辑
    onSubMenuEdit(index: number, subIndex: number) {
        const refForm: any = this.$refs[`SubMenuFormEdit-${index}-${subIndex}`]
        const refDialog: any = this.$refs[`SubMenuDialogEdit-${index}-${subIndex}`]

        const { valid, data } = refForm[0].validateForm()
        if (!valid) {
            return this.$message.error('表单验证失败!')
        }

        const parent = (this.form?.menu[index] as any).sub_button
        this.$set(parent, subIndex, { ...data })
        this.$message.success('修改成功')

        refForm[0].clearFrom()
        refDialog[0].close()
    }

    // 删除菜单
    handleMenuDel(index: number) {
        this.form.menu.splice(index, 1)
    }

    // 删除子菜单
    handleSubMenuDel(index: number, subIndex: number) {
        const menu: any = this.form.menu[index]
        menu.sub_button.splice(subIndex, 1)
    }

    // 初始化菜单数据
    initMPWeChatMenuData() {
        return new Promise((resovle, reject) => {
            apiMpWeChatMenuDetail().then((data: any) => {
                data.map((item: any) => {
                    item.has_menu = !!item.sub_button?.length
                })
                this.$set(this.form, 'menu', data)
            })
        })
    }

    // 表单保存
    onFromSave() {
        apiMpWeChatMenuSave({
            ...this.form
        })
            .then(data => {
                this.initMPWeChatMenuData()
                this.$message.success('保存成功')
            })
            .catch(err => {
                // this.$message.error('保存失败')
            })
    }

    // 保存并发布
    onFromPublish() {
        apiMpWeChatMenuPublish({
            ...this.form
        })
            .then(data => {
                this.initMPWeChatMenuData()
                this.$message.success('发布成功')
            })
            .catch(err => {
                // this.$message.error('发布失败')
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.initMPWeChatMenuData()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.channel-mp_wechat-menu {
    padding-bottom: 60px;
}

.mp_wechat__phone {
    position: relative;
    width: 375px;
    height: 812px;
    background: url('../../../assets/images/mobile.png') no-repeat;
    background-size: 100% 100%;

    &-menu {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 50px;
        display: flex;
        height: 60px;
        margin: 0 18px;
        border-top: 1px solid #e4e4e4;
        background-color: rgb(247, 247, 247);

        &-item {
            flex: 1;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;

            &:nth-child(n + 2)::before {
                position: absolute;
                left: 0;
                height: 50%;
                display: block;
                content: '';
                border-left: 1px solid #e4e4e4;
            }

            &--title {
                font-weight: 500;
            }
        }

        .mp_wechat__phone-submenu {
            position: absolute;
            z-index: 99;
            bottom: calc(100% + 10px);
            min-width: 100px;
            background-color: rgb(247, 247, 247);
            box-shadow: 0 0 4px 1px rgba(0, 0, 0, 0.3);

            &-item {
                padding: 16px 4px;
            }

            &-item:nth-child(n + 2) {
                border-top: 1px solid #e4e4e4;
            }
        }
    }

    &-active {
        border: 2px dashed $--color-primary;
    }
}

.mp_wechat__form {
    height: 100%;

    &--title {
        font-size: 14px;
        font-weight: 500;
    }

    &--content {
        display: flex;
        flex-wrap: wrap;

        .menu-item {
            position: relative;
            box-sizing: border-box;
            width: 340px;
            padding: 24px;
            border-radius: 8px;
            margin-right: 16px;
            margin-bottom: 16px;
            background-color: #efefef;
            overflow: hidden;

            &__delete {
                position: absolute;
                top: 24px;
                right: 24px;
                &-btn {
                    color: $--color-warning;
                    cursor: pointer;
                }
            }
        }
    }
}
</style>
