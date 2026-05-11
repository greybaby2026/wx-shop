<!-- 微信小程序 -->
<template>
    <div class="h5_store">
        <!-- 提示 -->
        <!-- <div class="ls-card">
			<el-alert title="请先前往微信小程序后台申请认证微信小程序；" type="info" :closable="false" show-icon />
		</div> -->

        <!-- 主要内容 -->
        <el-form ref="formRef" :model="form" label-width="140px" size="small">
            <!-- 微信小程序 -->
            <div class="ls-card m-t-16">
                <!-- <div class="card-title">渠道设置</div> -->
                <div class="card-content m-t-24">
                    <el-form-item label="H5状态">
                        <!-- switch开关 -->
                        <div class="flex">
                            <el-switch
                                v-model="form.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ form.status ? '开启' : '关闭' }}</span>
                        </div>
                        <div class="muted xs m-r-16">渠道状态为关闭时，将不对外提供服务，请谨慎操作</div>
                    </el-form-item>
                    <el-form-item label="访问链接">
                        <el-input class="ls-input m-r-10" v-model="form.url" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.url)">复制</el-button>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="putH5SettingsSet()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiH5Settings, apiH5SettingsSet } from '@/api/channel/h5_store'
import { copyClipboard } from '@/utils/util'
@Component({
    components: {
        MaterialSelect
    }
})
export default class h5Store extends Vue {
    /** S Data **/
    form = {
        status: 1, // 状态 0-关闭 1-开启
        url: '' // 访问链接
    }

    /** E Data **/

    // 获取APP设置
    getH5Settings() {
        apiH5Settings()
            .then((res: any) => {
                this.form = res
                // this.$message.success('获取数据成功!')
            })
            .catch(() => {
                // this.$message.error('获取数据失败!')
            })
    }

    // 修改APP设置
    putH5SettingsSet() {
        apiH5SettingsSet({ status: this.form.status })
            .then((res: any) => {
                this.getH5Settings()
                //this.$message.success('保存成功!')
            })
            .catch(() => {
                //this.$message.error('保存失败!')
            })
    }
    // 复制到剪贴板
    onCopy(value: string) {
        copyClipboard(value)
            .then(() => {
                this.$message.success('复制成功')
            })
            .catch(err => {
                this.$message.error('复制失败')
            })
    }

    /** S Life Cycle **/
    created() {
        this.getH5Settings()
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
}

.h5_store {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
