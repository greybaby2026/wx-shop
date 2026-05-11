<template>
    <div>
        <attribute-tabs title="菜单导航">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="导航样式">
                        <el-form-item label-width="0">
                            <style-chose v-model="content.style" :data="styleData" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="展示样式">
                        <el-form-item label-width="0">
                            <el-radio-group v-model="styles.nav_style">
                                <el-radio :label="1">固定显示</el-radio>
                                <el-radio :label="2">分页滑动</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="每行数量">
                            <el-select v-model="styles.nav_line_num">
                                <el-option label="3个" :value="3"></el-option>
                                <el-option label="4个" :value="4"></el-option>
                                <el-option label="5个" :value="5"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="显示行数">
                            <el-select v-model="styles.nav_line">
                                <el-option label="1行" :value="1"></el-option>
                                <el-option label="2行" :value="2"></el-option>
                                <el-option label="3行" :value="3"></el-option>
                                <el-option label="4行" :value="4"></el-option>
                            </el-select>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="导航设置">
                        <nav-list v-model="content.data" />
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="指示器设置" v-if="styles.nav_style == 2">
                        <el-form-item label="选中颜色">
                            <color-select v-model="styles.indicator_color" reset-color="#FF2C3C" />
                        </el-form-item>
                        <div class="lighter xs m-b-20">指示器样式</div>
                        <el-form-item label-width="0">
                            <style-chose v-model="styles.indicator_style" :data="indicatorData" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                        <el-form-item label="组件背景">
                            <color-select v-model="styles.bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="文字颜色">
                            <color-select v-model="styles.color" reset-color="#333333" />
                        </el-form-item>
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
                    <attribute-item title="圆角设置">
                        <el-form-item label="上圆角">
                            <slider v-model="styles.border_radius_top" />
                        </el-form-item>
                        <el-form-item label="下圆角">
                            <slider v-model="styles.border_radius_bottom" />
                        </el-form-item>
                        <el-form-item label="图片圆角">
                            <slider v-model="styles.img_border_radius" />
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import NavList from '@/components/decorate/nav-list.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        NavList,
        LinkSelect
    }
})
export default class Attribute extends Vue {
    /** S data **/
    $refs!: { materialSelect: any }
    index = -1
    styleData = [
        {
            name: '图文',
            value: 1
        },
        {
            name: '文字',
            value: 2
        }
    ]
    indicatorData = [
        {
            name: '圆角',
            value: 1
        },
        {
            name: '圆形',
            value: 2
        }
    ]
    resetColor = ''
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

    /** S methods **/

    /** E methods **/
}
</script>

<style lang="scss" scoped></style>
