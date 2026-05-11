<!-- 直播商品 -->
<template>
    <div class="live-broadcast-goods">
        <!-- 顶部 -->
        <div class="ls-card">
            <el-alert class="xxl" type="info" :closable="false" show-icon>
                <template slot="title">
                    <div class="iconSize">温馨提示：</div>
                    <div class="iconSize">1.同步商品库每天最多可同步10000次，请合理使用同步次数；</div>
                    <div class="iconSize">2.直播商品每天最多可添加500次，删除商品每天最多可删除1000次。</div>
                </template>
            </el-alert>
        </div>

        <!--  -->
        <div class="ls-card m-t-16">
            <el-tabs v-model="form.type" v-loading="pager.loading" @tab-click="getList">
                <!-- <el-tab-pane label="全部" name="0">
					<goods-pane v-model="pager.lists" :pager="pager" @refresh="getList" @onDel="onDelete()"
						@add="addLBGoods" @synchronization="synchronizationLBGoods" />
				</el-tab-pane> -->
                <!-- <el-tab-pane :label="`审核中(${extend.wait})`" name="1"> -->
                <el-tab-pane label="审核中" name="1">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        @refresh="getList"
                        @onDel="onDelete"
                        @add="addLBGoods"
                        @synchronization="synchronizationLBGoods"
                        :type="form.type"
                    />
                </el-tab-pane>
                <!-- <el-tab-pane :label="`审核通过(${extend.wait})`" name="2"> -->
                <el-tab-pane label="审核通过" name="2">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        @refresh="getList"
                        @onDel="onDelete"
                        @add="addLBGoods"
                        @synchronization="synchronizationLBGoods"
                        :type="form.type"
                    />
                </el-tab-pane>
                <!-- <el-tab-pane lazy :label="`审核驳回(${extend.wait})`" name="3"> -->
                <el-tab-pane lazy label="审核驳回" name="3">
                    <goods-pane
                        v-model="pager.lists"
                        :pager="pager"
                        @refresh="getList"
                        @onDel="onDelete"
                        @add="addLBGoods"
                        @synchronization="synchronizationLBGoods"
                        :type="form.type"
                    />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import { apiLiveGoodsLists, apiLiveGoodsDel } from '@/api/application/live_broadcast'
import { PageMode } from '@/utils/type'
import GoodsPane from '@/components/live-broadcast/goods-pane.vue'
@Component({
    components: {
        GoodsPane
    }
})
export default class LiveBroadcastGoods extends Vue {
    // 分页查询
    pager: RequestPaging = new RequestPaging()

    form = {
        type: '1'
    }

    extend = {
        all: 0,
        wait: 0,
        ing: 0,
        end: 0
    }

    // 添加直播商品
    addLBGoods() {
        this.$router.push({
            path: '/live_broadcast/add_broadcast_goods',
            query: {
                mode: PageMode.ADD
            }
        })
    }

    // 同步商品库
    synchronizationLBGoods() {
        // this.$set(this.pager, 'page', 1)
        this.getList()
    }

    // 删除
    onDelete(value: any) {
        apiLiveGoodsDel({
            goods_id: value.goods_id
        })
            .then(() => {
                this.getList()
                // this.$message.success("删除成功!");
            })
            .catch(() => {
                //this.$message.error("删除失败")
            })
    }

    // 获取列表
    getList() {
        // 分页请求
        this.pager
            .request({
                callback: apiLiveGoodsLists,
                params: {
                    ...this.form
                }
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
.iconSize {
    padding: 3px 0;
}
</style>
