<!-- 选择用户 -->
<template>
    <div>
        <div class="ls-dialog__trigger" @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            coustom-class="ls-dialog__content"
            :title="title"
            :visible="visible"
            :width="width"
            :top="top"
            :modal-append-to-body="false"
            center
            :before-close="close"
            :close-on-click-modal="false"
            :append-to-body="true"
        >
            <!-- 弹窗主要内容-->
            <div class="">
                <el-form inline :model="form" label-width="70px" size="small" class="ls-form">
                    <el-form-item label="商品名称">
                        <el-input v-model="form.keyword" placeholder="请输入商品名称"></el-input>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getList">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                </el-form>

                <div class="list-table m-t-16">
                    <el-table
                        height="420"
                        :data="pager.lists"
                        style="width: 100%"
                        v-loading="pager.loading"
                        size="mini"
                        :header-cell-style="{ background: '#f5f8ff' }"
                        highlight-current-row
                        @current-change="handleCurrentChange"
                    >
                        <!-- <el-table-column type="selection" width="55">
						</el-table-column> -->
                        <el-table-column prop="" label="商品名称">
                            <template slot-scope="scope">
                                <div class="flex">
                                    <div class="" style="min-width: 40px; min-height: 40px">
                                        <el-image style="width: 40px; height: 40px" :src="scope.row.image"></el-image>
                                    </div>
                                    <div class="m-l-10">
                                        {{ scope.row.name }}
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="price" label="价格"> </el-table-column>
                        <el-table-column prop="total_stock" label="库存"> </el-table-column>
                    </el-table>
                </div>
                <div class="flex row-right m-t-16 row-right">
                    <ls-pagination v-model="pager" @change="getList()" />
                </div>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="close">取消</el-button>
                <el-button size="small" @click="onSubmit" type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiGoodsLists } from '@/api/goods'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class LsGoodsSelect extends Vue {
    // @Prop() value ? : number
    @Prop({
        default: '选择商品'
    })
    title!: string //弹窗标题
    @Prop({
        default: '880px'
    })
    width!: string | number //弹窗的宽度
    @Prop({
        default: '10vh'
    })
    top!: string | number //弹窗的距离顶部位置
    /** S Data **/
    visible = false

    // 查询表单
    form = {
        keyword: '' // 商品名
    }
    pager: RequestPaging = new RequestPaging()
    // 被选中的列
    currentRow = {}
    /** E Data **/

    /** S Methods **/
    // 触发打开弹窗
    onTrigger() {
        this.getList()
        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false
        // 重制表单内容
        this.form.keyword = ''
    }
    // 确定选中
    onSubmit() {
        if (JSON.stringify(this.currentRow) == '{}') {
            return this.$message.error('请选择商品')
        }
        if (this.currentRow == null) {
            return this.$message.error('请选择商品')
        }

        this.$emit('getGoods', this.currentRow)
        this.visible = false
    }

    // 获取用户列表
    getList(): void {
        this.pager
            .request({
                callback: apiGoodsLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {})
    }

    // 重置
    onReset() {
        this.form.keyword = ''

        this.getList()
    }
    // 获取选中的列
    handleCurrentChange(val: any) {
        //
        this.currentRow = val
    }
    /** E Methods **/
}
</script>

<style scoped lang="scss"></style>
