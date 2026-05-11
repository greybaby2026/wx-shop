<!-- pc渠道设置 -->
<template>
    <div class="h5_store">
        <!-- 主要内容 -->
        <el-form ref="formRef" :rules="formRules" :model="form" label-width="140px" size="small">
            <!-- 微信小程序 -->
            <div class="ls-card">
                <div class="card-title">基础设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="渠道状态" prop="status">
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
                    </el-form-item>
                    <!-- <el-form-item label="渠道关闭后访问" prop="redirect_type">
						<div>
							<el-radio :label="0" v-model="form.redirect_type">
								<span class="m-r-5">默认空白页</span>
								<el-input class="ls-input" v-model="jumpDesc" size="small"
									placeholder="请输入页面提示语">
								</el-input>
							</el-radio>
						</div>
						<div class="m-t-15">
							<el-radio :label="1" v-model="form.redirect_type">
								<span class="m-r-5 ">跳转自定义页面</span>
								<el-input class="ls-input" v-model="jumpLink" size="small"
									placeholder="请输入完整url链接">
								</el-input>
							</el-radio>
							<div class="muted xs m-r-16">渠道关闭后，用户访问时打开的页面</div>
						</div>
					</el-form-item> -->
                    <el-form-item label="渠道访问链接">
                        <el-input class="ls-input m-r-10" v-model="form.visit_url" size="small" disabled></el-input>
                        <el-button size="small" @click="onCopy(form.visit_url)">复制</el-button>
                    </el-form-item>
                </div>
            </div>

            <div class="ls-card m-t-16">
                <div class="card-title">推广设置</div>
                <div class="card-content m-t-24">
                    <el-form-item label="网站标题" prop="title">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.title"
                            size="small"
                            placeholder="请输入网站标题"
                        >
                        </el-input>
                        <div class="muted xs m-r-16">网站标题默认为商城名称</div>
                    </el-form-item>
                    <el-form-item label="网站图标" prop="ico">
                        <material-select :limit="1" v-model="form.ico" />
                        <div class="muted xs m-r-16">建议尺寸：100 x 100</div>
                    </el-form-item>
                    <el-form-item label="网站描述" prop="description">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.description"
                            size="small"
                            placeholder="请输入网站描述"
                        >
                        </el-input>
                    </el-form-item>
                    <el-form-item label="网站关键词" prop="keywords">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.keywords"
                            size="small"
                            placeholder="请输入网站关键词"
                        >
                        </el-input>
                    </el-form-item>
                    <el-form-item label="工具代码" prop="tools_code">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.tools_code"
                            size="small"
                            type="textarea"
                            :autosize="{ minRows: 3, maxRows: 6 }"
                            show-word-limit
                            placeholder="请输入百度统计、CNZZ统计等代码"
                        >
                        </el-input>
                        <div class="muted xs m-r-16">支持百度统计、CNZZ统计等，需请前往相应统计后台获取代码</div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="putPCSettingsSet()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiPCSettings, apiPCSettingsSet } from '@/api/channel/pc_store'
import { copyClipboard } from '@/utils/util'
@Component({
    components: {
        MaterialSelect
    }
})
export default class h5Store extends Vue {
    /** S Data **/
    form = {
        status: 0, // 渠道状态 0-关闭 1-开启
        redirect_type: 0, // 跳转类型 0-跳转至空白页 1-跳转至指定页
        redirect_content: '', // 跳转内容 跳转至空白页时是提示文字，跳转至指定页时是页面链接
        title: '', // 网站标题
        ico: '', // 网站图标
        description: '', // 网站描述
        keywords: '', // 网站关键字
        tools_code: '', // 工具代码
        visit_url: '' // PC商城访问链接
    }
    jumpDesc = '' // 跳转至空白页时是提示文字
    jumpLink = '' // 跳转至指定页时是页面链接

    // 表单验证
    formRules = {
        status: [
            {
                required: true,
                message: '请设置渠道状态',
                trigger: 'blur'
            }
        ],
        redirect_type: [
            {
                required: true,
                message: '请设置渠道关闭后访问方式',
                trigger: 'blur'
            }
        ]
    }

    $refs!: {
        formRef: any
    }
    /** E Data **/

    @Watch('jumpDesc', {
        immediate: true
    })
    getJumpDesc(val: any) {
        // 初始值
        this.form.redirect_content = val
    }
    @Watch('jumpLink', {
        immediate: true
    })
    getJumpLink(val: any) {
        // 初始值
        this.form.redirect_content = val
    }

    // 获取设置
    getPCSettings() {
        apiPCSettings()
            .then((res: any) => {
                this.form = res
            })
            .catch(() => {})
    }

    // 修改设置
    putPCSettingsSet() {
        this.$refs.formRef.validate((valid: boolean) => {
            // 预校验
            if (!valid) {
                return this.$message.error('请完善设置')
            }
            apiPCSettingsSet(this.form)
                .then((res: any) => {
                    this.getPCSettings()
                })
                .catch(() => {})
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
        this.getPCSettings()
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
