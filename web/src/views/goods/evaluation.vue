<template>
    <div class="ls-evaluation">
        <div class="ls-evaluation__top ls-card">
            <el-alert
                title="温馨提示：1.会员购买商品之后可以对购买的商品进行评价；2.可以对会员的商品评价进行回复，隐藏或删除；3.商品评价会在商城的商品详情中进行显示。"
                type="info"
                show-icon
                :closable="false"
            ></el-alert>
            <div class="ls-top__search m-t-16">
                <el-form ref="form" inline :model="queryObj" label-width="80px" size="small">
                    <el-form-item label="商品信息">
                        <el-input placeholder="请输入商品名称/编码" v-model="queryObj.goods_info"></el-input>
                    </el-form-item>
                    <el-form-item label="买家信息">
                        <el-input placeholder="请输入用户昵称/编码" v-model="queryObj.user_info"></el-input>
                    </el-form-item>
                    <el-form-item label="评价类型">
                        <el-select v-model="queryObj.comment_type" placeholder="请选择评价类型">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="真实评价" :value="1"></el-option>
                            <el-option label="虚拟评价" :value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="回复状态">
                        <el-select v-model="queryObj.reply_status" placeholder="请选择回复状态">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="待回复" :value="1"></el-option>
                            <el-option label="已回复" :value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="审核状态">
                        <el-select v-model="queryObj.verify_status" placeholder="请选择审核状态">
                            <el-option label="全部" value=""></el-option>
                            <el-option label="待审核" :value="1"></el-option>
                            <el-option label="审核通过" :value="2"></el-option>
                            <el-option label="审核拒绝" :value="3"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="评价时间">
                        <date-picker :start-time.sync="queryObj.start_time" :end-time.sync="queryObj.end_time" />
                    </el-form-item>

                    <el-form-item label class="m-l-20">
                        <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                        <el-button size="small" @click="handleReset">重置</el-button>
                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiGoodsCommentLists"
                            :param="queryObj"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="ls-evaluation__content ls-card m-t-16">
            <div class="ls-content__btns">
                <ls-dialog
                    class="inline"
                    title="回复评价"
                    width="600px"
                    :disabled="disabledBtn"
                    @confirm="handleReply(selectIds)"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn" @click="reply = ''"
                        >批量回复</el-button
                    >
                    <div>
                        <el-input type="textarea" :rows="10" placeholder="请输入回复内容" v-model="reply"></el-input>
                    </div>
                </ls-dialog>
                <ls-dialog
                    class="inline m-l-10"
                    content="确定批量删除？请谨慎操作"
                    :disabled="disabledBtn"
                    @confirm="handleDelete(selectIds)"
                >
                    <el-button slot="trigger" size="small" :disabled="disabledBtn">批量删除</el-button>
                </ls-dialog>
            </div>
            <div class="ls-content__table m-t-16">
                <el-table
                    ref="table"
                    :data="pager.lists"
                    style="width: 100%"
                    size="mini"
                    v-loading="pager.loading"
                    @selection-change="handleSelect"
                >
                    <el-table-column fixed type="selection" width="55"></el-table-column>
                    <el-table-column label="商品图片" width="80">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image :src="scope.row.goods_image" style="width: 58px; height: 58px"></el-image>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="商品信息" min-width="240">
                        <template slot-scope="scope">
                            <div class="goods-info m-l-8">
                                <div class="line-2">
                                    {{ scope.row.goods_name }}
                                </div>
                                <div class="xs muted">规格：{{ scope.row.spec_value_str }}</div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="会员昵称" min-width="140">
                        <template slot-scope="scope">
                            <el-popover trigger="hover" placement="top" width="150">
                                <div class="flex">
                                    <div>头像：</div>
                                    <el-image
                                        :src="scope.row.avatar"
                                        style="width: 40px; height: 40px; border-radius: 50%"
                                    ></el-image>
                                </div>
                                <div class="flex m-t-10 col-top">
                                    <span class="flex-none">昵称：</span>
                                    <span>{{ scope.row.nickname }}</span>
                                </div>
                                <div class="m-t-10">
                                    <span>编号：</span>
                                    <span>{{ scope.row.user_sn }}</span>
                                </div>
                                <div slot="reference" class="name-wrapper">
                                    <span>{{ scope.row.nickname }}</span>
                                </div>
                            </el-popover>
                        </template>
                    </el-table-column>
                    <el-table-column label="评价等级" min-width="140">
                        <template slot-scope="scope">
                            <el-rate :value="scope.row.goods_comment" disabled></el-rate>
                            <div>{{ scope.row.comment_level }}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="评价内容" min-width="200">
                        <template slot-scope="scope">
                            <div>
                                <div style="line-height: 1.3em">
                                    {{ scope.row.comment }}
                                </div>
                                <div class="flex flex-wrap">
                                    <div
                                        class="m-r-8 m-t-8"
                                        v-for="(item, index) in scope.row.goods_comment_image"
                                        :key="index"
                                    >
                                        <el-image
                                            :preview-src-list="prevImage(scope.row.goods_comment_image)"
                                            style="width: 48px; height: 48px"
                                            :src="item.uri"
                                        ></el-image>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </el-table-column>

                    <el-table-column label="回复内容" min-width="200">
                        <template slot-scope="scope">
                            <div style="line-height: 1.3em">
                                {{ scope.row.reply }}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="comment_type_desc" label="评价类型" min-width="100"></el-table-column>

                    <el-table-column prop="reply_status_desc" label="回复状态" min-width="100"></el-table-column>
                    <el-table-column prop="status_desc" label="审核状态" min-width="100"></el-table-column>
                    <el-table-column fixed="right" label="操作" width="200">
                        <template slot-scope="scope">
                            <ls-dialog
                                v-if="scope.row.status == 0"
                                class="inline m-r-10"
                                :content="`确定审核通过 ？请谨慎操作。`"
                                @confirm="handdleStatus(1, [scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small">审核通过</el-button>
                            </ls-dialog>
                            <ls-dialog
                                v-if="scope.row.status == 0"
                                class="inline m-r-10"
                                :content="`确定审核拒绝 ？请谨慎操作。`"
                                @confirm="handdleStatus(2, [scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small">审核拒绝</el-button>
                            </ls-dialog>
                            <ls-dialog
                                v-if="scope.row.status == 1 && !scope.row.reply"
                                class="inline m-r-10"
                                title="回复评价"
                                width="600px"
                                @confirm="handleReply([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small" @click="reply = ''"
                                    >回复评价</el-button
                                >
                                <div>
                                    <el-input
                                        type="textarea"
                                        :rows="10"
                                        placeholder="请输入回复内容"
                                        v-model="reply"
                                    ></el-input>
                                </div>
                            </ls-dialog>
                            <ls-dialog
                                v-if="scope.row.status == 1 && scope.row.reply"
                                class="inline m-r-10"
                                title="编辑回复"
                                width="600px"
                                @confirm="handleReply([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small" @click="reply = scope.row.reply"
                                    >编辑回复</el-button
                                >
                                <div>
                                    <el-input
                                        type="textarea"
                                        :rows="10"
                                        placeholder="请输入回复内容"
                                        v-model="reply"
                                    ></el-input>
                                </div>
                            </ls-dialog>
                            <ls-dialog
                                class="inline"
                                :content="`确定删除该评价 ？请谨慎操作。`"
                                @confirm="handleDelete([scope.row.id])"
                            >
                                <el-button slot="trigger" type="text" size="small">删除评价</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="ls-content__footer m-t-16 flex row-between">
                <div class="btns flex">
                    <div class="m-r-16">
                        <el-checkbox
                            :value="selectIds.length == pager.lists.length"
                            @change="handleselectAll"
                            :disabled="!pager.lists.length"
                            >当页全选</el-checkbox
                        >
                    </div>
                    <ls-dialog
                        class="inline"
                        title="回复评价"
                        width="600px"
                        :disabled="disabledBtn"
                        @confirm="handleReply(selectIds)"
                    >
                        <el-button slot="trigger" size="small" :disabled="disabledBtn" @click="reply = ''"
                            >批量回复</el-button
                        >
                        <div>
                            <el-input
                                type="textarea"
                                :rows="10"
                                placeholder="请输入回复内容"
                                v-model="reply"
                            ></el-input>
                        </div>
                    </ls-dialog>
                    <ls-dialog
                        class="inline m-l-10"
                        content="确定批量删除？请谨慎操作"
                        :disabled="disabledBtn"
                        @confirm="handleDelete(selectIds)"
                    >
                        <el-button slot="trigger" size="small" :disabled="disabledBtn">批量删除</el-button>
                    </ls-dialog>
                </div>
                <div class="pagination">
                    <ls-pagination v-model="pager" @change="getList()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiGoodsCommentDel, apiGoodsCommentLists, apiGoodsCommentStatus, apiGoodsCommentReply } from '@/api/goods'
