<template>
    <div>
        <attribute-tabs title="公告">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="公告图标">
                        <el-form-item label-width="0">
                            <el-radio-group v-model="content.icon_type">
                                <el-radio :label="1">系统默认</el-radio>
                                <el-radio :label="2">自定义</el-radio>
                            </el-radio-group>
                            <div class="m-t-12" v-if="content.icon_type == 2">
                                <material-select
                                    ref="materialSelect"
                                    v-model="content.icon"
                                    :size="60"
                                    upload-bg="#fff"
                                    :enable-domain="false"
                                >
                                    <i class="el-icon-plus lg"></i>
                                </material-select>
                                <span class="muted xs">建议尺寸： 60*20</span>
                            </div>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="数据显示">
                        <el-form-item label="显示条数">
                            <slider :min="1" :max="5" v-model="content.num" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="最新标签">
                        <el-radio-group v-model="content.show_tag">
                            <el-radio :label="1">显示</el-radio>
                            <el-radio :label="0">隐藏</el-radio>
                        </el-radio-group>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="rgba(0,0,0,0)" />
                        </el-form-item>
                        <el-form-item label="组件背景">
                            <color-select v-model="styles.bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="线条颜色">
                            <color-select v-model="styles.line_color" reset-color="#E5E5E5" />
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
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        Slider,
        AttributeItem,
        MaterialSelect
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

    /** S methods **/
    clearCategory() {
        this.content.category.name = ''
        this.content.category.id = ''
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped></style>
