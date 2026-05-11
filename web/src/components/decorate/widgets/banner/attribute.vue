<template>
    <div>
        <attribute-tabs title="轮播图">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="图片设置" desc="最多可添加10张，建议图片尺寸：750px*340px">
                        <el-form-item label-width="0">
                            <banner-list v-model="content.data" :limit="10" />
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="指示器设置">
                        <el-form-item label="选中颜色">
                            <color-select v-model="styles.indicator_color" reset-color="#FF2C3C" />
                        </el-form-item>
                        <div class="lighter xs m-b-20">指示器样式</div>
                        <el-form-item label-width="0">
                            <style-chose v-model="styles.indicator_style" :data="indicatorData" />
                        </el-form-item>
                        <div class="lighter xs m-b-20">指示器位置</div>
                        <el-form-item label-width="0">
                            <style-chose v-model="styles.indicator_align" :data="alignData" />
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
                        <el-form-item label="圆角">
                            <slider v-model="styles.border_radius" />
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
import BannerList from '@/components/decorate/banner-list.vue'

@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        BannerList
    }
})
export default class Attribute extends Vue {
    /** S data **/

    indicatorData = [
        {
            name: '圆角',
            value: 1
        },
        {
            name: '圆形',
            value: 2
        },
        {
            name: '数字',
            value: 3
        }
    ]
    alignData = [
        {
            name: '居左',
            value: 'left'
        },
        {
            name: '居中',
            value: 'center'
        },
        {
            name: '居右',
            value: 'right'
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

    /** S methods **/

    /** E methods **/
}
</script>

<style lang="scss" scoped></style>
