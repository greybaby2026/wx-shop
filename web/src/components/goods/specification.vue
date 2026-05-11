<template>
    <div class="specification">
        <el-form-item label>
            <div>
                <el-button type="primary" @click="addSpecItem" :disabled="sepcItem.length >= 3">添加规格项</el-button>
                <span class="xs muted m-l-20">最多支持3个规格项</span>
            </div>
            <div class="spec-item flex p-16 m-t-16 col-top ls-del-wrap" v-for="(item, index) in sepcItem" :key="index">
                <div class="spec-item__label m-r-10 flex-none">规格名</div>
                <div class="spec-item__content">
                    <div>
                        <el-input v-model="item.name" style="width: 240px" maxlength="20" show-word-limit></el-input>
                        <el-checkbox class="m-l-16" v-model="item.has_image" @change="addImage(index, $event)"
                            >添加规格图片</el-checkbox
                        >
                    </div>
                    <div class="flex flex-wrap col-top">
                        <div class="m-r-10 m-t-10" v-for="(sitem, sindex) in item.spec_list" :key="sindex">
                            <div class="ls-del-wrap">
                                <el-input
                                    v-model="sitem.value"
                                    maxlength="50"
                                    show-word-limit
                                    @blur="checkValue(index, sindex)"
                                ></el-input>
                                <i class="el-icon-close ls-icon-del" @click="removeSpecValue(index, sindex)"></i>
                            </div>
                            <div v-if="item.has_image">
                                <material-select class="m-t-10" v-model="sitem.image" size="64"></material-select>
                            </div>
                        </div>
                        <div class="m-t-10">
                            <el-button @click="addSpecValue(index)">+ 添加规格值</el-button>
                        </div>
                    </div>
                </div>
                <i class="el-icon-close ls-icon-del" @click="removeSpecItem(index)"></i>
            </div>
        </el-form-item>
        <el-form-item label="规格明细" v-if="tableData.length">
            <div class="ls-batch-setting m-b-16">
                <popover-input
                    class="m-r-10"
                    :disabled="disabledBatchBtn"
                    @confirm="batchSetting($event, 'sell_price')"
                >
                    <el-button size="small" :disabled="disabledBatchBtn">设置价格</el-button>
                </popover-input>
                <popover-input
                    class="m-r-10"
                    :disabled="disabledBatchBtn"
                    @confirm="batchSetting($event, 'lineation_price')"
                >
                    <el-button size="small" :disabled="disabledBatchBtn">设置划线价</el-button>
                </popover-input>
                <popover-input
                    class="m-r-10"
                    :disabled="disabledBatchBtn"
                    @confirm="batchSetting($event, 'cost_price')"
                >
                    <el-button size="small" :disabled="disabledBatchBtn">设置成本价</el-button>
                </popover-input>
                <popover-input class="m-r-10" :disabled="disabledBatchBtn" @confirm="batchSetting($event, 'stock')">
                    <el-button size="small" :disabled="disabledBatchBtn">设置库存</el-button>
                </popover-input>
                <popover-input class="m-r-10" :disabled="disabledBatchBtn" @confirm="batchSetting($event, 'volume')">
                    <el-button size="small" :disabled="disabledBatchBtn">设置体积</el-button>
                </popover-input>
                <popover-input class="m-r-10" :disabled="disabledBatchBtn" @confirm="batchSetting($event, 'weight')">
                    <el-button size="small" :disabled="disabledBatchBtn">设置重量</el-button>
                </popover-input>
                <popover-input :disabled="disabledBatchBtn" @confirm="batchSetting($event, 'bar_code')">
                    <el-button size="small" :disabled="disabledBatchBtn">设置条码</el-button>
                </popover-input>
            </div>
            <u-table
                :data="tableData"
                use-virtual
                size="mini"
                max-height="600"
                :row-height="75"
                tooltip-effect="dark"
                :border="false"
                big-data-checkbox
                @selection-change="selectDataChange"
            >
                <u-table-column type="selection" width="55"></u-table-column>
                <u-table-column
                    v-for="(item, index) in sepcItem"
                    :key="index"
                    :label="item.name"
                    min-width="200"
                    :show-overflow-tooltip="true"
                >
                    <template slot-scope="scope">
                        {{ scope.row.value[index] }}
                    </template>
                </u-table-column>
                <u-table-column label="规格图片" min-width="80">
                    <template slot-scope="scope">
                        <div class="spec-image ls-del-wrap m-t-8" v-if="scope.row.image">
                            <el-image
                                style="width: 100%; height: 100%"
                                :src="scope.row.image"
                                @click="addSpecImage(scope.$index)"
                            ></el-image>
                            <i class="el-icon-close ls-icon-del" @click="removeSpecImage(scope.$index)"></i>
                        </div>
                        <div class="add-spec-image flex row-center" @click="addSpecImage(scope.$index)" v-else>
                            <i class="el-icon-plus"></i>
                        </div>
                    </template>
                </u-table-column>
                <u-table-column label min-width="130">
                    <template slot="header"> <span class="require-text">*</span> 价格 </template>
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.sell_price"></el-input>
                    </template>
                </u-table-column>
                <u-table-column min-width="130">
                    <template slot="header"> 划线价 </template>
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.lineation_price"></el-input>
                    </template>
                </u-table-column>
                <u-table-column label="成本价" min-width="130">
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.cost_price"></el-input>
                    </template>
                </u-table-column>
                <u-table-column min-width="130">
                    <template slot="header"> <span class="require-text">*</span> 库存 </template>
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.stock"></el-input>
                    </template>
                </u-table-column>
                <u-table-column min-width="130">
                    <template slot="header"> 体积 </template>
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.volume"></el-input>
                    </template>
                </u-table-column>
                <u-table-column label="重量" min-width="130">
                    <template slot="header"> 重量 </template>
                    <template slot-scope="scope">
                        <el-input class="spec-input" type="number" v-model="scope.row.weight"></el-input>
                    </template>
                </u-table-column>
                <u-table-column label="条码" min-width="130">
                    <template slot-scope="scope">
                        <el-input
                            class="spec-input"
                            type="text"
                            @input="handleBarCodeInput(scope.row.bar_code, scope.$index)"
                            v-model="scope.row.bar_code"
                        ></el-input>
                    </template>
                </u-table-column>
            </u-table>
        </el-form-item>
        <material-select ref="materialSelect" :hidden-trigger="true" @change="changeSpecImage" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import PopoverInput from '@/components/popover-input.vue'
