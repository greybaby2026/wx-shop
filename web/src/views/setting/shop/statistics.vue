<template>
    <div class="">
        <div class="ls-card">
            <el-alert
                title="温馨提示：站点统计功能可以帮你了解用户在网站上的行为，助你优化网站体验，提高访问效果。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="ls-card m-t-10">
            <div class="card-title">
                <div>Clarity</div>
            </div>
            <div class="card-content m-t-24">
                <el-form ref="form" :model="form" label-width="120px" size="small">
                    <el-form-item label="应用ID" prop="site_statistic.clarity_app_id">
                        <!-- :rules="[
                            {
                                required: true,
                                message: '请输入应用ID',
                                trigger: ['blur', 'change']
                            }
                        ]" -->
                        <el-input class="ls-input" v-model="form.site_statistic.clarity_app_id" />
                        <div class="muted xs">
                            请访问
                            <a
                                href="https://clarity.microsoft.com"
                                style="text-decoration: none; color: #3868f9"
                                target="_blank"
                                >Clarity</a
                            >
                            官网创建你的Clarity统计应用
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="onSubmitFrom('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiProtocolInfo, apiProtocolEdit } from '@/api/setting/shop'
import { apiSiteStatisticInfo, apiSiteStatisticset } from '@/api/setting/statistics'

@Component({})
export default class Settingstatistics extends Vue {
    /** S Data **/
    // 表单数据
    form: any = {
        site_statistic: {
            clarity_app_id: ''
        }
    }
    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiSiteStatisticInfo()
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

    // 提交表单
    onSubmitFrom(formName: string) {
        const refs = this.$refs[formName] as HTMLFormElement

        refs.validate((valid: boolean) => {
            // if (!valid) {
            //     return
            // }
            const loading = this.$loading({ text: '保存中' })

            apiSiteStatisticset({
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
                    this.initFormData()
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
<style scoped lang="scss">
.card-title {
    display: flex;
    align-items: flex-end;
    font-size: 14px;
    font-weight: 500;
}
</style>
