<template>
    <ls-dialog
        class="link-select"
        title="选择跳转链接"
        width="1050px"
        top="20vh"
        ref="dialog"
        :disabled="disabled"
        :async="true"
        @confirm="onConfirm"
    >
        <div class="link-select__trigger" slot="trigger">
            <el-input
                style="width: 100%"
                :value="linkName"
                placeholder="请选择跳转链接"
                size="small"
                readonly
                :disabled="disabled"
            >
                <i v-if="linkName" class="el-icon-close el-input__icon" slot="suffix" @click.stop="handleClear"> </i>
                <i v-else class="el-icon-arrow-right el-input__icon" slot="suffix"> </i>
            </el-input>
        </div>
        <detail :client="client" v-model="link" />
    </ls-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import Detail from './detail.vue'
@Component({
    components: {
        LsDialog,
        Detail
    }
})
export default class LinkSelect extends Vue {
    $refs!: { dialog: any }
    @Prop({ default: () => ({}) }) value!: any
    @Prop({ default: false }) disabled!: boolean
    @Prop({ default: 'mobile' }) client!: string
    link: any = {}

    get linkName() {
        const { type, name, params, path } = this.value
        if (!name) {
            return ''
        }
        switch (type) {
            case 'custom':
                return params.url || path
            default:
                return `${name}${params && params.name ? '(' + params.name + ')' : ''}`
        }
    }

    @Watch('value', { immediate: true })
    valueChange(val: any) {
        this.link = JSON.parse(JSON.stringify(val))
    }
    onConfirm() {
        const { type, params, path } = this.link

        switch (type) {
            case 'shop':
                if (!path) {
                    this.$message.warning('请选择商城页面')
                    return
                }
                break
            case 'goods':
                if (!params.name) {
                    this.$message.warning('请选择商品')
                    return
                }
                break
            case 'seckill':
                if (!params.name) {
                    this.$message.warning('请选择秒杀商品')
                    return
                }
                break
            case 'team':
                if (!params.name) {
                    this.$message.warning('请选择拼团商品')
                    return
                }
                break
            case 'marking':
                if (!path) {
                    this.$message.warning('请选择营销页面')
                    return
                }
                break
            case 'draw':
                if (!params.name) {
                    this.$message.warning('请选择积分抽奖')
                    return
                }
                break
            case 'category':
                if (!params.name) {
                    this.$message.warning('请选择分类')
                    return
                }
                break
            case 'custom':
                if (!params.url && !path) {
                    this.$message.warning('请输入自定义链接')
                    return
                }
                break

            case 'page':
                if (!params.name) {
                    this.$message.warning('请选择微页面')
                    return
                }
                break
        }
        this.$refs.dialog.close()
        this.$emit('input', this.link)
    }
    handleClear() {
        if (this.disabled) {
            return
        }
        this.$emit('input', {})
    }
}
</script>

<style scoped lang="scss">
.link-select {
    &__trigger {
        ::v-deep.el-input {
            input {
                cursor: pointer;
            }
            .el-input__icon {
                cursor: pointer;
            }
        }
    }
}
</style>
