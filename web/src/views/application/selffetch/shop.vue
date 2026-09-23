<template>
    <div class="admin">
        <div class="ls-card">
            <!-- 头部表单 -->
            <div class="m-t-20">
                <el-form :inline="true" :model="form" size="small">
                    <!-- 自提门店 -->
                    <el-form-item label="门店名称">
                        <el-input v-model="form.name" placeholder="请输入门店名称" />
                    </el-form-item>

                    <!-- 门店状态 -->
                    <el-form-item label="门店状态" class="m-l-24">
                        <el-select v-model="form.status" placeholder="请选择状态">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="启用" :value="1"></el-option>
                            <el-option label="停用" :value="0"></el-option>
                        </el-select>
                    </el-form-item>

                    <!-- 搜索查询 -->
                    <el-form-item class="m-l-24">
                        <el-button type="primary" @click="search">查询</el-button>
                        <el-button @click="resetSearch">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :method="apiSelffetchShopList"
                            :param="form"
                            :pageSize="pager._size"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="ls-card m-t-16">
            <!-- 添加自提门店 -->
            <el-button type="primary" size="small" @click="addSelffetchShop">新增自提门店</el-button>

            <!-- 管理员数据列表 -->
            <div class="m-t-24">
                <el-table :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                    <el-table-column prop="name" label="门店LOGO" min-width="80">
                        <div class="flex" slot-scope="scope">
                            <el-image :src="scope.row.image" style="width: 40px; height: 40px" fit="fill" />
                            <!-- <span class="m-l-10">{{ scope.row.name }}</span> -->
                        </div>
                    </el-table-column>
                    <el-table-column prop="name" label="门店名称" min-width="120" />
                    <el-table-column prop="contact" label="店长" min-width="100" />
                    <el-table-column prop="mobile" label="联系电话" min-width="120" />
                    <el-table-column prop="detailed_address" label="门店地址" min-width="220" />
                    <el-table-column prop="create_time" label="营业时间" width="180">
                        <template slot-scope="scope">
                            {{ scope.row.business_start_time + '-' + scope.row.business_end_time }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="门店状态" min-width="80">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                @change="changeSwitchStatus($event, scope.row)"
                            />
                        </template>
                    </el-table-column>
                    <!-- <el-table-column
                        sortable
                        prop="create_time"
                        label="创建时间"
                        width="180"
                    /> -->
                    <el-table-column label="操作" min-width="240">
                        <!-- 操作 -->
                        <template slot-scope="scope">
                            <el-button type="text" size="small" @click="showShopQrCode(scope.row)">门店码</el-button>
                            <el-button type="text" size="small" @click="goSelffetchShopEdit(scope.row)">编辑</el-button>
                            <ls-dialog class="m-l-10 inline" @confirm="onSelffetchShopDelete(scope.row)">
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页 -->
                <div class="m-t-24 pagination">
                    <ls-pagination v-model="pager" @change="getSelffetchShopList" />
                </div>
            </div>
        </div>

        <!-- 门店码弹窗 -->
        <el-dialog title="门店专属小程序码" :visible.sync="qrDialog.show" width="420px">
            <div v-loading="qrDialog.loading" class="qr-wrap">
                <template v-if="qrDialog.image">
                    <img class="qr-image" :src="qrDialog.image" alt="门店码" />
                    <div class="qr-name">{{ qrDialog.name }}</div>
                    <div class="qr-tip">
                        顾客扫码进入小程序后，将自动绑定为该门店会员（首绑终身）。<br />
                        可直接保存图片用于门店物料，或复制场景值到微信公众平台自行生成。
                    </div>
                    <div class="qr-scene">场景值：{{ qrDialog.scene }}</div>
                </template>
                <template v-else>
                    <div class="qr-empty">暂无门店码</div>
                </template>
            </div>
            <div slot="footer">
                <el-button size="small" @click="qrDialog.show = false">关闭</el-button>
                <el-button size="small" :disabled="!qrDialog.scene" @click="copyQrScene">复制场景值</el-button>
                <el-button type="primary" size="small" :disabled="!qrDialog.image" @click="downloadQrCode">
                    下载门店码
                </el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiSelffetchShopList, apiSelffetchShopStatus, apiSelffetchShopDel } from '@/api/application/selffetch'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
import ExportData from '@/components/export-data/index.vue'

