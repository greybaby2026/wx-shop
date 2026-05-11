<template>
    <div>
        <attribute-tabs title="商品组">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="标题设置">
                        <el-form-item label="标题名称">
                            <el-input
                                v-model="content.title"
                                placeholder="请输入标题名称"
                                maxlength="20"
                                show-word-limit
                            ></el-input>
                        </el-form-item>
                        <el-form-item label="副标题名称">
                            <el-input
                                v-model="content.subtitle"
                                placeholder="请输入副标题名称"
                                maxlength="50"
                                show-word-limit
                            ></el-input>
                        </el-form-item>
                        <el-form-item label="更多按钮">
                            <el-radio-group v-model="content.show_more">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="广告设置">
                        <el-form-item label="是否显示">
                            <el-radio-group v-model="content.show_adv">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="广告图片">
                            <material-select
                                ref="materialSelect"
                                v-model="content.adv_url"
                                :size="80"
                                upload-bg="#fff"
                                :enable-domain="false"
                            >
                            </material-select>
                            <div class="muted">建议尺寸：1140px*200px</div>
                        </el-form-item>
                        <el-form-item label="链接地址">
                            <link-select client="pc" v-model="content.adv_link" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="选择商品">
                        <el-form-item label-width="0">
                            <div class="p-20" style="background: #f9f9f9">
                                <g-select :content="content" />
                            </div>
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
import NavList from '@/components/decorate/nav-list.vue'

import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import GSelect from '@/components/decorate/goods-select.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        NavList,
        MaterialSelect,
        LinkSelect,
        GSelect
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
