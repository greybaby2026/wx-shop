<!-- 微信小程序 -->
<template>
    <div class="wechat_app">
        <!-- 提示 -->
        <div class="ls-card">
            <el-alert
                title="1.首次使用小程序一键上传，先安装好node环境，然后在终端下使用cd命令进到项目下的server/extend/miniprogram-ci目录，运行命令 npm install miniprogram-ci --save  建议使用node最新版本。2.小程序上传前，请先到本系统的“小程序配置”设置小程序代码上传密钥，如已配置，请忽略。3.小程序上传成功后，需要前往小程序后台提交审核。"
                type="info"
                :closable="false"
                show-icon
            />
        </div>

        <!-- 主要内容 -->
        <el-form ref="formRef" :model="form" :rules="formRules" label-width="140px" size="small">
            <!-- 一健上传 -->
            <div class="ls-card m-t-16">
                <div class="card-title">小程序代码上传</div>
                <div class="card-content m-t-24">
                    <el-form-item label="版本号">
                        <el-input class="ls-input m-r-10" v-model="form.version" size="small" disabled></el-input>
                    </el-form-item>
                    <el-form-item label="项目备注">
                        <el-input
                            class="ls-input m-r-10"
                            v-model="form.upload_desc"
                            placeholder="请输入项目备注（选填）"
                            size="small"
                            type="textarea"
                            rows="4"
                        ></el-input>
                    </el-form-item>
                    <el-form-item><el-button type="primary" @click="handleUpload">上传小程序</el-button> </el-form-item>
                </div>
            </div>
        </el-form>
        <div class="ls-card m-t-16">
            <el-table :data="pager.lists" style="width: 100%" size="mini" v-loading="pager.loading">
                <el-table-column label="版本号" prop="version"></el-table-column>
                <el-table-column label="状态" prop="status_desc">
                    <template slot-scope="scope">
                        <el-tag size="mini" type="success" v-if="scope.row.status == 1">
                            {{ scope.row.status_desc }}
                        </el-tag>
                        <el-tooltip class="item" effect="dark" :content="scope.row.fail_reason" placement="top">
                            <el-tag size="mini" type="danger" v-if="scope.row.status == 2">
                                {{ scope.row.status_desc }}
                            </el-tag>
                        </el-tooltip>
                        <el-tag size="mini" type="info" v-if="scope.row.status == 0">
                            {{ scope.row.status_desc }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作人" prop="admin_name"></el-table-column>
                <el-table-column label="上传时间" prop="create_time"></el-table-column>
            </el-table>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getLogList()" />
            </div>
        </div>

        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="">保存</el-button>
            </div>
        </div>
        <IsLoading ref="isLoadingRef" title="上传小程序" />
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import { throttle } from '@/utils/util'
import IsLoading from '@/components/is-loading.vue'
import { apiWechatMiniUpload, apiWechatMinigetlog } from '@/api/channel/wechat_app'
import { RequestPaging } from '@/utils/util'
import config from '@/config'
import LsPagination from '@/components/ls-pagination.vue'
import { apiWorkbenchIndex } from '@/api/home'

@Component({
    components: {
        LsPagination,
        IsLoading
    }
})
export default class WechatAppUpload extends Vue {
    $refs!: { apiConfig: any; isLoadingRef: any; form: any }

    /** S Data **/
    form: any = {
        version: this.$store.getters.config.version,
        upload_desc: ''
    }

    // 分页查询
    pager: RequestPaging = new RequestPaging()

    // 表单验证
    formRules = {}

    /** E Data **/

    handleUpload() {
        this.$refs.isLoadingRef.openDialog()

        apiWechatMiniUpload({ upload_desc: this.form.upload_desc }).finally(() => {
            this.getLogList()
            this.form.upload_desc = ''
            this.$refs.isLoadingRef.closeDialog()
        })
    }
    getLogList() {
        this.pager.request({
            callback: apiWechatMinigetlog
        })
        // apiWorkbenchIndex({}).then(res => {
        //     this.form.version = res.shop_info.version
        // })
    }

    /** S Life Cycle **/
    created() {
        this.handleUpload = throttle(this.handleUpload, 1000)

        this.getLogList()
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

.wechat_app {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
