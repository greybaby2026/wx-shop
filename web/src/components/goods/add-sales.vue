<template>
    <div class="add-sales">
        <el-form-item label="库存预警">
            <el-input class="ls-input" type="number" v-model="value.stock_warning"></el-input>
            <div class="xs muted">设置最低库存预警值，不填或填0表示不做库存预警</div>
        </el-form-item>
        <el-form-item label="虚拟销量">
            <el-input class="ls-input" type="number" v-model="value.virtual_sales_num"></el-input>
        </el-form-item>
        <el-form-item label="虚拟浏览量">
            <el-input class="ls-input" type="number" v-model="value.virtual_click_num"></el-input>
        </el-form-item>
        <el-form-item label="服务保障">
            <el-select v-model="value.service_guarantee_ids" multiple placeholder="请选择">
                <el-option
                    v-for="item in goodsServiceGuaranteelists.lists"
                    :key="item.id"
                    :label="item.name"
                    :value="String(item.id)"
                >
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="购买限制">
            <div>
                <el-radio-group v-model="value.limit_type">
                    <el-radio :label="1">不限制</el-radio>
                    <el-radio :label="2">每人限购</el-radio>
                    <el-radio :label="3">每笔订单限购</el-radio>
                </el-radio-group>
            </div>
        </el-form-item>
        <el-form-item label=" " v-if="value.limit_type !== 1">
            <el-input class="ls-input" type="number" v-model="value.limit_value">
                <template slot="append">件</template>
            </el-input>
        </el-form-item>
        <el-form-item label="商品状态" required v-if="isGatherGoods">
            <el-radio-group v-model="value.status">
                <el-radio :label="1">立即上架</el-radio>
                <el-radio :label="0">放入仓库</el-radio>
            </el-radio-group>
        </el-form-item>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import { apigoodsServiceGuaranteelists } from '@/api/goods'
@Component
export default class AddSales extends Vue {
    @Prop() value: any
    @Prop() isGatherGoods: any
    goodsServiceGuaranteelists: any = []
    created() {
        apigoodsServiceGuaranteelists().then(res => {
            this.goodsServiceGuaranteelists = res
        })
    }
}
</script>

<style scoped lang="scss"></style>
