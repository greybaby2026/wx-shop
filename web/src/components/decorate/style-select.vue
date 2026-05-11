<template>
    <div>
        <ls-dialog :title="title" width="900px" top="20vh" @confirm="onConfirm">
            <div slot="trigger">
                <slot name="trigger"></slot>
            </div>
            <el-scrollbar class="ls-scrollbar" style="height: 500px">
                <div class="style-select">
                    <el-radio-group v-model="select">
                        <div class="flex flex-wrap" style="align-items: stretch">
                            <div
                                :style="{
                                    width: itemWidth
                                }"
                                class="flex item flex-col"
                                v-for="(item, index) in data"
                                :key="index"
                            >
                                <div
                                    class="item-img flex col-center m-b-20 flex-1"
                                    :class="{
                                        select: select == item.value
                                    }"
                                    @click="select = item.value"
                                >
                                    <img style="width: 100%" :src="item.img" alt="" />
                                </div>
                                <el-radio :label="item.value">{{ item.text }}</el-radio>
                            </div>
                        </div>
                    </el-radio-group>
                </div>
            </el-scrollbar>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class StyleSelect extends Vue {
    /** S data **/
    @Prop() value!: number
    @Prop({ default: '选择风格' }) title!: string

    @Prop() data!: any[]
    @Prop({ default: '33.3%' }) itemWidth!: string

    /** E data **/
    select = 0
    /** S watch **/
    @Watch('value', { immediate: true })
    valueChange(val: number) {
        this.select = val
    }
    /** E watch **/

    /** S methods **/
    onConfirm() {
        this.$emit('input', this.select)
    }
    /** E methods **/
}
</script>
<style lang="scss" scoped>
.style-select {
    .item {
        .item-img {
            background: #f9f9f9;
            padding: 20px 15px;
            margin-right: 20px;
            cursor: pointer;
            border: 1px solid transparent;
            &.select {
                border-color: $--color-primary;
            }
            img {
                width: 100%;
            }
        }
    }
}
</style>
