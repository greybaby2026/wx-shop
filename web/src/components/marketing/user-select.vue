<template>
    <div>
        <div class="ls-dialog__trigger" @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            coustom-class="ls-dialog__content"
            title="用户选择"
            :visible="visible"
            width="70vw"
            :top="top"
            :modal-append-to-body="false"
            center
            :before-close="close"
            :close-on-click-modal="false"
            append-to-body
        >
            <!-- 搜索用户头部 -->
            <div class="user-search m-t-16 flex row-between">
                <div style="height: 53px">
                    <el-checkbox v-model="selectAll">全选</el-checkbox>
                </div>
                <el-form ref="form" inline :model="userSearchData" label-width="80px" size="small">
                    <el-form-item label="用户搜索">
                        <el-input class="header-input" v-model="userSearchData.nickname" placeholder="请输入用户名称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getUserInfoList(1)">查询</el-button>
                        <el-button size="small" @click="resetuserSearchData">重置</el-button>
                    </el-form-item>
                </el-form>
            </div>

            <!-- 用户数据 -->
            <div>
                <el-table
                    ref="paneTable"
                    style="width: 100%"
                    height="500"
                    size="mini"
                    :data="pager.lists"
                    v-loading="pager.loading"
                >
                    <el-table-column fixed="left" width="55">
                        <template slot-scope="scope">
                            <el-checkbox :value="selectItem(scope.row)" @change="handleSelect($event, scope.row)">
                            </el-checkbox>
                        </template>
                    </el-table-column>
                    <el-table-column prop="sn" label="用户编号" min-width="120"> </el-table-column>
                    <el-table-column label="用户昵称" min-width="120">
                        <template slot-scope="scope">
                            <div class="flex">
                                <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                                <div class="m-l-10">
                                    {{ scope.row.nickname }}
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="user_money" label="用户余额" min-width="120"> </el-table-column>
                    <el-table-column prop="mobile" label="手机号码" min-width="120"> </el-table-column>
                    <el-table-column prop="is_distribution" label="分销商" min-width="120">
                        <template slot-scope="scope">
                            {{ scope.row.is_distribution == 1 ? '是' : '否' }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" label="注册时间" min-width="120"> </el-table-column>
                </el-table>
            </div>

            <!-- 分页 -->
            <div class="m-t-24 flex row-center">
                <ls-pagination v-model="pager" @change="getUserInfoList()" />
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="handleEvent('cancel')" type="primary">取消并关闭</el-button>
                <el-button size="small" @click="handleEvent('confirm')" type="primary">确认选择</el-button>
            </div>
        </el-dialog>

        <!-- 选中的用户table -->
        <el-table
            class="m-t-24"
            v-if="selectIds.length != 0"
            height="250"
            ref="paneTable"
            style="width: 100%"
            size="mini"
            :data="selectIds"
        >
            <el-table-column prop="sn" label="用户编号"> </el-table-column>
            <el-table-column label="用户昵称" min-width="120">
                <template slot-scope="scope">
                    <div class="flex">
                        <el-image :src="scope.row.avatar" style="width: 34px; height: 34px"></el-image>
                        <div class="m-l-10">
                            {{ scope.row.nickname }}
                        </div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column prop="is_distribution" label="分销商" width="80">
                <template slot-scope="scope">
                    {{ scope.row.is_distribution == 1 ? '是' : '否' }}
                </template>
            </el-table-column>
            <el-table-column prop="mobile" label="手机号码"> </el-table-column>
            <el-table-column label="操作" width="110">
                <template slot-scope="scope">
                    <el-button @click="removeSelectGoods(scope.row.id)" type="text" size="small">移除</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import { apiSelectUserLists } from '@/api/user/user'
@Component({
    components: {
        LsPagination
    }
})
export default class UserSelect extends Vue {
    @Prop({ default: '5vh' }) top!: string | number //弹窗的距离顶部位置

    @Prop({ default: 0 }) is_distribution!: string | number //用户选择是否非分销商（目前在分销->分销商中以及优惠券，卖家发放优惠券选择的用户中有使用到

    /** S Data **/
    visible = false //是否

    userSearchData = {
        // 搜索的数据
        nickname: ''
    }

    pager = new RequestPaging() // 用户数据分页

    selectIds: any[] = [] //当前选中的用户数组

    /** E Data **/

    /** S Computed **/

    // 获取当前的选择
    get selectItem() {
        return (row: any) => {
            return this.selectIds.some(item => item.id === row.id)
        }
    }

    // 获取全选
    get selectAll() {
        const ids: any[] = this.selectIds.map(item => item.id)
        return this.pager.lists.every(item => ids.includes(item.id))
    }

    // 设置全选
    set selectAll(val) {
        if (val) {
            // 深度克隆再赋值，防止table表数据跟着更新
            this.selectIds = JSON.parse(JSON.stringify(this.pager.lists))
        } else {
            this.selectIds = []
        }
    }

    /** E Computed **/

    /** S Method **/

    // 选择当前用户
    handleSelect(event: boolean, row: any) {
        if (event) {
            this.selectIds.push(row)
        } else {
            const index = this.selectIds.findIndex(item => item.id === row.id)
            if (index != -1) {
                this.selectIds.splice(index, 1)
            }
        }
    }

    // 移除选中的用户
    removeSelectGoods(id: number) {
        const index: any = this.selectIds.map(item => item.id == id)
        if (index != -1) {
            this.selectIds.splice(index, 1)
        }
    }

    // 获取用户数据
    getUserInfoList(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiSelectUserLists,
            params: {
                ...this.userSearchData,
                is_distribution: this.is_distribution
            }
        })
    }

    // 重置搜索
    resetuserSearchData() {
        Object.keys(this.userSearchData).map(key => {
            this.$set(this.userSearchData, key, '')
        })
        this.getUserInfoList()
    }

    // 选中用户
    selectionChange(val: any[]) {
        this.selectIds = val.map(item => item)
    }

    // 点击取消
    handleEvent(type: 'cancel' | 'confirm') {
        if (type === 'cancel') {
            this.selectIds = []
            this.close()
        }
        if (type === 'confirm') {
            this.$emit('input', this.selectIds)
            this.close()
        }
    }

    // 打开弹窗
    onTrigger() {
        this.visible = true
        this.getUserInfoList()
    }

    // 关闭用户选择弹窗
    close() {
        this.visible = false
    }

    /** E Method **/

    created() {
        this.getUserInfoList()
    }
}
</script>

<style lang="scss" scoped>
.header-input {
    width: 220px !important;
}
</style>
