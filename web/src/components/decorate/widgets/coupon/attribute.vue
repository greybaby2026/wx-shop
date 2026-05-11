<template>
    <div>
        <attribute-tabs title="优惠券">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="风格选择">
                        <style-select slot="right" v-model="content.style" :data="styleData">
                            <el-button slot="trigger" size="mini" type="text">切换风格</el-button>
                        </style-select>
                        <el-form-item label-width="0">
                            <div class="style-chose flex col-center">
                                <template v-for="(item, index) in styleData">
                                    <img v-if="item.value == content.style" :key="index" :src="item.img" alt="" />
                                </template>
                            </div>
                        </el-form-item>
                        <template v-if="content.style == 3">
                            <el-form-item label="标题文字">
                                <el-input
                                    v-model="content.title"
                                    maxlength="12"
                                    show-word-limit
                                    placeholder="请输入标题"
                                ></el-input>
                            </el-form-item>
                            <el-form-item label="标题颜色">
                                <color-select v-model="styles.title_color" reset-color="#FFFFFF" />
                            </el-form-item>
                        </template>
                    </attribute-item>
                    <attribute-item title="添加优惠券" desc="最多可添加10张">
                        <coupon-select v-model="content.data" :limit="10"></coupon-select>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="优惠券设置">
                        <el-form-item label="背景颜色">
                            <color-select v-model="styles.bg_color" reset-color="#FCE7E7" />
                        </el-form-item>
                        <template v-if="content.style == 3">
                            <el-form-item label="金额颜色">
                                <color-select v-model="styles.money_color" reset-color="#FF2C3C" />
                            </el-form-item>
                            <el-form-item label="条件颜色">
                                <color-select v-model="styles.condition_color" reset-color="#333333" />
                            </el-form-item>
                            <el-form-item label="场景颜色">
                                <color-select v-model="styles.scene_color" reset-color="#999999" />
                            </el-form-item>
                        </template>
                        <el-form-item label="文字颜色" v-if="content.style != 3">
                            <color-select v-model="styles.text_color" reset-color="#FF2C3C" />
                        </el-form-item>
                        <template v-if="content.style != 2">
                            <el-form-item label="按钮背景">
                                <color-select v-model="styles.btn_bg_color" reset-color="#FF2C3C" />
                            </el-form-item>
                            <el-form-item label="按钮文字">
                                <color-select v-model="styles.btn_text_color" reset-color="#FFFFFF" />
                            </el-form-item>
                        </template>
                    </attribute-item>
                    <attribute-item title="背景设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <template v-if="content.style == 3">
                            <el-form-item label="背景图片">
                                <el-radio-group v-model="content.bg_type">
                                    <el-radio :label="1">系统默认</el-radio>
                                    <el-radio :label="2">自定义</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="" v-if="content.bg_type == 2">
                                <material-select
                                    class="m-r-10"
                                    ref="materialSelect"
                                    v-model="styles.bg_image"
                                    :size="60"
                                    upload-bg="#fff"
                                >
                                    <i class="el-icon-plus lg"></i>
                                </material-select>
                            </el-form-item>
                        </template>
                    </attribute-item>
                    <attribute-item title="边距设置">
                        <el-form-item label="上边距">
                            <slider v-model="styles.padding_top" />
                        </el-form-item>
                        <el-form-item label="下边距">
                            <slider v-model="styles.padding_bottom" />
                        </el-form-item>
                        <el-form-item label="左右边距">
                            <slider v-model="styles.padding_horizontal" />
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import StyleSelect from '@/components/decorate/style-select.vue'
import CouponSelect from '@/components/coupon-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        StyleSelect,
        CouponSelect,
        MaterialSelect
    }
})
export default class Attribute extends Vue {
    /** S data **/
    styleData = [
        {
            value: 1,
            img: require('@/assets/images/coupon1.png'),
            text: '风格1'
        },
        {
            value: 2,
            img: require('@/assets/images/coupon2.png'),
            text: '风格2'
        },
        {
            value: 3,
            img: require('@/assets/images/coupon3.png'),
            text: '风格3'
        }
    ]
    /** E data **/

    /** S computed **/

    get content() {
        return this.$store.getters.content
    }

    set content(val) {
        const data = {
            key: 'content',
            value: val
        }
        this.$store.commit('setAttribute', data)
    }
    get styles() {
        return this.$store.getters.styles
    }

    /** E computed **/
    @Watch('content.style')
    styleChange(val: number) {
        switch (val) {
            case 1:
            case 2:
                this.styles.root_bg_color = '#FFFFFF'
                this.styles.bg_color = '#FCE7E7'
                break
            case 3:
                this.styles.root_bg_color = ''
                this.styles.bg_color = '#FFFFFF'
        }
    }
    /** S methods **/

    /** E methods **/
}
</script>
<style lang="scss" scoped>
.style-chose {
    background: #f9f9f9;
    padding: 15px;
    height: 200px;
    text-align: center;
    box-sizing: border-box;
    img {
        width: 100%;
    }
}
::v-deep .el-alert__content {
    line-height: 1;
}
</style>
