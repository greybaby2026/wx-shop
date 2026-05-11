<template>
    <div class="delivery-area">
        <ls-dialog title="区域选择" width="900px" top="20vh" ref="dialog" @confirm="onConfirm">
            <div class="area-content flex col-stretch">
                <div class="area-content__left flex-1">
                    <div class="flex row-between area-title">
                        <div class="normal nr">地区选择</div>
                        <el-input style="width: 200px" size="small" placeholder="输入地区关键字" v-model="filterText">
                        </el-input>
                    </div>
                    <area-panel
                        ref="panelAll"
                        :option="areaLists"
                        :filter-text="filterText"
                        :area-id="areaId"
                        @select="onSelect"
                    />
                </div>
                <div class="area-content__center flex row-center">
                    <i class="el-icon-arrow-right font-size-30"></i>
                </div>
                <div class="area-content__right flex-1">
                    <div class="flex row-between area-title">
                        <div class="normal nr">已选择</div>
                    </div>
                    <div class="select-area-list">
                        <area-panel
                            ref="panelSelect"
                            :option="areaLists"
                            :area-id="areaId"
                            type="select"
                            @cancel="onCancel"
                        />
                    </div>
                </div>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import area from '@/utils/area'
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import AreaPanel from './area-panel.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { throttle } from '@/utils/util'
@Component({
    components: {
        AreaPanel,
        LsDialog
    }
})
export default class DeliveryArea extends Vue {
    $refs!: { dialog: any; panelSelect: any; panelAll: any }
    @Prop() areaId!: string
    @Prop() defaultRegion!: any[]
    isFirst = true
    filterText = ''
    areaLists: any[] = JSON.parse(JSON.stringify(area))

    @Watch('defaultRegion')
    defaultRegionChange(val: any[]) {
        if (!this.isFirst) {
            return
        }
        this.isFirst = false
        val.forEach(item => {
            this.setSelectArea(item, this.areaLists)
        })
    }
    show() {
        this.$refs.dialog?.onTrigger()
    }
    onSelect() {
        this.$refs.panelSelect.filter()
    }
    onCancel() {
        this.$refs.panelAll.filter()
    }
    getSelectArea(area: any[]): any[] {
        const newLists: any[] = []
        area.forEach(pitem => {
            const selectTwoArr: any[] = []
            pitem.children &&
                pitem.children.forEach((citem: any) => {
                    const selectThreeArr = citem.children.filter(
                        (ditem: any) => ditem.select && ditem.areaId == this.areaId
                    )
                    if (citem.children.length == selectThreeArr.length) {
                        selectTwoArr.push(citem)
                    } else {
                        newLists.push(...selectThreeArr)
                    }
                })
            if (selectTwoArr.length == pitem.children.length) {
                newLists.push(pitem)
            } else {
                newLists.push(...selectTwoArr)
            }
        })
        return newLists
    }
    clearSelectArea(area?: any[]) {
        area = area || this.areaLists
        for (const i in area) {
            if (area[i].select && area[i].areaId == this.areaId) {
                area[i].select = false
                area[i].areaId = 0
            }
            if (area[i].children) {
                this.clearSelectArea(area[i].children)
            }
        }
    }

    setSelectArea(item: any, area: any[]) {
        area.forEach(pitem => {
            pitem.children &&
                pitem.children.forEach((citem: any) => {
                    citem.children &&
                        citem.children.forEach((ditem: any) => {
                            if (
                                item.region_id.includes(pitem.value) ||
                                item.region_id.includes(citem.value) ||
                                item.region_id.includes(ditem.value)
                            ) {
                                ditem.select = true
                                ditem.areaId = item.area_id
                            }
                        })
                })
        })
    }

    onConfirm = throttle(() => {
        const data = this.getSelectArea(this.areaLists)
        this.$emit('change', data)
    })
    mounted() {}
}
</script>

<style scoped lang="scss">
.delivery-area {
    ::v-deep.el-dialog__body {
        padding: 0;
    }
}
.area-content {
    padding: 16px 30px;
    border-top: 1px solid $--border-color-base;
    border-bottom: 1px solid $--border-color-base;
    .area-title {
        height: 36px;
        margin-bottom: 16px;
    }
    &__center {
        width: 100px;
    }
}
</style>
