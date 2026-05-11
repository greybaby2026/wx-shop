<!-- 上级分销商调整 -->
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
        >
            <!-- 弹窗主要内容-->
            <div class="">
                <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
                    <el-form-item label="用户信息" prop="">
                        <div class="">{{ nickname }}({{ sn }})</div>
                    </el-form-item>
                    <el-form-item label="当前上级分销商" prop="">
                        <div class="">
                            {{ value }}
                        </div>
                    </el-form-item>
                    <el-form-item label="调整方式" prop="type" required>
                        <el-radio-group v-model="form.type">
                            <el-radio label="assign">指定分销商</el-radio>
                            <el-radio label="system">设置分销商为系统</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">选择“设置分销商为系统”时，用户的上级分销商会默认为系统</div>
                    </el-form-item>
                    <el-form-item label="选择分销商" prop="" required>
                        <div class="flex">
                            <div class="m-r-10" v-if="selectUser.nickname != ''">
                                {{ selectUser.nickname || '' }}({{ selectUser.sn || '' }})
                            </div>
                            <ls-user-select title="选择用户" :userId="form.user_id" @getUser="getUser">
                                <el-button slot="trigger" type="text" size="small">选择用户</el-button>
                            </ls-user-select>
                        </div>
                        <div class="muted xs m-r-16">选择新的分销商</div>
                    </el-form-item>
                </el-form>
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
import { apiUseradjustFirstLeader } from '@/api/user/user'
import LsUserSelect from '@/components/user/ls-user-select.vue'
@Component({
    components: {
        LsUserSelect
    }
})
export default class LsInvitationChange extends Vue {
    @Prop() value?: string // 当前上级分销商
    @Prop() nickname?: string // 用户信息-昵称
    @Prop() sn?: string // 用户信息-编号
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
        type: 'assign', // 调整方式
        first_id: '' // 分销商id
    }
    // 选中用户信息
    selectUser = {
        id: '',
        nickname: '',
        sn: ''
    }

    // 表单验证
    formRules = {
        type: [
            {
                required: true,
                message: '请选择',
                trigger: 'change'
            }
        ]
    }
    /** E Data **/
    // 获取用户id
    @Watch('userId', {
        immediate: true
    })
    getuserId(val: any) {
        // 初始值
        //this.form.value = val
        this.$set(this.form, 'user_id', val)
    }

    /** S Methods **/

    // 获取选择用户
    getUser(val: any) {
        // 未选中用户不修改
        if (val == null) {
            return
        }
        this.selectUser = val
        this.form.first_id = this.selectUser.id
    }

    // 修改上级分销商
    onSubmit() {
        this.$refs.formRef.validate((valid: any) => {
            if (!valid) {
                return
            }
            if (this.form.first_id == '' && this.form.type == 'assign') {
                return this.$message.error('请选择上级分销商')
            }
            apiUseradjustFirstLeader({
                ...this.form
            })
                .then((res: any) => {
                    this.$emit('refresh')
                })
                .catch((res: any) => {})
            this.visible = false
            // 重制表单内容
            ;(this.form.first_id = ''),
                (this.selectUser = {
                    id: '',
                    nickname: '',
                    sn: ''
                })
        })
    }

    onTrigger() {
        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false
        // 重制表单内容
        ;(this.form.first_id = ''),
            (this.selectUser = {
                id: '',
                nickname: '',
                sn: ''
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss"></style>
