<!-- 系统更新 -->
<template>
    <div class="system-update">
        <div class="ls-card">
            <el-alert class="xxl" type="info" :closable="false" show-icon>
                <template slot="title">
                    <div class="iconSize">温馨提示：</div>
                    <div class="iconSize flex">
                        1.版本更新需要逐个版本更新，
                        <div class="a-key">更新前请备份好系统和数据库，更新成功后需要强制刷新站点；</div>
                    </div>
                    <div class="iconSize">2.系统没有做二次开发，可以直接选择在线更新功能；</div>
                    <div class="iconSize">
                        3.系统已做二次开发，进行了功能修改，请谨慎选择在线更新功能，推荐以更新包的形式手动更新；
                    </div>
                    <div class="iconSize">
                        4.由于更换新域名原因，1.3.1及以下版本，需修改server/app/adminapi/logic/settings/system/UpgradeLogic.php文件中的BASE_URL为https://server.likeshop.cn
                        否则系统更新列表会报错。
                    </div>
                </template>
            </el-alert>
        </div>
        <div class="ls-card m-t-16">
            <el-table
                :data="pager.lists"
                style="width: 100%"
                size="mini"
                v-loading="false"
                :header-cell-style="{ background: '#f5f8ff' }"
            >
                <el-table-column prop="publish_time" label="发布日期"> </el-table-column>
                <el-table-column label="版本号">
                    <template slot-scope="scope">
                        <div class="flex">
                            <el-tag
                                v-if="scope.$index == 0 && pager._page == 1"
                                size="small"
                                type="success"
                                effect="plain"
                                round
                            >
                                new</el-tag
                            >
                            <div class="m-l-5">{{ scope.row.version_no }}</div>
                        </div>
                        <div class="">
                            {{ scope.row.version_str }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="" label="版本内容">
                    <template slot-scope="scope">
                        <div class="" v-for="(item, index) in scope.row.add">
                            {{ item }}
                        </div>
                        <div class="" v-for="(item, index) in scope.row.optimize">
                            {{ item }}
                        </div>
                        <div class="" v-for="(item, index) in scope.row.repair">
                            {{ item }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="notice" label="注意事项">
                    <template slot-scope="scope">
                        <div class="" v-for="(item, index) in scope.row.notice" :key="index">
                            {{ item }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="操作" min-width="200px">
                    <template slot-scope="scope">
                        <div class="operation flex flex-wrap">
                            <el-button
                                v-if="scope.row.able_update == 1"
                                class="m-r-10"
                                type="primary"
                                size="small"
                                slot="trigger"
                                @click="onOuterVisible(scope.row.id)"
                                >一键更新
                            </el-button>
                            <el-button
                                v-if="scope.row.integral_package_link"
                                class="m-r-10"
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 6)"
                                >下载完整安装包
                            </el-button>
                            <el-button
                                v-if="scope.row.package_link"
                                class="m-r-10"
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 2)"
                                >下载服务端更新包</el-button
                            >
                            <el-button
                                v-if="scope.row.pc_package_link"
                                class="m-r-10"
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 3)"
                                >下载pc端更新包</el-button
                            >
                            <el-button
                                v-if="scope.row.uniapp_package_link"
                                class="m-r-10"
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 4)"
                                >下载uniapp更新包</el-button
                            >
                            <el-button
                                v-if="scope.row.web_package_link"
                                class=""
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 5)"
                            >
                                下载后台前端更新包</el-button
                            >
                            <el-button
                                v-if="scope.row.kefu_package_link"
                                class=""
                                type=""
                                size="small"
                                slot="trigger"
                                @click="toPackage(scope.row.id, 8)"
                            >
                                下载客服更新包</el-button
                            >
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 底部分页栏  -->
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getSystemUpgradeLists()" />
            </div>
        </div>

        <!-- 一键更新弹出框 -->
        <el-dialog class="dialog" title="" :visible.sync="outerVisible" width="30%" top="40vh" center>
            <el-dialog class="dialogTwo" width="30%" top="40vh" :visible.sync="innerVisible" append-to-body center>
                <el-dialog
                    class="dialogThree"
                    width="30%"
                    top="40vh"
                    :visible.sync="threeVisible"
                    append-to-body
                    center
                    :close-on-click-modal="false"
                    :close-on-press-escape="false"
                >
                    <div
                        style="height: 200px; text-align: center"
                        v-loading="loading"
                        element-loading-text="切勿关闭窗口或刷新页面"
                    >
                        <div>系统更新中，更新完毕后会自行刷新页面</div>
                        <!-- <div>切勿关闭窗口或刷新页面</div> -->
                    </div>
                </el-dialog>
                <div v-if="isSecondaryDev == false" style="height: 200px">
                    <div class="" style="text-align: center">
                        一键更新导致的系统问题，欢迎前往社区反馈，请做好系统备份！
                    </div>
                    <div class="btn-div flex-col" style="width: 160px; margin: auto">
                        <el-button
                            type="primary"
                            @click="confirmUpdate"
                            size="small"
                            style="margin-top: 15px; margin-left: 0"
                            >确定更新</el-button
                        >
                        <el-button @click="innerVisible = false" size="small" style="margin-top: 15px; margin-left: 0">
                            取消更新</el-button
                        >
                    </div>
                </div>
                <div v-if="isSecondaryDev == true" style="height: 200px">
                    <div class="" style="text-align: center">
                        <div>二次开发后请谨慎使用一键更新功能！</div>
                        <div>二次开发后一键更新导致的系统问题，官方无法处理，请做好系统备份！</div>
                    </div>
                    <div class="btn-div flex-col" style="width: 160px; margin: auto">
                        <el-button
                            type="primary"
                            @click="confirmUpdate"
                            size="small"
                            style="margin-top: 15px; margin-left: 0"
                            >确定更新</el-button
                        >
                        <el-button @click="innerVisible = false" size="small" style="margin-top: 15px; margin-left: 0">
                            下载更新包，手动更新</el-button
                        >
                        <el-button @click="innerVisible = false" size="small" style="margin-top: 15px; margin-left: 0">
                            取消更新</el-button
                        >
                    </div>
                </div>
            </el-dialog>
            <div style="height: 200px">
                <div class="" style="text-align: center">系统是否进行二次开发</div>
                <div class="flex-col">
                    <el-button type="primary" @click="NotSecondaryDev" size="small">未做二次开发，直接更新</el-button>
                    <el-button @click="SecondaryDev" size="small">已做二次开发</el-button>
                    <el-button @click="outerVisible = false" size="small">取消更新</el-button>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiSystemUpgradeLists,
    apiSystemUpgradeDownloadPkg,
    apiSystemUpgrade
} from '@/api/setting/system_maintain/system_maintain'
import { RequestPaging, debounce, throttle } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class SystemUpdate extends Vue {
    /** S Data **/
    // 外层dialog
    outerVisible = false
    // 第二层dialog
    innerVisible = false
    // 第三层dialog
    threeVisible = false
    // 是否二次开发
    isSecondaryDev = true

    // 加载动画
    loading = true

    pager: RequestPaging = new RequestPaging()

    // 点击一键更新按钮的版本id
    id = 0

    timer = 0
    /** E Data **/

    /** S Methods **/

    //系统更新列表
    getSystemUpgradeLists() {
        // 分页请求
        this.pager
            .request({
                callback: apiSystemUpgradeLists
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载')
            })
    }

    // // 下载完整安装包跳转页面
    // toIntegralPackage(url: any) {
    // 	window.open(url, '_blank')
    // }

    // 非完整安装包下载
    toPackage(id: any, type: any) {
        if (this.timer) {
            clearTimeout(this.timer)
        }

        this.timer = setTimeout(() => {
            //
            apiSystemUpgradeDownloadPkg({
                id: id,
                update_type: type // 2:服务端更新包下载，3:pc端更新包下载，4:uniapp更新包下载,5:后台前端更新包下载, 6:完整包更新
            }).then(res => {
                //
                //
                if (type == 6) {
                    window.open(res.line, '_blank')
                } else {
                    window.location.href = res.line
                }
            })
        }, 500)
    }

    // 一键更新请求
    systemUpgrade() {
        apiSystemUpgrade({
            id: this.id,
            update_type: 1 // 1:一键更新
        })
            .then(res => {
                this.getSystemUpgradeLists()
            })
            .finally(() => {
                // this.loading = false
                this.threeVisible = false
                this.innerVisible = false
                this.outerVisible = false
            })
    }

    // 点击一键更新按钮
    onOuterVisible(id: any) {
        this.outerVisible = true
        this.id = id
    }

    // 未做二次开发
    NotSecondaryDev() {
        this.isSecondaryDev = false
        this.innerVisible = true
    }
    // 已做二次开发
    SecondaryDev() {
        this.isSecondaryDev = true
        this.innerVisible = true
    }

    // 点击确定更新
    confirmUpdate() {
        this.threeVisible = true
        this.systemUpgrade()
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getSystemUpgradeLists()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.system-update {
    .dialog {
        ::v-deep .el-button {
            margin: auto;
            width: 160px;
            margin-top: 15px;
        }

        .dialogTwo {
            .btn-div {
                ::v-deep .el-button {
                    margin: auto;
                    width: 160px;
                    margin-top: 15px;
                }
            }
        }
    }

    .iconSize {
        padding: 3px 0;

        .a-key {
            color: #f56c6c;
        }
    }

    .operation {
        ::v-deep .el-button {
            margin-left: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    }
}
</style>
