<template>
    <div>
        <ls-dialog
            title="接口配置"
            width="600px"
            top="30vh"
            ref="dialog"
            :async="true"
            @confirm="handleConfirm"
            @cancel="closeDialog"
        >
            <div class="p-l-20 p-r-20">
                <el-form ref="form" :model="form" label-width="120px" size="small">
                    <el-form-item label="99Api apiKey">
                        <el-input
                            style="width: 380px"
                            v-model="form.key_99api"
                            placeholder="复制商品第三方平台接口秘钥"
                        ></el-input>
                        <div class="xs muted">
                            采集的API接口请前往
                            <el-link
                                href="https://user.99api.com/login?log=5&referee=25463"
                                target="_blank"
                                type="primary"
                                :underline="false"
                            >
                                99API
                            </el-link>
                            官网注册
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import { apiSetGatherKey } from '@/api/application/goods_collection'
@Component({
    components: {
        LsDialog
    }
})
export default class GoodsCollectionConfig extends Vue {
    form = {
        key_99api: ''
    }
    $refs!: { dialog: any }
    @Watch('value')
    closeDialog() {
        this.$refs.dialog.close()
    }
    openDialog(key_99api: string) {
        this.form.key_99api = key_99api
        this.$refs.dialog.open()
    }

    handleConfirm() {
        apiSetGatherKey({
            key_99api: this.form.key_99api
        })
            .then(() => {
                this.closeDialog()
                this.$emit('getGatherKey')
            })
            .finally(() => {
                this.form.key_99api = ''
            })
    }
}
</script>

<style lang="scss" scoped></style>
