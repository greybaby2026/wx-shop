<template>
    <div class="setting-record">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="温馨提示：应工信部的要求，请务必填写公安备案号和网站备案号，保存的备案信息，将展示在后台登录页面。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <!-- 备案信息 -->
            <div class="ls-card m-t-16">
                <div class="card-title">备案信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="版权信息">
                        <el-input class="ls-input" v-model="form.copyright" show-word-limit />
                        <div class="muted xs">例如填写，Copyright © 2019-2020 公司名称</div>
                    </el-form-item>
                    <el-form-item label="备案号">
                        <el-input class="ls-input" v-model="form.record_number" show-word-limit />
                    </el-form-item>
                    <el-form-item label="备案号链接" prop="record_system_link">
                        <el-input class="ls-input" v-model="form.record_system_link" show-word-limit />
                        <div class="muted xs">例如填写域名信息备案系统链接，http://beian.miit.gov.cn</div>
                    </el-form-item>
                </div>
            </div>
            <div class="ls-card m-t-16">
                <div class="card-title">版权信息</div>
                <div class="m-t-16">
                    <ls-editor
                        v-model="form.copyright2"
                        :menu="[
                            'head',
                            'bold',
                            'fontSize',
                            'fontName',
                            'italic',
                            'underline',
                            'strikeThrough',
                            'indent',
                            'lineHeight',
                            'foreColor',
                            'link',
                            'list',
                            'justify',
                            'quote',
                            'emoticon',
                            'image',
                            'undo',
                            'redo'
                        ]"
                    />
                </div>
            </div>
            <div class="ls-card m-t-16">
                <div class="card-title">版权认证</div>
                <div class="m-t-16">
                    <el-form-item label="类型">
                        <el-radio-group v-model="form.copyright_auth.type">
                            <el-radio label="1">文字+链接</el-radio>
                            <el-radio label="2">图片+链接</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item>
                        <div class="flex p-6" style="width: 1000px; background-color: #f4f7ff">
                            <div style="width: 25%" class="m-r-5">图标</div>
                            <div style="width: 25%" class="m-r-5">名称</div>
                            <div style="width: 25%" class="m-r-5">链接地址</div>
                            <div style="width: 25%" class="m-r-5">操作</div>
                        </div>
                        <draggable
                            v-model="form.copyright_auth.list"
                            animation="300"
                            filter=".forbid"
                            style="cursor: move"
                        >
                            <div
                                class="flex p-6"
                                style="width: 1000px"
                                v-for="(item, index) in form.copyright_auth.list"
                            >
                                <div style="width: 25%" class="m-r-5">
                                    <material-select :limit="1" v-model="item.image" size="40">
                                        <template slot="upload">
                                            <div
                                                class="flex row-center"
                                                style="border: 1px solid #dcdfe6; width: 40px; height: 40px"
                                            >
                                                +
                                            </div>
                                        </template>
                                    </material-select>
                                </div>
                                <div style="width: 25%" class="m-r-5">
                                    <el-input v-model="item.name" show-word-limit maxlength="10"></el-input>
                                </div>
                                <div style="width: 25%" class="m-r-5">
                                    <el-input v-model="item.link"></el-input>
                                </div>
                                <div style="width: 25%" class="m-r-5">
                                    <el-button type="text" @click="delCopyrightAuth(index)">删除</el-button>
                                </div>
                            </div>
                        </draggable>
                        <div class="m-t-16">
                            <el-button type="primary" @click="addCopyrightAuth">添加</el-button>
                        </div>
                    </el-form-item>
                </div>
            </div>

            <!-- 资质信息 -->
            <!-- <div class="ls-card m-t-16">
                <div class="card-title">资质信息</div>
                <div class="card-content m-t-24">
                    <el-form-item label="营业执照">
                        <material-select v-model="form.business_license" :limit="1"></material-select>
                    </el-form-item>
                    <el-form-item label="其它资质" prop="record_system_link">
                        <material-select v-model="form.other_qualifications" :limit="5"></material-select>
                        <div class="muted xs">最多上传5张</div>
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
import { apiRecordInfo, apiRecordEdit } from '@/api/setting/shop'
import materialSelect from '@/components/material-select/index.vue'
import LsEditor from '@/components/editor.vue'
import Draggable from 'vuedraggable'

@Component({
    components: {
        materialSelect,
        LsEditor,
        Draggable
    }
})
export default class SettingRecord extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        copyright: '', // 版权信息
        record_number: '', // 备案号
        record_system_link: '', // 备案号链接
        business_license: '', // 营业执照
        other_qualifications: '', // 其它资质
        copyright2: '',
        copyright_auth: {
            type: '1',
            list: [{ image: '', name: '', link: '' }]
        }
    }

    // 表单验证
    // eslint-disable-next-line @typescript-eslint/ban-types
    rules: object = {
        record_system_link: [
            {
                pattern: /[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/,
                message: '请输入合法链接',
                trigger: 'blur'
            }
        ]
    }
    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiRecordInfo()
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
                return
            }
            const loading = this.$loading({ text: '保存中' })
            const params = { ...this.form }

            apiRecordEdit({
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
    addCopyrightAuth() {
        this.form.copyright_auth.list.push({
            image: '',
            name: '',
            link: ''
        })
    }
    delCopyrightAuth(index: number) {
        this.form.copyright_auth.list.splice(index, 1)
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
.setting-record {
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
}
</style>
