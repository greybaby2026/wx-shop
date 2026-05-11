<template>
    <div>
        <attribute-tabs title="小程序插件">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="选择直播间">
                        <el-form-item label="直播间数量">
                            <slider :min="1" :max="10" v-model="content.num" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="直播间列表样式">
                        <el-form-item label="" label-width="0">
                            <style-chose v-model="content.style" :data="styleData" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="头部设置">
                        <el-form-item label="头部背景">
                            <el-radio-group v-model="content.header_bg_type">
                                <el-radio :label="1">背景颜色</el-radio>
                                <el-radio :label="2">背景图片</el-radio>
                            </el-radio-group>
                            <color-select
                                v-if="content.header_bg_type == 1"
                                class="m-t-10"
                                v-model="styles.header_bg_color"
                                reset-color="#FF5F4E"
                            />
                            <template v-else>
                                <material-select
                                    class="m-t-10"
                                    ref="materialSelect"
                                    v-model="content.header_bg_image"
                                    :size="60"
                                    upload-bg="#fff"
                                    :enable-domain="false"
                                >
                                    <i class="el-icon-plus lg"></i>
                                </material-select>
                                <div class="muted">建议尺寸：750*100像素</div>
                            </template>
                        </el-form-item>
                        <el-form-item label="左侧图标">
                            <material-select
                                class="m-t-10"
                                ref="materialSelect"
                                v-model="content.header_icon_image"
                                :size="60"
                                upload-bg="#fff"
                                :enable-domain="false"
                            >
                                <i class="el-icon-plus lg"></i>
                            </material-select>
                            <div class="muted">建议尺寸：750*100像素</div>
                        </el-form-item>

                        <el-form-item label="标题文字">
                            <el-input
                                style="width: 280px"
                                v-model="content.header_title"
                                maxlength="10"
                                show-word-limit
                                placeholder="请输入标题"
                            ></el-input>
                        </el-form-item>
                        <el-form-item label="标题颜色">
                            <color-select v-model="styles.header_title_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="标题大小">
                            <slider v-model="styles.header_title_size" :min="12" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="更多按钮">
                        <el-form-item label="按钮显示">
                            <el-radio-group v-model="content.show_haeder_more">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="按钮文字">
                            <el-input
                                style="width: 280px"
                                v-model="content.header_more_text"
                                maxlength="5"
                                show-word-limit
                                placeholder="按钮文字"
                            ></el-input>
                        </el-form-item>
                        <el-form-item label="按钮颜色">
                            <color-select v-model="styles.header_more_color" reset-color="#FFFFFF" />
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
                        <el-form-item label="组件背景">
                            <color-select v-model="styles.content_bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="直播背景">
                            <color-select v-model="styles.bg_color" reset-color="#F8F8F8" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="边距设置">
                        <el-form-item label="直播间距">
                            <slider v-model="styles.margin" />
                        </el-form-item>
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
                        <el-form-item label="商品圆角">
                            <slider v-model="styles.goods_border_radius" />
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
import ActivitySelect from '@/components/activity-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        ActivitySelect
    }
})
export default class Attribute extends Vue {
    /** S data **/
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
            name: '列表模式',
            value: 3
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
