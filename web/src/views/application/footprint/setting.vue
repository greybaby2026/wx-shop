<template>
    <div class="footprint-container">
        <!-- Header -->
        <div class="ls-card">
            <el-alert
                title="温馨提示：设置中的足迹气泡开关是整个气泡的总开关,如果这里关了则任何地方都不显示气泡。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="m-t-16 ls-card">
            <div>
                <el-form ref="form" :model="form" :rules="rules" label-width="80px" size="small">
                    <el-form-item label="气泡状态">
                        <el-switch v-model="form.status" :active-value="1" :inactive-value="0" />
                        <div class="muted">开启还是关闭足迹气泡，默认开启</div>
                    </el-form-item>
                    <el-form-item label="气泡时长" prop="duration">
                        <el-input type="number" v-model="form.duration" prefix-icon="el-icon-time" style="width: 180px">
                            <template slot="append">分钟</template>
                        </el-input>
                        <div class="muted">查询多长时间范围内的足迹信息</div>
                    </el-form-item>
                    <el-form-item label="显示页面">
                        <el-checkbox-group v-model="form.pages">
                            <el-checkbox :label="1">首页</el-checkbox>
                            <el-checkbox :label="2">商品详情</el-checkbox>
                        </el-checkbox-group>
                        <div class="muted">设置在哪些页面会显示气泡</div>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="free-shipping-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSaveFootprintConfig('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiFootprintEdit, apiFootprintSetting } from '@/api/application/footprint'

interface Form {
    duration: number
    status: number
    pages: number[]
}

@Component
export default class Footprint extends Vue {
    form: Form = {
        duration: 60, // 气泡时长
        status: 1, // 气泡状态
        pages: []
    }

    // 表单验证
    rules: any = {
        duration: [
            {
                required: true,
                validator: (rule: any, value: any, callback: any): any => {
                    if (!value) {
                        return callback(new Error('必填项不可为空'))
                    }
                    if (value * 1 <= 0) {
                        return callback(new Error('时长必须大于0'))
                    }
                    callback()
                }
            }
        ]
    }

    /** S Methods **/

    // 获取足迹配置信息
    getFootprintData(): void {
        apiFootprintSetting().then(res => {
            this.form = res
        })
    }

    // 点击足迹配置保存按钮
    onSaveFootprintConfig(formName: string) {
        const ref = this.$refs[formName] as HTMLFormElement

        ref.validate((valid: boolean) => {
            apiFootprintEdit(this.form).then(() => {
                this.getFootprintData()
            })
        })
    }

    /** E Methods **/

    /** Life Cycle **/
    created() {
        // 初始化数据
        this.getFootprintData()
    }
}
</script>

<style lang="scss" scoped>
.footprint-container {
    padding-bottom: 80px;
}
</style>
