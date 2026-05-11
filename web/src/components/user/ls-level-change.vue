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
        >
            <!-- 弹窗主要内容-->
            <div class="">
                <el-form :rules="valueRules" ref="valueRef" :model="form" label-width="120px" size="small">
                    <el-form-item label="当前等级">
                        <div>{{ form.value }}</div>
                    </el-form-item>
                    <el-form-item label="等级调整" prop="level">
                        <el-select class="ls-select" v-model="form.level" placeholder="请选择等级">
                            <div v-for="(value, key) in userLevelList" :key="key">
                                <el-option :label="value.name" :value="value.id"></el-option>
                            </div>
                        </el-select>
                        <div class="xxs lighter">选择调整后的等级</div>
                    </el-form-item>
                    <el-form-item label="调整后等级">
                        <div>{{ lastValue }}</div>
                    </el-form-item>
                    <el-form-item label="备注">
                        <el-input
                            class="ls-input"
                            type="textarea"
                            :rows="3"
                            placeholder="请输入备注"
                            v-model="form.remark"
                            style="width: 300px"
                        >
                        </el-input>
                    </el-form-item>
                </el-form>
            </div>

            <!-- 底部弹窗页脚 -->
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="close">取消</el-button>
                <el-button size="small" @click="changeUserLevel" type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { apiUserSearchList, apiUserSetInfo } from '@/api/user/user'
@Component({
    components: {}
})
export default class LsLevelChange extends Vue {
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
        valueRef: any
    }
    form = {
        user_id: '', // 用户id
        level: '', // 用户等级
        remark: '', // 备注
        value: '' // 初始值
    }
    // 选中用户标签
    labelValue = []
    // 用户选择框数据
    userLevelList = []

    // 表单验证
    valueRules = {
        level: [
            {
                required: true,
                message: '请选择用户等级',
                trigger: 'change'
            }
        ]
    }
    // 修改后的值
    get lastValue(): string {
        //
        let total: any = [{ name: '' }]
        // let total =((this.userLevelList as any)[this.form.level])
        //
        total = this.userLevelList.filter((item: any) => {
            //
            return item?.id == this.form.level
        })
        const res = total.map((item: any) => item.name)

        return res.toString()
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

    @Watch('value', {
        immediate: true
    })
    getValue(val: any) {
        // 初始值
        // this.form.value = val
        this.$set(this.form, 'value', val)
    }

    /** S Methods **/
    // 获取多选框列表
    getUserSearchList() {
        apiUserSearchList().then((res: any) => {
            this.userLevelList = res.user_level_list
        })
    }

    // 设置用户等级
    changeUserLevel() {
        this.$refs.valueRef.validate((valid: any) => {
            if (!valid) {
                return
            }

            // if(this.form.level == '') {
            // 	return this.$message.error("请选择用户等级")
            // }

            apiUserSetInfo({
                user_id: this.form.user_id,
                field: 'level',
                value: this.form.level
            })
                .then((res: any) => {
                    this.$emit('refresh')
                })
                .catch((res: any) => {})
            this.visible = false
            this.form.remark = ''
        })
    }

    onTrigger() {
        this.getUserSearchList()
        this.visible = true
    }

    // 关闭弹窗
    close() {
        this.visible = false

        // 重制表单内容
        this.form.remark = ''
        this.form.level = ''
    }
    /** E Methods **/

    /** S Life Cycle **/
    /** E Life Cycle **/
}
</script>

<style scoped lang="scss"></style>