import { flatten } from '@/utils/util'
@Component({
    components: {
        MaterialSelect,
        PopoverInput
    }
})
export default class Specification extends Vue {
    $refs!: { materialSelect: any }
    @Prop() value: any
    tableDataIndex = 0
    fileList = []
    tableData: any[] = []
    selectData: any[] = []
    get sepcItem() {
        return this.value.spec_value
    }

    get disabledBatchBtn() {
        return !this.selectData.length
    }

    @Watch('value.spec_value_list')
    defaultDataChange(val: any[]) {
        this.tableData = val
    }

    @Watch('sepcItem', { deep: true, immediate: true })
    sepcItemChange(val: any) {
        this.setTableData()
    }
    @Watch('tableData', { deep: true })
    tableDataChange(val: any) {
        this.value.spec_value_list = val
    }
    // 添加规格项
    addSpecItem() {
        if (this.sepcItem.length >= 3) {
            return
        }
        this.sepcItem.push({
            has_image: false,
            id: '',
            name: '',
            spec_list: [
                {
                    id: '',
                    value: '',
                    image: ''
                }
            ]
        })
    }
    // 移除规格项
    removeSpecItem(index: number) {
        if (this.sepcItem.length <= 1) {
            return this.$message.error('至少有一个规格项')
        }
        this.sepcItem.splice(index, 1)
    }
    // 添加规格值
    addSpecValue(index: number) {
        this.sepcItem[index].spec_list.push({
            id: '',
            value: '',
            image: ''
        })
    }
    // 移除规格值
    removeSpecValue(index: number, sindex: number) {
        this.sepcItem[index].spec_list.splice(sindex, 1)
    }

    addImage(i: number, v: boolean) {
        this.sepcItem.forEach((item: any, index: number) => {
            item.has_image = false
            if (i == index) {
                item.has_image = v
            }
            item.spec_list.forEach((sitem: any) => {
                sitem.image = ''
            })
        })
        this.tableData.forEach(item => {
            item.image = ''
        })
    }

    checkValue(index: number, sindex: number) {
        const value = this.sepcItem[index].spec_list[sindex].value
        this.sepcItem[index].spec_list.forEach((item: any, idx: number) => {
            if (item.value && sindex != idx && item.value == value) {
                this.sepcItem[index].spec_list[sindex].value = ''
                this.$message({
                    message: '已存在相同规格值',
                    type: 'warning'
                })
            }
        })
    }
    selectDataChange(value: any[]) {
        this.selectData = value.map(item => item.ids)
    }

    batchSetting(value: string, fields: string) {
        const selectedIds = new Set(this.selectData)
        // 强制触发一次完整的数组更新，避免逐个修改属性导致的视图更新堆积卡顿
        const newTableData = this.tableData.map(item => {
            if (selectedIds.has(item.ids)) {
                return {
                    ...item,
                    [fields]: value
                }
            }
            return item
        })
        this.tableData = newTableData
    }

