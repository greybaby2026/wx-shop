<template>
    <div class="add-price">
        <el-form-item label="商品规格" required>
            <el-radio-group v-model="value.spec_type">
                <el-radio :label="1">单规格</el-radio>
                <el-radio :label="2">多规格</el-radio>
            </el-radio-group>
        </el-form-item>
        <specification v-model="value" v-show="value.spec_type == 2" />
        <div v-if="value.spec_type == 1">
            <el-form-item
                label="商品价格"
                required
                prop="spec_value_list[0].sell_price"
                :rules="[
                    {
                        required: true,
                        message: '请输入商品金额',
                        trigger: ['blur', 'change']
                    }
                ]"
            >
                <el-input class="ls-input" type="number" v-model="value.specs_single.sell_price">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>
            <el-form-item label="划线价" prop="spec_value_list[0].lineation_price">
                <el-input class="ls-input" type="number" v-model="value.specs_single.lineation_price">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>
            <el-form-item label="成本价">
                <el-input class="ls-input" type="number" v-model="value.specs_single.cost_price">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>
            <el-form-item
                label="库存"
                required
                prop="spec_value_list[0].stock"
                :rules="[
                    {
                        required: true,
                        message: '请输入库存',
                        trigger: ['blur', 'change']
                    }
                ]"
            >
                <el-input class="ls-input" type="number" v-model="value.specs_single.stock"></el-input>
            </el-form-item>
            <el-form-item label="体积">
                <el-input class="ls-input" type="number" v-model="value.specs_single.volume"></el-input>
            </el-form-item>
            <el-form-item label="重量">
                <el-input class="ls-input" type="number" v-model="value.specs_single.weight"></el-input>
            </el-form-item>
            <el-form-item label="条码">
                <el-input
                    class="ls-input"
                    type="text"
                    @input="handleBarCodeInput"
                    v-model="value.specs_single.bar_code"
                ></el-input>
            </el-form-item>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import Specification from './specification.vue'
@Component({
    components: {
        MaterialSelect,
        Specification
    }
})
export default class AddPrice extends Vue {
    @Prop() value: any
    @Prop() specData!: any
    mounted() {}
    handleBarCodeInput(value: string) {
        // 允许数字、字母和更多常见符号（横线、下划线、点、空格、斜杠、冒号、括号等）
        this.value.specs_single.bar_code = value.replace(/[^a-zA-Z0-9_ .\-\/\:;()\[\]{}@#$%&+\=|~`!?',"<>^]/g, '')
    }
}
</script>

<style scoped lang="scss">
.add-price {
    .ls-input {
        width: 240px;
    }
}
</style>
