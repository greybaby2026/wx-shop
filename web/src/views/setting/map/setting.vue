<template>
    <div class="map-setting">
        <el-form ref="form" :model="form" :rules="rules" label-width="120px" size="small">
            <!-- 分享设置 -->
            <div class="ls-card">
                <div class="card-title">地图设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="腾讯地图配置" prop="tencent_map_key">
                        <el-input
                            class="ls-input"
                            v-model="form.tencent_map_key"
                            placeholder="请输入腾讯地图的配置Key"
                        />
                        <div class="muted m-t-10">
                            如果没有Key，可前往：https://lbs.qq.com/ 申请
                            <br />
                            使用场景：1、PC端门店自提，获取地理定位；2、后台创建门店时需要获取地理位置
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="onSubmitFrom('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { apiMapGet, apiMapSet } from '@/api/setting/map'
@Component
export default class MapSetting extends Vue {
    /** S Data **/
    // 表单数据
    form = {
        tencent_map_key: ''
    }
    // 表单验证
    rules = {
        tencent_map_key: [
            {
                required: true,
                message: '请输入腾讯地图的配置Key'
            }
        ]
    }

    /** E Data **/

    /** S Methods **/
    // 初始化表单数据
    initFormData() {
        apiMapGet()
            .then(res => {
                // 表单同步于接口响应数据
                this.form = res
            })
            .catch(() => {
                this.$message.error('数据加载失败，请刷新重载')
            })
    }

    // 提交表单
    onSubmitFrom(formName: string) {
        const refs = this.$refs[formName] as HTMLFormElement

        refs.validate((valid: boolean) => {
            if (!valid) {
                return
            }
            const loading = this.$loading({ text: '保存中' })

            apiMapSet({
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
.map-setting {
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
