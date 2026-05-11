<template>
    <div class="setting-share">
        <div class="ls-card">
            <!-- 提示 -->
            <el-alert
                title="1.无需设置分享链接，分享页面为当前页面链接，APP分享时使用H5当前页链接；2.个人中心、我的订单等隐私页面分享链接为商城首页。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <!-- 分享设置 -->
            <div class="ls-card m-t-16">
                <div class="card-title">分享设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="页面" prop="share_page">
                        <el-radio v-model="form.share_page" :label="1">当前页面</el-radio>
                        <div class="muted xs">用户使用微信分享把商城页面发送给微信好友时，分享当前页面对应的链接。</div>
                    </el-form-item>
                    <el-form-item label="标题">
                        <el-input class="ls-input" v-model="form.share_title" show-word-limit />
                        <div class="muted xs">分享标题，不填则为当前页面标题</div>
                    </el-form-item>
                    <el-form-item label="简介">
                        <el-input class="ls-input" v-model="form.share_intro" show-word-limit />
                        <div class="muted xs">分享简介，不填则为空</div>
                    </el-form-item>
                    <el-form-item label="图片" prop="share_image">
                        <material-select :limit="1" v-model="form.share_image" />
                        <div class="flex">
                            <div class="muted xs m-r-16">建议尺寸：500 * 400px，支持jpg，jpeg，png格式</div>
                            <el-popover placement="right" width="200" trigger="hover">
                                <el-image :src="shareImageExample" />
                                <el-button slot="reference" type="text">查看示例</el-button>
                            </el-popover>
                        </div>
                    </el-form-item>
                </div>
            </div>
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
import { apiShareInfo, apiShareEdit } from '@/api/setting/shop'

@Component({
    components: {
        MaterialSelect
    }
})
export default class SettingShare extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        share_page: 0, // 分享页面
        share_title: '', // 分享标题
        share_intro: '', // 分享简介
        share_image: '' // 分享图片
    }

    // 表单验证
    rules: object = {
        share_page: [
            {
                required: true,
                message: '请选择分享页面',
                trigger: 'blur'
            }
        ]
    }

    shareImageExample = require('@/assets/images/setting/img_shili_share_example.png')
    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiShareInfo()
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

            apiShareEdit({
                ...this.form
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
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.initFormData()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.setting-share {
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
