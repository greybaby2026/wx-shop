<template>
    <div class="default-reply">
        <div class="ls-card">
            <el-alert
                class="xxl"
                title="温馨提示：1.粉丝关注公众号时，会自动发送启用的关注回复；2.同时只能启用一个关注回复。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>
        </div>

        <div class="ls-user__grade ls-card m-t-20">
            <div class="list-header">
                <el-button size="small" type="primary" @click="onReplyAdd()">新增关注回复</el-button>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    v-loading="pager.loading"
                    :default-sort="{ prop: 'level', order: 'ascending' }"
                    :header-cell-style="{ background: '#f5f8ff' }"
                    size="mini"
                >
                    <el-table-column prop="name" label="规则名称" min-width="100px"> </el-table-column>
                    <el-table-column prop="content_type_desc" label="回复类型" min-width="100px"> </el-table-column>
                    <el-table-column prop="status" label="启用状态" min-width="100px">
                        <template slot-scope="scope">
                            <el-switch
                                v-model="scope.row.status"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                                @change="putMpWeChatReplyStatus(scope.row)"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="120px">
                        <template slot-scope="scope">
                            <!-- <el-button type="text" size="small" @click="onUserLevelEdit(scope.row)">详情</el-button> -->
                            <el-button type="text" size="small" @click="onReplyEdit(scope.row)">编辑</el-button>
                            <ls-dialog class="m-l-10 inline" @confirm="onMpWeChatReplyDelete(scope.row)">
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getMpWeChatReplyLists()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiMpWeChatReplyLists, apiMpWeChatReplyDelete, apiMpWeChatReplyStatus } from '@/api/channel/mp_wechat'
import { PageMode } from '@/utils/type'
import { RequestPaging } from '@/utils/util'
// import {apiMpWeChatConfigEdit, apiMPWeChatConfigInfo} from '@/api/channel/mp_wechat'
import LsDialog from '@/components/ls-dialog.vue'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsDialog,
        LsPagination
    }
})
export default class followReply extends Vue {
    /** S Data **/
    // 分页请求
    pager: RequestPaging = new RequestPaging()
    reply_type = '1' // 回复类型 1-关注回复 2-关键词回复 3-默认回复

    /** E Data **/

    /** S Methods **/
    // 获取
    getMpWeChatReplyLists() {
        this.pager
            .request({
                callback: apiMpWeChatReplyLists,
                params: { reply_type: this.reply_type }
            })
            .catch(() => {
                // this.$message.error("数据请求失败，刷新重载!");
            })
    }
    // 新增用户等级
    onReplyAdd() {
        this.$router.push({
            path: '/channel/mp_wechat/reply/reply_edit',
            query: {
                mode: PageMode.ADD,
                replyType: this.reply_type
            }
        })
    }
    // 编辑
    onReplyEdit(item: any) {
        this.$router.push({
            path: '/channel/mp_wechat/reply/reply_edit',
            query: {
                mode: PageMode.EDIT,
                id: item.id,
                replyType: this.reply_type
            }
        })
    }
    // 修改状态
    putMpWeChatReplyStatus(item: any) {
        apiMpWeChatReplyStatus({
            id: item.id as number
        })
            .then(() => {
                this.getMpWeChatReplyLists()
                // this.$message.success("修改成功!");
            })
            .catch(() => {
                this.$message.error('修改失败')
            })
    }
    // 删除
    onMpWeChatReplyDelete(item: any) {
        apiMpWeChatReplyDelete({
            id: item.id as number
        })
            .then(() => {
                this.getMpWeChatReplyLists()
                // this.$message.success("删除成功!");
            })
            .catch(() => {
                // this.$message.error("删除失败")
            })
    }
    /** E Methods **/
    /** S Life Cycle **/
    created() {
        this.getMpWeChatReplyLists()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
