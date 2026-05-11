<template>
    <div>
        <attribute-tabs title="图文">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="图片设置" :desc="`最多可添加${limit}条，图片建议尺寸：280*280`">
                        <div class="graphic-list">
                            <draggable v-model="content.data" animation="300">
                                <div
                                    class="graphic-item ls-del-wrap"
                                    v-for="(item, index) in content.data"
                                    :key="index"
                                >
                                    <el-form-item label="图片" label-width="60px">
                                        <material-select
                                            v-model="item.url"
                                            :size="50"
                                            :enable-domain="false"
                                            upload-bg="#fff"
                                        >
                                            <i class="el-icon-plus lg"></i>
                                        </material-select>
                                    </el-form-item>
                                    <el-form-item label="主标题" label-width="60px">
                                        <el-input
                                            style="width: 260px"
                                            v-model="item.title"
                                            show-word-limit
                                            placeholder="请输入标题"
                                        ></el-input>
                                    </el-form-item>
                                    <el-form-item label="颜色" label-width="60px">
                                        <color-select v-model="item.title_color" reset-color="#333333" />
                                    </el-form-item>
                                    <el-form-item label="主标题" label-width="60px">
                                        <el-input
                                            style="width: 260px"
                                            v-model="item.subtitle"
                                            show-word-limit
                                            placeholder="请输入标题"
                                        ></el-input>
                                    </el-form-item>
                                    <el-form-item label="颜色" label-width="60px">
                                        <color-select v-model="item.subtitle_color" reset-color="#666666" />
                                    </el-form-item>
                                    <el-form-item label="链接" label-width="60px">
                                        <link-select v-model="item.link" />
                                    </el-form-item>
                                    <el-form-item label="背景色" label-width="60px">
                                        <color-select v-model="item.bg_color" reset-color="#FFFFFF" />
                                    </el-form-item>
                                    <i
                                        v-if="content.data.length > 1"
                                        @click="handleDelete(index)"
                                        class="el-icon-close ls-icon-del"
                                    ></i>
                                </div>
                            </draggable>
                            <el-form-item label-width="0">
                                <el-button
                                    size="small"
                                    v-if="content.data.length < limit"
                                    class="add-btn"
                                    @click="handleAdd"
                                    >+ 添加</el-button
                                >
                            </el-form-item>
                        </div>
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
import LinkSelect from '@/components/link-select/index.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        LinkSelect,
        Draggable
    }
})
export default class SearchAttribute extends Vue {
    /** S data **/

    limit = 10
    /** E data **/

    /** S computed **/

    get content() {
        return this.$store.getters.content
    }

    get styles() {
        return this.$store.getters.styles
    }

    /** E computed **/

    handleAdd() {
        if (this.content.data.length < this.limit) {
            this.content.data.push({
                url: '',
                title: '标题名称',
                title_color: '#333333',
                subtitle: '副标题名称',
                subtitle_color: '#666666',
                link: {},
                bg_color: '#FFFFFF'
            })
        } else {
            this.$message.warning(`最多添加${this.limit}条`)
        }
    }
    handleDelete(index: number) {
        if (this.content.data.length <= 1) {
            return
        }
        this.content.data.splice(index, 1)
    }
}
</script>
<style lang="scss" scoped>
.graphic-list {
    .graphic-item {
        background: #f9f9f9;
        padding: 20px 20px 1px;
        margin-bottom: 20px;
    }
    .add-btn {
        width: 100%;
    }
}
</style>