    //设置字段名称
    setFields(prev: any, next: any) {
        let valueArr: any[] = [prev, next]
        valueArr = valueArr.filter(item => item.value !== undefined)
        const ids = flatten(valueArr.map(item => item.ids)).join()
        const value = flatten(valueArr.map(item => item.value))
        return {
            id: prev.id ? prev.id : '',
            ids: ids,
            value,
            spec_value_str: value.join(),
            image: prev.image !== undefined ? prev.image : (next.image !== undefined ? next.image : ''),
            sell_price: prev.sell_price !== undefined ? prev.sell_price : '',
            lineation_price: prev.lineation_price !== undefined ? prev.lineation_price : '',
            cost_price: prev.cost_price !== undefined ? prev.cost_price : '',
            stock: prev.stock !== undefined ? prev.stock : '',
            volume: prev.volume !== undefined ? prev.volume : '',
            weight: prev.weight !== undefined ? prev.weight : '',
            bar_code: prev.bar_code !== undefined ? prev.bar_code : ''
        }
    }
    // 通过规格项和规格值得到一个表格data
    getTableData(arr: any[]) {
        arr = JSON.parse(JSON.stringify(arr))
        return arr.reduce(
            (prev, next) => {
                const newArr: any[] = []
                for (let i = 0; i < prev.length; i++) {
                    if (!next.length) {
                        newArr.push(this.setFields(prev[i], {}))
                    }
                    for (let j = 0; j < next.length; j++) {
                        next[j].ids = j
                        newArr.push(this.setFields(prev[i], next[j]))
                    }
                }
                return newArr
            },
            [{}]
        )
    }

    setTableData() {
        const { tableData, sepcItem } = this

        const specList = sepcItem.map((item: any) => item.spec_list)
        const newData = this.getTableData(specList)
        const rawData = JSON.parse(JSON.stringify(tableData))
        
        const newDataMap = new Map()
        newData.forEach((newItem: any) => {
            newDataMap.set(newItem.spec_value_str, newItem.ids)
        })
        
        rawData.forEach((item: any) => {
            if(item.ids==undefined || item.ids==null || item.ids=='') {
                if (newDataMap.has(item.spec_value_str)) {
                    item.ids = newDataMap.get(item.spec_value_str)
                }
            }
        })
        const rawObject: any = {}
        rawData.forEach((item: any, index: number) => {
            if (item.spec_value_str !== undefined) {
                if(item.ids==undefined || item.ids==null || item.ids=='') {
                    rawObject[item.spec_value_str+'_'+0] = item
                }else {
                    rawObject[item.spec_value_str+'_'+item.ids] = item
                }
            }
        })
        this.tableData = newData.map((item: any, index: number) => {
            let ids=0;
            if(item.ids==undefined || item.ids==null || item.ids=='') {
                ids=0
            }else {
                ids=item.ids
            }
            const data = rawObject[item.spec_value_str+'_'+ids]
            if (data) {
                return {
                    ...rawObject[item.spec_value_str+'_'+ids],
                    value: item.value,
                    ids: item.ids,
                    image: item.image || rawObject[item.spec_value_str+'_'+ids].image
                }
            }
            const findTableData = tableData.find((tableItem: any) => tableItem.ids === item.ids)
            const removeIsNullData = Object.keys(item).reduce((obj: any, key: string) => {
                if (item[key] !== undefined && item[key] !== null && item[key] !== '') {
                    obj[key] = item[key]
                }
                return obj
            }, {})
            return {
                ...findTableData,
                ...removeIsNullData
            }
        })
    }

    addSpecImage(index: number) {
        this.tableDataIndex = index
        this.$refs.materialSelect.showDialog()
    }
    changeSpecImage(value: string) {
        this.tableData[this.tableDataIndex].image = value
    }
    removeSpecImage(index: number) {
        this.tableData[index].image = ''
    }
    handleBarCodeInput(value: string, index: number) {
        this.tableData[index].bar_code = value.replace(/[^a-zA-Z0-9_ .\-\/\:;()\[\]{}@#$%&+\=|~`!?',"<>^]/g, '')
    }
}
</script>

<style scoped lang="scss">
.specification {
    .spec-item {
        background-color: #f5f8ff;
        &.ls-del-wrap {
            & > .ls-icon-del {
                top: 10px;
                right: 10px;
            }
        }
    }
    ::v-deep .el-table {
        .spec-image {
            width: 44px;
            height: 44px;
        }
        .add-spec-image {
            width: 44px;
            height: 44px;
            box-sizing: border-box;
            border: 1px dashed $--border-color-base;
            cursor: pointer;
            border-radius: 4px;
        }
        .require-text {
            color: #f86056;
        }
        .spec-input {
            width: 100px;
        }
    }
}
</style>
