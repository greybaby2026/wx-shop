<template>
    <div>
        <el-form-item label="添加商品">
            <el-radio-group v-model="content.goods_type">
                <el-radio :label="1">手动选择</el-radio>
                <el-radio :label="2">选择分类</el-radio>
            </el-radio-group>
        </el-form-item>
        <div class="p-10 bg-white">
            <div v-show="content.goods_type == 1">
                <goods-select v-model="content.data" :show-virtual-goods="true"> </goods-select>
            </div>
            <div v-show="content.goods_type == 2">
                <category-select v-model="content.category"> </category-select>
                <el-form-item label="显示件数" style="margin-bottom: 0">
                    <slider :min="1" :max="50" v-model="content.category.num" />
                    <div class="muted">最多显示50个</div>
                </el-form-item>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import GoodsSelect from '@/components/goods-select/index.vue'
import CategorySelect from '@/components/category-select/index.vue'
import Slider from '@/components/decorate/slider.vue'
@Component({
    components: {
        GoodsSelect,
        CategorySelect,
        Slider
    }
})
export default class Select extends Vue {
    @Prop() content!: any
}
</script>
