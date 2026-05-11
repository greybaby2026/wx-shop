<template>
    <div class="area-panel">
        <el-scrollbar class="ls-scrollbar" style="height: 450px">
            <div class="area-tree">
                <el-tree
                    ref="tree"
                    node-key="value"
                    :data="option"
                    icon-class="el-icon-arrow-right"
                    :filter-node-method="filterMethod"
                >
                    <div class="flex flex-1" slot-scope="{ node, data }">
                        <span class="flex-1">{{ node.label }}</span>
                        <div class="flex-none m-r-10">
                            <el-button
                                type="text"
                                size="mini"
                                v-if="type == 'select'"
                                @click.stop="onCancel(node, data)"
                            >
                                取消
                            </el-button>
                            <el-button type="text" size="mini" v-else @click.stop="onSelect(node, data)">
                                选择
                            </el-button>
                        </div>
                    </div>
                </el-tree>
            </div>
        </el-scrollbar>
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
export default class AreaPanel extends Vue {
    $refs!: { tree: any }
    @Prop({ default: () => {} }) option!: any[]
    @Prop() type!: string
    @Prop() filterText!: string
    @Prop() areaId!: string
    filterMethod = (value: string, data: any) => {
        if (this.type == 'select') {
            return data.select && data.areaId == this.areaId ? true : false
        }
        if (data.select) {
            return false
        }
        if (this.isSelectAll(data)) {
            return false
        }
        if (!value) {
            return true
        }
        return data.label.indexOf(value) !== -1
    }
    @Watch('filterText')
    filterTextChange(val: number) {
        this.$refs.tree.filter(val)
    }
    @Watch('areaId')
    areaIdChange() {
        this.filter()
    }
    onSelect(node: any, data: any) {
        this.selectChildren(node, true)
        // this.selectPartent(node, true)
        this.filter()
        this.$emit('select')
    }
    onCancel(node: any, data: any) {
        this.selectChildren(node, false)
        // this.selectPartent(node, false)
        this.filter()
        this.$emit('cancel')
    }
    filter() {
        this.$refs.tree.filter()
    }
    selectChildren(node: any, select: boolean) {
        const { data, childNodes } = node
        if (!childNodes.length) {
            if (!data.areaId || data.areaId == this.areaId) {
                data.select = select
            }
            data.areaId = data.areaId ? (!select ? 0 : data.areaId) : this.areaId
            return
        }
        childNodes.forEach((item: any) => {
            this.selectChildren(item, select)
        })
    }

    isSelectAll(data: any) {
        if (!data.children) {
            return data.select
        }
        return data.children.every((item: any) => this.isSelectAll(item))
    }
    // selectPartent(node: any, select: boolean) {
    //     const parent = node.parent
    //     if (!parent) {
    //         return
    //     }
    //     const children: any = parent.data.children || parent.data
    //     let isAll = select
    //         ? children.every((item: any) => item.select)
    //         : children.some((item: any) => !item.select)
    //     if (isAll) {
    //         if (!parent.data.areaId) {
    //             parent.data.select = select
    //             parent.data.areaId = this.areaId
    //         } else {
    //             parent.data.select = select
    //             !select && (parent.data.areaId = 0)
    //         }
    //         this.selectPartent(parent, select)
    //     }
    // }

    mounted() {
        this.filter()
    }
}
</script>

<style scoped lang="scss">
.area-panel {
    border: $--border-base;
    border-radius: 4px;
    overflow: hidden;
    .area-tree {
        padding: 16px;
    }
}
</style>
