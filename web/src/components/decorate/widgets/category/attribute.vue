<template>
    <div>
        <attribute-tabs title="商品分类">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="风格选择">
                        <category-style slot="right" v-model="content.style">
                            <el-button slot="trigger" size="mini" type="text">切换风格</el-button>
                        </category-style>
                        <el-form-item label-width="0">
                            <div class="style-chose" style="height: 648px; width: 375px">
                                <style-list :content="content" />
                            </div>
                        </el-form-item>
                    </attribute-item>

                    <attribute-item title="广告图片" desc="建议图片尺寸：750px*340px">
                        <el-form-item label-width="0">
                            <el-alert :closable="false" type="info">
                                <span slot="title">请前往移动端查看效果</span>
                            </el-alert>
                        </el-form-item>
                        <el-form-item label-width="0">
                            <div class="banner">
                                <div class="banner-list">
                                    <div
                                        class="banner-item ls-del-wrap m-b-20"
                                        v-for="(item, index) in content.data"
                                        :key="index"
                                    >
                                        <material-select
                                            v-model="item.url"
                                            :enable-domain="false"
                                            :enable-delete="false"
                                        >
                                            <template v-slot:preview="{ item }">
                                                <div class="banner-iamge muted">
                                                    <el-image :src="item"></el-image>
                                                </div>
                                            </template>
                                            <div slot="upload" class="banner-iamge muted">
                                                <div class="add-image flex-col row-center col-center">
                                                    <i class="el-icon-plus font-size-30"></i>
                                                    添加图片
                                                </div>
                                            </div>
                                        </material-select>
                                        <div class="flex m-t-10">
                                            <div class="m-r-10 lighter">所属分类</div>
                                            <category-select :level="1" v-model="item.category">
                                                <el-input
                                                    slot="trigger"
                                                    style="width: 100%"
                                                    :value="item.category && item.category.name"
                                                    placeholder="请选择分类"
                                                    size="small"
                                                    readonly
                                                >
                                                    <i
                                                        v-if="item.category && item.category.name"
                                                        class="el-icon-close el-input__icon"
                                                        slot="suffix"
                                                        @click.stop="handleDelete"
                                                    >
                                                    </i>
                                                    <i v-else class="el-icon-arrow-right el-input__icon" slot="suffix">
                                                    </i>
                                                </el-input>
                                            </category-select>
                                        </div>
                                        <div class="flex m-t-10">
                                            <div class="m-r-10 lighter">链接地址</div>
                                            <link-select v-model="item.link" />
                                        </div>

                                        <i @click="handleDelete(index)" class="el-icon-close ls-icon-del"></i>
                                    </div>
                                </div>
                                <el-button size="small" class="add-banner" @click="handleAdd">+ 添加</el-button>
                            </div>
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
            <!-- <div slot="styles">
                <el-form
                    ref="form"
                    label-width="80px"
                    size="small"
                    label-position="left"
                >
                    <attribute-item title="分类图片样式">
                        <el-form-item label="图片圆角">
                            <slider v-model="styles.border_radius" :max="60" />
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div> -->
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
import CategoryStyle from './category-style.vue'
import StyleList from './style-list.vue'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import CategorySelect from '@/components/category-select/dialog.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        CategoryStyle,
        StyleList,
        MaterialSelect,
        LinkSelect,
        CategorySelect
    }
})
export default class Attribute extends Vue {
    /** S data **/
    showSelect = false
    category = 1
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
    handleAdd() {
        if (!Array.isArray(this.content.data)) {
            this.content.data = []
        }
        this.content.data.push({
            url: '',
            link: {},
            category: {}
        })
    }
    handleDelete(index: number) {
        this.content.data.splice(index, 1)
    }

    selectStyle(style: number) {
        this.content.style = style
        this.showSelect = false
    }
    /** E methods **/
}
</script>
<style lang="scss" scoped>
.style-chose {
    background: #f9f9f9;
    padding: 15px 0;
    height: 260px;
    text-align: center;
    box-sizing: border-box;
}
::v-deep .el-alert__content {
    line-height: 1;
}

.banner-list {
    .banner-item {
        background: #f9f9f9;
        padding: 10px 30px;
        .banner-iamge {
            width: 300px;
            height: 136px;
            .el-image,
            .add-image {
                cursor: pointer;
                width: 100%;
                height: 100%;
                border-radius: 4px;
                background: #fff;
                border: 1px dashed $--border-base;
            }
        }
    }
}
.add-banner {
    width: 100%;
    margin-bottom: 20px;
}
</style>
