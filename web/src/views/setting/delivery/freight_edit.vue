<template>
    <div class="freight-edit flex-col">
        <div class="ls-card freight-edit__header">
            <el-page-header @back="$router.go(-1)" :content="id ? '编辑运费模板' : '新增运费模板'"></el-page-header>
        </div>
        <div class="ls-card freight-edit__form m-t-10">
            <el-form class="ls-form" ref="form" :model="form" label-width="120px" size="small">
                <el-form-item
                    label="模板名称"
                    prop="name"
                    required
                    :rules="[
                        {
                            required: true,
                            message: '请输入模板名称',
                            trigger: ['blur', 'change']
                        }
                    ]"
                >
                    <el-input v-model="form.name" placeholder="请输入模板名称"></el-input>
                </el-form-item>
                <el-form-item label="计费方式" required>
                    <el-radio-group v-model="form.charge_way">
                        <el-radio :label="1">件数计费</el-radio>
                        <el-radio :label="2">重量计费</el-radio>
                        <el-radio :label="3">体积计费</el-radio>
                    </el-radio-group>
                    <div class="muted">保存后计费方式不能更改</div>
                </el-form-item>
                <el-form-item label="配送区域" required>
                    <el-table :data="form.region" style="width: 100%" size="mini">
                        <el-table-column label="配送区域" prop="region_name" min-width="300">
                            <!-- <template slot-scope="{row}">
                                <div class="flex flex-wrap">
                                    <div
                                        v-for="(item, index) in row.region_id"
                                        :key="item.value"
                                    >{{index > 0 ? '、' : ''}}{{item.label}}</div>
                                </div>
                            </template> -->
                        </el-table-column>
                        <el-table-column min-width="150" :label="`首${getTableTitle}`">
                            <template slot-scope="scope">
                                <el-input
                                    class="p-t-8 p-b-8"
                                    style="width: 90%"
                                    type="number"
                                    v-model="scope.row.first_unit"
                                ></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column min-width="150" label="运费（元）">
                            <template slot-scope="scope">
                                <el-input
                                    class="p-t-8 p-b-8"
                                    style="width: 90%"
                                    type="number"
                                    v-model="scope.row.first_money"
                                ></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column min-width="150" :label="`续${getTableTitle}`">
                            <template slot-scope="scope">
                                <el-input
                                    class="p-t-8 p-b-8"
                                    style="width: 90%"
                                    type="number"
                                    v-model="scope.row.continue_unit"
                                ></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column min-width="150" label="续费（元）">
                            <template slot-scope="scope">
                                <el-input
                                    class="p-t-8 p-b-8"
                                    style="width: 90%"
                                    type="number"
                                    v-model="scope.row.continue_money"
                                ></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column fixed="right" label="操作" width="150">
                            <template slot-scope="scope" v-if="scope.$index > 0">
                                <el-button type="text" size="small" @click="handleEdit(scope.row.area_id)">
                                    编辑
                                </el-button>
                                <el-button type="text" size="small" @click="handleDelete(scope.row.area_id)">
                                    删除
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
                <el-form-item>
                    <el-button size="small" type="primary" @click="handleShowDelivery">指定配送区域和运费</el-button>
                    <span class="muted m-l-10">
                        根据件数来计算运费，当物品不足《首件数量》时，按照《首件费用》计算，超过部分按照《续件件数》和《续件费用》乘积来计算</span
                    >
                </el-form-item>
                <el-form-item label="备注">
                    <el-input
                        style="width: 600px"
                        v-model="form.remark"
                        rows="8"
                        type="textarea"
                        placeholder="请输入备注内容"
                    ></el-input>
                </el-form-item>
            </el-form>
            <delivery-area ref="deliveryArea" :area-id="areaId" :default-region="form.region" @change="areaChange" />
        </div>
        <div class="freight-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import area from '@/utils/area'
import DeliveryArea from '@/components/setting/delivery-area/index.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { getNonDuplicateID } from '@/utils/util'
import { apiFreightAdd, apiFreightDetail, apiFreightEdit } from '@/api/setting/delivery'
@Component({
    components: {
        DeliveryArea,
        LsDialog
    }
})
export default class FreightEdit extends Vue {
    $refs!: { deliveryArea: any; form: any }
    id!: any
    areaLists = area
    form = {
        name: '',
        charge_way: 1,
        remark: '',
        region: [
            {
                area_id: '',
                region_id: '100000',
                region_name: '全国统一运费',
                first_unit: '',
                first_money: '',
                continue_unit: '',
                continue_money: ''
            }
        ]
    }
    areaId = ''

    get getTableTitle() {
        const { charge_way } = this.form
        switch (charge_way) {
            case 1:
                return '件（件）'
            case 2:
                return '重（Kg）'
            case 3:
                return '体积（m³）'
        }
    }

    get checkRegion() {
        const check = this.form.region.every(item => {
            return (
                item.region_id &&
                item.first_unit !== '' &&
                item.first_unit !== '' &&
                item.first_unit !== '' &&
                item.first_unit !== ''
            )
        })
        if (!check) {
            this.$message.error('填写完整运费模板')
        }
        return check
    }

    handleShowDelivery() {
        this.areaId = getNonDuplicateID()
        this.$refs.deliveryArea.show()
    }
    handleEdit(index: string) {
        this.areaId = index
        this.$refs.deliveryArea.show()
    }
    handleDelete(areaId: string) {
        this.areaId = areaId
        const index = this.form.region.findIndex(item => item.area_id == areaId)
        if (index != -1) {
            this.form.region.splice(index, 1)
        }
        this.$nextTick(() => {
            this.$refs.deliveryArea.clearSelectArea()
        })
    }
    areaChange(value: any[]) {
        const index = this.form.region.findIndex(item => item.area_id == this.areaId)
        const region_id = value.map(item => item.value).join()
        const region_name = value.map(item => item.label).join('、')
        if (index != -1) {
            this.form.region[index].region_id = region_id
            this.form.region[index].region_name = region_name
            return
        }
        this.form.region.push({
            area_id: this.areaId,
            region_id,
            region_name,
            first_unit: '',
            first_money: '',
            continue_unit: '',
            continue_money: ''
        })
    }
    handleSave() {
        this.$refs.form.validate((valid: boolean) => {
            if (valid && this.checkRegion) {
                const api = this.id ? apiFreightEdit(this.form) : apiFreightAdd(this.form)
                api.then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getFreightDetail() {
        apiFreightDetail({ id: this.id }).then((res: any) => {
            res.region.forEach((item: any) => {
                item.area_id = getNonDuplicateID()
            })
            this.form = res
        })
    }

    created() {
        this.id = this.$route.query.id
        this.id && this.getFreightDetail()
    }
}
</script>
<style lang="scss" scoped>
.freight-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
    &__header {
        flex: none;
    }
}
</style>
