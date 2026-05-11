<template>
    <div>
        <attribute-item title="商品风格">
            <el-form-item label-width="0">
                <style-chose v-model="content.style" :data="styleData" />
            </el-form-item>
        </attribute-item>
        <attribute-item title="显示内容">
            <el-form-item label-width="0">
                <div class="flex">
                    <el-checkbox :true-label="1" :false-label="0" v-model="content.show_title">商品标题</el-checkbox>
                    <color-select class="m-l-10" v-model="styles.title_color" reset-color="#101010" />
                </div>
            </el-form-item>
            <el-form-item label-width="0">
                <div class="flex">
                    <el-checkbox :true-label="1" :false-label="0" v-model="content.show_price">商品价格</el-checkbox>
                    <color-select class="m-l-10" v-model="styles.price_color" reset-color="#FF2C3C" />
                </div>
            </el-form-item>
            <el-form-item label-width="0">
                <div class="flex">
                    <el-checkbox :true-label="1" :false-label="0" v-model="content.show_scribing_price"
                        >划线原价</el-checkbox
                    >
                    <color-select class="m-l-10" v-model="styles.scribing_price_color" reset-color="#999999" />
                </div>
            </el-form-item>
        </attribute-item>
        <attribute-item title="购买按钮" v-if="content.style == 1 || content.style == 4">
            <el-form-item label="是否显示">
                <el-radio-group v-model="content.show_btn">
                    <el-radio :label="1">显示</el-radio>
                    <el-radio :label="0">隐藏</el-radio>
                </el-radio-group>
            </el-form-item>
            <template v-if="content.show_btn == 1">
                <el-form-item label="按钮文字">
                    <el-input
                        v-model="content.btn_text"
                        maxlength="4"
                        show-word-limit
                        placeholder="请输入按钮文字"
                    ></el-input>
                </el-form-item>
                <el-form-item label="按钮背景">
                    <color-select v-model="styles.btn_bg_color" reset-color="#FF2C3C" />
                </el-form-item>
                <el-form-item label="文字颜色">
                    <color-select v-model="styles.btn_color" reset-color="#FFFFFF" />
                </el-form-item>
                <el-form-item label="按钮圆角">
                    <slider v-model="styles.btn_border_radius" />
                </el-form-item>
                <el-form-item label="边框颜色">
                    <color-select v-model="styles.btn_border_color" reset-color="#FF2C3C" />
                </el-form-item>
            </template>
        </attribute-item>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
@Component({
    components: {
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem
    }
})
export default class Select extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    styleData = [
        {
            name: '大图模式',
            value: 1
        },
        {
            name: '一行2个',
            value: 2
        },
        {
            name: '横向滑动',
            value: 3
        },
        {
            name: '列表模式',
            value: 4
        }
    ]
}
</script>
