<template>
    <div class="ls-goods">
        <div class="ls-goods__top ls-card">
            <el-alert
                title="温馨提示：1.管理分销商的等级，系统默认等级不能删除；2.删除分销等级时，会重新调整分销商等级为系统默认等级，请谨慎操作。"
                type="info"
                show-icon
                :closable="false"
            >
            </el-alert>
        </div>

        <div class="m-t-24 ls-card">
            <div class="m-b-24">
                <el-button
                    type="primary"
                    size="mini"
                    @click="$router.push('/distribution/distribution_grade_edit?mode=add&flag=0')"
                    >新增分销等级
                </el-button>
            </div>

            <el-table ref="paneTable" :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                <el-table-column prop="weights_desc" label="等级级别" width="180"></el-table-column>
                <el-table-column prop="name" label="等级名称" width="180"></el-table-column>
                <el-table-column prop="first_ratio" label="自购佣金比例" width="180">
                    <template slot-scope="scope">
                        <span>{{ scope.row.self_ratio }}%</span>
                    </template>
                </el-table-column>
                <el-table-column prop="first_ratio" label="一级佣金比例" width="180">
                    <template slot-scope="scope">
                        <span>{{ scope.row.first_ratio }}%</span>
                    </template>
                </el-table-column>
                <el-table-column prop="second_ratio" label="二级佣金比例" width="180">
                    <template slot-scope="scope">
                        <span>{{ scope.row.second_ratio }}%</span>
                    </template>
                </el-table-column>
                <el-table-column prop="member_num" label="分销商数" width="180"></el-table-column>
                <el-table-column fixed="right" label="操作" min-width="180">
                    <template slot-scope="scope">
                        <el-button
                            slot="trigger"
                            class="m-r-10"
                            type="text"
                            size="mini"
                            @click="
                                $router.push({
                                    path: '/distribution/distribution_grade_edit',
                                    query: {
                                        id: scope.row.id,
                                        mode: 'edit',
                                        disabled: true,
                                        flag: scope.row.is_default
                                    }
                                })
                            "
                            >详情</el-button
                        >

                        <el-button
                            slot="trigger"
                            class="m-r-10"
                            type="text"
                            size="mini"
                            @click="
                                $router.push({
                                    path: '/distribution/distribution_grade_edit',
                                    query: {
                                        id: scope.row.id,
                                        mode: 'edit',
                                        flag: scope.row.is_default
                                    }
                                })
                            "
                            >编辑</el-button
                        >

                        <ls-dialog
                            title="删除等级"
                            v-if="!scope.row.is_default"
                            class="inline m-l-10"
                            :content="`确定将此：${scope.row.name}删除？`"
                            @confirm="Del([scope.row.id])"
                        >
                            <el-button slot="trigger" type="text" size="mini">删除</el-button>
                        </ls-dialog>
                    </template>
                </el-table-column>
            </el-table>

            <div class="m-t-24 flex row-right">
                <ls-pagination v-model="pager" @change="getDistributionData()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import { apiDistributionGradeLists, apiDistributionGradeDel } from '@/api/distribution/distribution'
@Component({
    components: {
        LsPagination,
        LsDialog
    }
})
export default class DistributionGoods extends Vue {
    /** S Data **/

    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    // 获取分销列表
    getDistributionData(): void {
        this.pager.request({
            callback: apiDistributionGradeLists,
            params: {}
        })
    }

    Del(id: Number) {
        apiDistributionGradeDel({ id }).then(() => {
            this.getDistributionData()
        })
    }

    /** E Method **/

    created() {
        this.getDistributionData()
        // this.getGoodsOtherList();
    }
}
</script>

<style lang="scss" scoped>
.ls-goods {
    &__top {
        padding-bottom: 6px;
    }
    .goods-search {
        .ls-input-price {
            width: 180px;
        }
    }
    .ls-goods__content {
        padding-top: 0;
    }
}
</style>
