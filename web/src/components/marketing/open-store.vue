<template>
    <div>
        <div @click="onTrigger">
            <!-- 触发弹窗 -->
            <slot name="trigger"></slot>
        </div>

        <el-dialog
            :title="title"
            :visible="visibles"
            width="70vw"
            :top="tops"
            :modal-append-to-body="false"
            center
            :before-close="close"
            :close-on-click-modal="false"
        >
            <!-- 用户数据 -->
            <div v-loading="distributionList.length == 0" class="content">
                <el-form ref="distributionData" :model="form" label-width="120px" size="small">
                    <el-form-item label="用户信息：" required>
                        <user-select v-model="selectIds" ref="user_select">
                            <el-button slot="trigger" size="mini" type="primary">选择用户</el-button>
                        </user-select>
                    </el-form-item>

                    <el-form-item label="分销等级：" required>
                        <el-select v-model="form.level_id" placeholder="全部">
                            <el-option
                                v-for="item in distributionList"
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
    apiDistributionGradeLists, //分销等级
    apiDistributionOpen //开通分销
} from '@/api/distribution/distribution'
import UserSelect from '@/components/marketing/user-select.vue'
@Component({
    components: {
        UserSelect
    }
})
export default class OpenStore extends Vue {
    @Prop({ default: '20vh' }) tops!: string | number //弹窗的距离顶部位置
    @Prop({ default: '等级调整' }) title!: string | number //弹窗顶部名称标题

    /** S Data **/
    visibles: any = false //是否显示

    selectIds: any = [] //用户列表

    distributionList: any = [] //分销等级列表

    form: any = {
        ids: [],
        level_id: ''
    }

    /** E Data **/

    /** S Method **/

    // 分销等级
    getGoodsOtherList() {
        apiDistributionGradeLists({}).then((res: any) => {
            this.distributionList = res.lists
        })
    }

    // 点击取消
    handleEvent(type: 'cancel' | 'confirm') {
        if (type === 'cancel') {
            this.close()
        }
        if (type === 'confirm') {
            if (this.selectIds.length == 0) {
                return this.$message.error('请选择用户！')
            }
            if (this.form.level_id == '') {
                return this.$message.error('请选择分销等级！')
            }
            this.form.ids = this.selectIds.map((item: any) => item.id)
            //  开通分销
            apiDistributionOpen({
                ...this.form
            }).then(() => {
                ;(this.$refs.user_select as any).selectIds = []
                this.selectIds = []
                this.form.level_id = ''
                this.$emit('update', '')
                this.close()
            })
        }
    }

    // 打开弹窗
    onTrigger() {
        this.getGoodsOtherList()
        this.visibles = true
    }

    // 关闭用户选择弹窗
    close() {
        this.visibles = false
    }

    /** E Method **/
}
</script>

<style lang="scss" scoped>
.content {
    margin: 50px 0;
}
</style>
