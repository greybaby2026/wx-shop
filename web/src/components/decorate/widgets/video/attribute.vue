<template>
    <div>
        <attribute-tabs title="视频">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="选择视频">
                        <el-form-item label="视频类型">
                            <el-radio-group v-model="content.video_type">
                                <el-radio :label="1">手动上传</el-radio>
                                <el-radio :label="2">视频链接</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label-width="0">
                            <material-select
                                v-if="content.video_type == 1"
                                v-model="content.url"
                                type="video"
                                :size="60"
                                :enable-domain="false"
                            >
                                <i class="el-icon-plus lg"></i>
                            </material-select>
                            <el-input v-else v-model="content.url" placeholder="请输入视频链接"></el-input>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="视频封面">
                        <el-form-item label-width="0">
                            <material-select v-model="content.poster" :size="60" :enable-domain="false">
                                <i class="el-icon-plus lg"></i>
                            </material-select>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="比例">
                        <el-form-item label-width="0">
                            <el-radio-group v-model="content.proportion">
                                <el-radio :label="1">16:9</el-radio>
                                <el-radio :label="2">4:3</el-radio>
                                <el-radio :label="3">1:1</el-radio>
                            </el-radio-group>
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
import AttributeItem from '@/components/decorate/attribute-item.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import Slider from '@/components/decorate/slider.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect
    }
})
export default class SearchAttribute extends Vue {
    /** S data **/

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
