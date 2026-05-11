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
                    <el-form-item label="用户信息">
                        <el-select v-model="isNameSN" placeholder="用户编号" style="width: 120px">
                            <el-option label="用户编号" value="0"></el-option>
                            <el-option label="用户昵称" value="1"></el-option>
                        </el-select>
                        <el-input v-if="isNameSN == 0" v-model="form.sn" placeholder="请输入用户编号查询"> </el-input>
                        <el-input
                            v-if="isNameSN == 1"
                            v-model="form.nickname"
                            placeholder="请输入用户昵称查询"
                        ></el-input>
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
                        <el-table-column prop="sn" label="用户编号" min-width="" width=""> </el-table-column>
                        <el-table-column prop="nickname" label="用户昵称" min-width="" width=""> </el-table-column>
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
import { apiSelectUserLists } from '@/api/user/user'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class LsUserSelect extends Vue {
    // @Prop() value ? : number
    @Prop() userId?: number // 用户id
    @Prop({
        default: ''
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
    $refs!: {
        formRef: any
    }

    isNameSN = '' // 0-编号， 1-昵称
    // 查询表单
    form = {
        user_id: '', // 用户id
        sn: '', // 用户编号
        nickname: '' // 用户昵称
    }
    pager: RequestPaging = new RequestPaging()
    // 被选中的列
    currentRow = {}
    // 表单验证
    formRules = {
        // labelValue: [{
        // 	required: true,
        // 	message: '请选择用户标签',
        // 	trigger: 'change'
        // }],
    }
    /** E Data **/

    @Watch('userId', {
        immediate: true
    })
    getuserId(val: any) {
        // 初始值
        //
        //this.form.value = val
        this.$set(this.form, 'user_id', val)
    }

    // 监听用户信息查询框条件
    @Watch('isNameSN', {
        immediate: true
    })
    getChange(val: any) {
        // 初始值
        this.form.sn = ''
        this.form.nickname = ''
        // this.form.mobile = ''
    }

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
        this.form.sn = ''
        this.form.nickname = ''
    }
    // 确定选中
    onSubmit() {
        if (JSON.stringify(this.currentRow) == '{}') {
            return this.$message.error('请选择用户')
        }
        if (this.currentRow == null) {
            return this.$message.error('请选择用户')
        }

        //
        this.$emit('getUser', this.currentRow)
        this.visible = false
    }

    // 获取用户列表
    getList(): void {
        this.pager
            .request({
                callback: apiSelectUserLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {})
    }

    // 重置
    onReset() {
        this.form.sn = ''
        this.form.nickname = ''

        this.getList()
    }
    // 获取选中的列
    handleCurrentChange(val: any) {
        //
        this.currentRow = val
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss"></style>
