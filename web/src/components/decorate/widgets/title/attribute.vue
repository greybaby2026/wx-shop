<template>
    <div>
        <attribute-tabs title="标题栏">
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
                    </attribute-item>
                    <attribute-item title="主标题">
                        <el-form-item label="主标题标题">
                            <el-input
                                v-model="content.title"
                                maxlength="8"
                                show-word-limit
                                placeholder="请输入标题"
                            ></el-input>
                        </el-form-item>
                        <el-form-item label="字体颜色">
                            <color-select v-model="styles.title_color" reset-color="#333333" />
                        </el-form-item>
                        <el-form-item label="字体大小">
                            <slider v-model="styles.title_font_size" :min="12" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="副标题">
                        <el-form-item label="副标题标题">
                            <el-input
                                style="width: 220px"
                                v-model="content.subtitle"
                                maxlength="10"
                                show-word-limit
                                placeholder="请输入副标题"
                            ></el-input>
                            <el-checkbox
                                :true-label="1"
                                :false-label="0"
                                class="m-l-10"
                                v-model="content.hidden_subtitle"
                                >隐藏</el-checkbox
                            >
                        </el-form-item>
                        <el-form-item label="字体颜色">
                            <color-select v-model="styles.subtitle_color" reset-color="#999999" />
                        </el-form-item>
                        <el-form-item label="字体大小">
                            <slider v-model="styles.subtitle_font_size" :min="12" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="右侧按钮" v-if="content.style == 2">
                        <el-form-item label="是否显示">
                            <el-radio-group v-model="content.show_more">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <template v-if="content.show_more">
                            <el-form-item label="文字内容">
                                <el-input
                                    v-model="content.more_title"
                                    maxlength="6"
                                    show-word-limit
                                    placeholder="请输入文字内容"
                                ></el-input>
                            </el-form-item>
                            <el-form-item label="按钮颜色">
                                <color-select v-model="styles.more_color" reset-color="#999999" />
                            </el-form-item>
                            <el-form-item label="链接地址">
                                <link-select v-model="content.link" />
                            </el-form-item>
                        </template>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部颜色">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                        <el-form-item label="组件颜色">
                            <color-select v-model="styles.bg_color" reset-color="#F5F5F5" />
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
import LinkSelect from '@/components/link-select/index.vue'
import StyleSelect from '@/components/decorate/style-select.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        LinkSelect,
        StyleSelect
    }
})
export default class Attribute extends Vue {
    /** S data **/

    styleData = [
        {
            value: 1,
            img: require('@/assets/images/title_style1.png'),
            text: '风格1'
        },
        {
            value: 2,
            img: require('@/assets/images/title_style2.png'),
            text: '风格2'
        }
    ]
    alignData = [
        {
            name: '左对齐',
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
</style>
