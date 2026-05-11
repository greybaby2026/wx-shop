<template>
    <div>
        <attribute-tabs title="我的订单">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="">
                        <el-form-item label="标题名称">
                            <el-input
                                v-model="content.text"
                                maxlength="8"
                                show-word-limit
                                placeholder="请输入标题名称"
                            ></el-input>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="导航内容">
                        <nav-list v-model="content.data" />
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                        <el-form-item label="组件背景">
                            <color-select v-model="styles.bg_color" reset-color="#FFFFFF" />
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
        NavList
    }
})
export default class Attribute extends Vue {
    /** S data **/

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
