<template>
    <div>
        <attribute-tabs title="辅助线">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="选择样式">
                        <template>
                            <el-radio v-model="content.separate" :label="'solid'">实线</el-radio>
                            <el-radio v-model="content.separate" :label="'dashed'">虚线</el-radio>
                            <el-radio v-model="content.separate" :label="'dotted'">点状</el-radio>
                        </template>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="rgba(0,0,0,0)" />
                        </el-form-item>
                        <el-form-item label="线条颜色">
                            <color-select v-model="styles.line_color" reset-color="#E5E5E5" />
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
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        Slider,
        AttributeItem
    }
})
export default class Attribute extends Vue {
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
}
</script>

<style lang="scss" scoped></style>
