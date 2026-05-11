<!-- 直播间 -->
<template>
    <div class="live-broadcast">
        <!-- 顶部 -->
        <div class="ls-card">
            <el-alert class="xxl" type="info" :closable="false">
                <template slot="title">
                    <div class="iconSize">温馨提示：</div>
                    <div class="iconSize">1.同步直播间信息每天最多可同步100000次，请合理使用同步次数；</div>
                    <div class="iconSize flex col-top">
                        <div class="">2.扫描微信直播二维码开启直播：</div>
                        <el-image
                            style="width: 100px; height: 100px"
                            :src="weChatZhiBoImg"
                            :preview-src-list="[weChatZhiBoImg]"
                        ></el-image>
                    </div>
                </template>
            </el-alert>
        </div>

        <div class="ls-card m-t-16">
            <div class="list-header">
                <el-button size="small" type="primary" @click="onAdd()">创建直播间</el-button>
                <el-button size="small" @click="onReset()">同步直播间</el-button>
            </div>
            <div class="list-table m-t-16">
                <el-table
                    :data="pager.lists"
                    style="width: 100%"
                    size="mini"
                    v-loading="pager.loading"
                    :header-cell-style="{ background: '#f5f8ff' }"
                >
                    <el-table-column prop="room_id" label="直播间ID"> </el-table-column>
                    <el-table-column prop="name" label="直播间标题"> </el-table-column>
                    <el-table-column prop="anchor_name" label="主播昵称"> </el-table-column>
                    <el-table-column prop="start_time" label="开播时间" min-width="120"> </el-table-column>
                    <el-table-column prop="end_time" label="结束时间" min-width="120"> </el-table-column>
                    <el-table-column prop="goods" label="商品数量"> </el-table-column>
                    <el-table-column prop="live_status" label="状态"> </el-table-column>
                    <el-table-column fixed="right" label="操作" min-width="100">
                        <template slot-scope="scope">
                            <ls-dialog
                                class="m-l-10 inline"
                                :content="`确定删除直播间：${scope.row.name}`"
                                @confirm="onDelete(scope.row)"
                            >
                                <el-button type="text" size="small" slot="trigger">删除</el-button>
                            </ls-dialog>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <!-- 底部分页栏  -->
            <div class="flex row-right m-t-16 row-right">
                <ls-pagination v-model="pager" @change="getList()" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import { apiLiveRoomLists, apiLiveRoomDel } from '@/api/application/live_broadcast'
import { PageMode } from '@/utils/type'
import LsDialog from '@/components/ls-dialog.vue'
import config from '@/config'
@Component({
    components: {
        LsPagination,
        LsDialog
    }
})
export default class LiveBroadcast extends Vue {
    srcList = ['https://fuss10.elemecdn.com/8/27/f01c15bb73e1ef3793e64e6b7bbccjpeg.jpeg']
    weChatZhiBoImg = require('@/assets/images/wechatzhibo.png')

    // 分页查询
    pager: RequestPaging = new RequestPaging()

    // 创建直播间
    onAdd() {
        this.$router.push({
            path: '/live_broadcast/edit',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 删除
    onDelete(value: any) {
        apiLiveRoomDel({
            room_id: value.room_id
        })
            .then(() => {
                this.getList()
                // this.$message.success("删除成功!");
            })
            .catch(() => {
                //this.$message.error("删除失败")
            })
    }

    onReset() {
        // this.$set(this.pager, 'page', 1)
        this.getList()
    }

    // 获取列表
    getList() {
        // 分页请求
        this.pager
            .request({
                callback: apiLiveRoomLists
            })
            .catch(() => {
                this.$message.error('数据请求失败，刷新重载')
            })
    }

    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.live-broadcast {
    .iconSize {
        padding: 3px 0;
    }
}
</style>
