<template>
    <div>
        <ls-dialog
            title="自定义名称"
            width="440px"
            top="30vh"
            ref="dialog"
            :async="true"
            @confirm="handleConfirm"
            @cancel="closeDialog"
        >
            <div class="p-r-20">
                <el-form ref="form" :model="form" label-width="80px" size="small">
                    <el-form-item label="配送方式">
                        <el-input v-model="mode" disabled></el-input>
                    </el-form-item>
                    <el-form-item label="自定义名称">
                        <el-input v-model="customName" placeholder="请输入自定义名称"></el-input>
                        <div class="xs muted">修改名称之后，手机端和PC端将显示自定义的名称</div>
                    </el-form-item>
                </el-form>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import { apideliveryWayset } from '@/api/setting/delivery'

@Component({
    components: {
        LsDialog
    }
})
export default class GoodsCollectionConfig extends Vue {
    form = {
        express: {
            is_express: 0,
            express_name: ''
        },
        selffetch: {
            is_selffetch: 0,
            selffetch_name: ''
        }
    }
    // 1-快递发货 2-上门自提
    mode = '快递发货'
    customName = ''
    $refs!: { dialog: any }
    closeDialog() {
        this.$refs.dialog.close()
    }
    openDialog(way: string, form: any) {
        this.mode = way
        this.form = form
        switch (way) {
            case '快递发货':
                this.customName = form.express.express_name
                break
            default:
                this.customName = form.selffetch.selffetch_name
                break
        }
        this.$refs.dialog.open()
    }

    async handleConfirm() {
        switch (this.mode) {
            case '快递发货':
                this.form.express.express_name = this.customName
                break
            default:
                this.form.selffetch.selffetch_name = this.customName
                break
        }
        await apideliveryWayset({ ...this.form.express, ...this.form.selffetch })
        this.closeDialog()
    }
}
</script>

<style lang="scss" scoped></style>