import { RequestPaging, timeFormat } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsDialog,
        LsPagination,
        DatePicker,
        ExportData
    }
})
export default class Evaluation extends Vue {
    $refs!: { table: any }
    pager = new RequestPaging()
    selectIds: any[] = []
    reply = ''

    queryObj: any = {
        goods_info: '',
        user_info: '',
        reply_status: '',
        verify_status: '',
        start_time: '',
        end_time: '',
        comment_type: ''
    }
    apiGoodsCommentLists = apiGoodsCommentLists
    get disabledBtn() {
        return !this.selectIds.length
    }

    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiGoodsCommentLists,
            params: {
                ...this.queryObj
            }
        })
    }
    get prevImage() {
        return (lists: any[]) => {
            return lists.map(item => item.uri)
        }
    }
    // 全选商品
    handleselectAll() {
        this.$refs.table.toggleAllSelection()
    }
    handleReset() {
        this.queryObj = {
            goods_info: '',
            user_info: '',
            reply_status: '',
            verify_status: '',
            start_time: '',
            end_time: '',
            comment_type: ''
        }
        this.getList()
    }
    handleSelect(val: any[]) {
        this.selectIds = val.map(item => item.id)
    }
    handleReply(id: any[]) {
        apiGoodsCommentReply({
            id,
            reply: this.reply
        }).then(() => {
            this.getList()
        })
    }
    handdleStatus(value: number, id: any[]) {
        apiGoodsCommentStatus({
            id,
            status: value
        }).then(() => {
            this.getList()
        })
    }

    handleDelete(id: any) {
        apiGoodsCommentDel({
            id
        }).then(() => {
            this.getList()
        })
    }

    created() {
        this.getList()
    }
}
</script>
<style lang="scss" scoped>
.ls-evaluation {
    &__top {
        padding-bottom: 6px;
    }
}
</style>
