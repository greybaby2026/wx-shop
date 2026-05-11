<!-- 用户详情·钱包调整 -->
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
            @close="close"
        >
            <!-- 弹窗主要内容-->
            <div class="">
                <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
                    <el-form-item label="标签调整" prop="labelValue">
                        <el-select v-model="form.labelValue" multiple placeholder="请选择">
                            <div v-for="(val, key) in userLabelList" :key="key">
                                <el-option :label="val.name" :value="val.id"></el-option>
                            </div>
                        </el-select>
                        <div class="xxs lighter">可以多选用户标签</div>
                    </el-form-item>
                </el-form>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="close">取消</el-button>
                <el-button size="small" @click="changeUserLabel" type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiUserSearchList, apiUserSetUserLabel } from '@/api/user/user'
@Component({
    components: {}
})
export default class LsLabelChange extends Vue {
    @Prop() value?: number
    @Prop() userId?: number // 用户id
    @Prop({
        default: ''
    })
    title!: string //弹窗标题
    @Prop({
        default: '660px'
    })
    width!: string | number //弹窗的宽度
    @Prop({
        default: '20vh'
    })
    top!: string | number //弹窗的距离顶部位置
    /** S Data **/
    visible = false
    $refs!: {
        formRef: any
    }
    form = {
        user_id: '', // 用户id
        labelValue: [] // 用户选中的标签
    }
    // 选中用户标签

    // 用户选择框数据
    userLevelList = {}
    userLabelList = {}

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
        //this.form.value = val
        this.$set(this.form, 'user_id', val)
    }

    /** S Methods **/
    // 获取多选框列表
    getUserSearchList() {
        apiUserSearchList().then((res: any) => {
            this.userLevelList = res.user_level_list
            this.userLabelList = res.user_label_list
        })
    }

    // 设置用户标签
    changeUserLabel() {
        this.$refs.formRef.validate((valid: any) => {
            if (!valid) {
                return
            }
            if (!this.form.labelValue.length) {
                return this.$message.error('请选择标签')
            }

            apiUserSetUserLabel({
                user_id: this.userId,
                label_ids: this.form.labelValue
            })
                .then(res => {
                    this.$emit('refresh')
                    this.visible = false
                })
                .catch(res => {})
        })
    }

    onTrigger() {
        this.getUserSearchList()
        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false
        this.form.labelValue = []
        // 重制表单内容
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss"></style>