@Component({
    components: {
        LsDialog,
        LsPagination,
        ExportData
    }
})
export default class SelffetchShop extends Vue {
    /** S Data **/
    apiSelffetchShopList = apiSelffetchShopList
    // 表单数据
    form = {
        name: '', //名称
        status: '' //角色id
    }
    pager: RequestPaging = new RequestPaging()

    // 门店码弹窗：扫码进入小程序后自动绑定该门店（scene=store_id=x）
    qrDialog = {
        show: false,
        loading: false,
        name: '',
        image: '',
        scene: ''
    }
    /** E Data **/

    /** S Methods **/
    // 搜索
    search() {
        this.pager.page = 1
        this.getSelffetchShopList()
    }

    // 重置搜索
    resetSearch() {
        Object.keys(this.form).map(key => {
            this.$set(this.form, key, '')
        })
        this.getSelffetchShopList()
    }

    // 获取列表数据
    getSelffetchShopList() {
        // 请求管理员列表
        this.pager
            .request({
                callback: apiSelffetchShopList,
                params: this.form
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载!')
            })
    }

    // 添加
    addSelffetchShop() {
        this.$router.push({
            path: '/selffetch/selffetch_shop_edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 删除
    onSelffetchShopDelete(data: any) {
        apiSelffetchShopDel({ id: data.id }).then(() => {
            // 删除成功就请求新列表
            this.getSelffetchShopList()
        })
    }

    // 编辑
    goSelffetchShopEdit(data: any) {
        this.$router.push({
            path: '/selffetch/selffetch_shop_edit',
            query: {
                mode: PageMode.EDIT,
                id: data.id
            }
        })
    }

    // 更改状态
    changeSwitchStatus(value: 0 | 1, data: any) {
        apiSelffetchShopStatus({
            id: data.id,
            status: value
        }).catch(err => {
            this.getSelffetchShopList()
        })
    }

    // 门店码：生成并展示（scene=store_id=x，扫码后自动绑定该门店）
    // 说明：门店码由微信接口实时生成，需小程序已发布且首页存在，否则接口会返回失败提示
    showShopQrCode(data: any) {
        this.qrDialog = {
            show: true,
            loading: true,
            name: data.name,
            image: '',
            scene: `store_id=${data.id}`
        }
        apiSelffetchShopQrCode({ id: data.id })
            .then((res: any) => {
                this.qrDialog.image = res && res.base64 ? res.base64 : ''
                if (!this.qrDialog.image) {
                    this.$message.error('门店码生成失败，请稍后重试')
                }
            })
            .catch(() => {})
            .finally(() => {
                this.qrDialog.loading = false
            })
    }

    // 下载门店码（base64 直接触发浏览器下载，无需服务端落盘）
    downloadQrCode() {
        if (!this.qrDialog.image) return
        const a = document.createElement('a')
        a.href = this.qrDialog.image
        a.download = `门店码_${this.qrDialog.name || this.qrDialog.scene}.png`
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
    }

    // 复制场景值：便于到微信公众平台自行生成小程序码
    copyQrScene() {
        if (!this.qrDialog.scene) return
        const input = document.createElement('textarea')
        input.value = this.qrDialog.scene
        input.style.position = 'fixed'
        input.style.top = '-9999px'
        document.body.appendChild(input)
        input.select()
        try {
            document.execCommand('copy')
            this.$message.success('已复制场景值')
        } catch (e) {
            this.$message.error('复制失败，请手动复制')
        }
        document.body.removeChild(input)
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getSelffetchShopList()
    }

    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.pagination {
    padding-right: 5%;
    display: flex;
    justify-content: flex-end;
}

.qr-wrap {
    min-height: 200px;
    text-align: center;

    .qr-image {
        width: 260px;
        height: 260px;
    }

    .qr-name {
        margin-top: 8px;
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    .qr-tip {
        margin-top: 12px;
        font-size: 12px;
        line-height: 20px;
        color: #999;
        text-align: left;
    }

    .qr-scene {
        margin-top: 12px;
        font-size: 12px;
        color: #666;
        background: #f5f7fa;
        border-radius: 4px;
        padding: 6px 10px;
        word-break: break-all;
    }

    .qr-empty {
        padding: 60px 0;
        color: #999;
        font-size: 13px;
    }
}
</style>
