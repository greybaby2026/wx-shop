<template>
    <div class="table m-t-20">
        <el-table v-if="list.length" ref="paneTable" size="mini" :data="list" max-height="500">
            <el-table-column label="商品信息" min-width="200">
                <template slot-scope="scope">
                    <div class="flex">
                        <el-image
                            class="flex-none"
                            style="width: 58px; height: 58px"
                            :src="scope.row.image"
                            fit="cover"
                        />
                        <div class="goods-info m-l-8">
                            <div class="line-2">{{ scope.row.name }}</div>
                            <el-tag v-if="scope.row.spec_type == 2" size="mini">多规格</el-tag>
                        </div>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="价格" min-width="100">
                <template slot-scope="scope"> ￥{{ scope.row.sell_price }}</template>
            </el-table-column>
            <el-table-column v-if="extend.name" :label="`${extend.name}设置`" min-width="200">
                <template slot-scope="scope">
                    <sepc-table
                        v-if="scope.row.spec_type == 2"
                        v-model="scope.row.item"
                        :extend="extend"
                        :disabled="disabled"
                    />
                    <template v-else>
                        <el-input
                            class="m-r-10 m-t-5"
                            v-for="(item, index) in extendPrice"
                            :key="index"
                            style="width: 150px"
                            type="number"
                            :placeholder="item.title"
                            v-model="scope.row.item[0][item.key]"
                            :disabled="disabled"
                        >
                        </el-input>
                    </template>
                </template>
            </el-table-column>
            <el-table-column label="虚拟销量" min-width="100">
                <template #default="scope">
                    <el-input class="m-r-10 m-t-5" v-model="scope.row.virtual_sale" :disabled="disabled" />
                </template>
            </el-table-column>
            <el-table-column label="虚拟浏览量" min-width="100">
                <template #default="scope">
                    <el-input class="m-r-10 m-t-5" v-model="scope.row.virtual_click" :disabled="disabled" />
                </template>
            </el-table-column>
            <el-table-column label="操作" width="100">
                <template slot-scope="scope">
                    <el-button
                        @click="handleDelete(scope.$index, scope.row)"
                        type="text"
                        size="small"
                        :disabled="disabled"
                        >移除</el-button
                    >
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import SepcTable from './sepc-table.vue'
@Component({
    components: {
        LsDialog,
        SepcTable
    }
})
export default class Table extends Vue {
    @Prop({ default: false }) disabled!: boolean //是否禁用
    @Prop({ default: () => [] }) value!: any[]

    @Prop({ default: false }) isSpec!: boolean
    @Prop({
        default: () => ({})
    })
    extend!: any
    get list() {
        return this.value
    }

    set list(val) {
        this.$emit('input', val)
    }

    get extendPrice() {
        return this.extend.price || []
    }
    handleDelete(index: number, test: any) {
        this.list.splice(index, 1)
    }
}
</script>

<style scoped lang="scss"></style>
