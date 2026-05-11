<template>
    <div>
        <attribute-tabs title="搜索框">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item>
                        <el-form-item label="提示文字">
                            <el-input
                                v-model="content.text"
                                maxlength="12"
                                show-word-limit
                                placeholder="请输入关键字搜索"
                            ></el-input>
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="搜索框设置">
                        <el-form-item label="框体圆角">
                            <slider v-model="styles.border_radius" />
                        </el-form-item>
                        <el-form-item label="文本位置">
                            <style-chose v-model="styles.text_align" :data="alignData" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                        <el-form-item label="组件背景">
                            <color-select v-model="styles.bg_color" reset-color="#F5F5F5" />
                        </el-form-item>
                        <el-form-item label="图标颜色">
                            <color-select v-model="styles.icon_color" reset-color="#999999" />
                        </el-form-item>
                        <el-form-item label="文字颜色">
                            <color-select v-model="styles.color" reset-color="#999999" />
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
import AttributeItem from '@/components/decorate/attribute-item.vue'
import Slider from '@/components/decorate/slider.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem
    }
})
export default class SearchAttribute extends Vue {
    /** S data **/

    borderData = [
        {
            name: '直角',
            value: 1
        },
        {
            name: '圆角',
            value: 2
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
        }
    ]
    resetColor = ''
    /** E data **/

    /** S computed **/

    get content() {
        return this.$store.getters.content
    }

    get styles() {
        return this.$store.getters.styles
    }

    /** E computed **/
}
</script>
