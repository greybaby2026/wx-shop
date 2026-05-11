<template>
    <div>
        <div @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>

        <el-dialog
            title="等级调整"
            :visible="visible"
            width="50vw"
            :top="top"
            :modal-append-to-body="false"
            center
            :before-close="close"
            :close-on-click-modal="false"
        >
            <!-- 用户数据 -->
            <div v-loading="distributionList.length == 0" class="content">
                <el-form ref="distributionData" :model="form" label-width="220px" size="small">
                    <el-form-item label="用户信息：" prop="">
                        {{ distributionList.user.nickname }}（{{ distributionList.user.sn }})
                    </el-form-item>

                    <el-form-item label="当前分销等级" prop="">
                        {{ distributionList.user.level_name }}
                    </el-form-item>

                    <el-form-item label="调整后分销等级" prop="" required>
                        <el-select v-model="form.level_id" placeholder="全部">
                            <el-option
                                v-for="item in distributionList.levels"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-form>
            </div>
            <!-- 底部弹窗页脚 -->
            <div slot="footer">
                <el-button size="small" @click="handleEvent('cancel')">取消</el-button>
                <el-button size="small" @click="handleEvent('confirm')" type="primary">确认</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import {
    apiDistributionAdjustLevelInfo, //分销等级
    apiDistributionAdjustLevel
} from '@/api/distribution/distribution'
@Component
export default class UserSelect extends Vue {
    @Prop({ default: '25vh' }) top!: string | number //弹窗的距离顶部位置
    @Prop({ default: 1 }) id!: string | number //弹窗的距离顶部位置

    /** S Data **/
    visible: any = false //是否显示

    distributionList: any = {
        user: {
            level_name: '默认等级(1级)',
            nickname: '',
            sn: ''
        }
    } //分销等级列表

    form: any = {
        level_id: ''
    }

    /** E Data **/

    /** S Method **/

    // 分销等级
    getDistributionAdjustLevelInfo() {
        apiDistributionAdjustLevelInfo({ user_id: this.id }).then((res: any) => {
            this.distributionList = res
        })
    }

    // 点击取消
    handleEvent(type: 'cancel' | 'confirm') {
        if (type === 'cancel') {
            this.close()
        }
        if (type === 'confirm') {
            if (this.form.level_id == '') {
                return this.$message.error('请选择分销等级！')
            }
            this.form.user_id = this.id
            apiDistributionAdjustLevel({
                ...this.form
            }).then(() => {
                this.$emit('update', '')
            })
            this.close()
        }
    }

    // 打开弹窗
    onTrigger() {
        this.getDistributionAdjustLevelInfo()
        this.visible = true
    }

    // 关闭用户选择弹窗
    close() {
        this.visible = false
    }

    /** E Method **/
}
</script>

<style lang="scss" scoped>
.content {
    margin: 50px 0;
}
</style>
