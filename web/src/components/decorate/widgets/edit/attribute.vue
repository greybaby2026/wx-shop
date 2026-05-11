<template>
    <div>
        <attribute-tabs title="富文本">
            <div slot="content">
                <div style="padding: 10px">
                    <editor
                        v-model="content.data"
                        :decorate="true"
                        width="375"
                        ref="Editor"
                        :menu="[
                            'head',
                            'bold',
                            'fontSize',
                            'fontName',
                            'italic',
                            'underline',
                            'strikeThrough',
                            'indent',
                            'lineHeight',
                            'foreColor',
                            'link',
                            'list',
                            'justify',
                            'quote',
                            'emoticon',
                            'image',
                            'undo',
                            'redo'
                        ]"
                    />
                </div>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <!-- <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                    </attribute-item> -->
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
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import Slider from '@/components/decorate/slider.vue'

import Editor from '@/components/editor.vue'

@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        Editor
    }
})
export default class SearchAttribute extends Vue {
    $refs!: { Editor: any }

    /** S data **/
    Data = ''
    /** E data **/

    /** S computed **/
    get areaLists() {
        return this.$store.getters.content
    }

    get content() {
        return this.$store.getters.content
    }

    get styles() {
        return this.$store.getters.styles
    }
    created() {}

    /** E computed **/
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
.hotarea {
    padding: 10px;
    &-tip {
        padding: 10px;
        background-color: $--color-primary-light-9;
        border-radius: 5px;
        width: 100%;
    }
    .image {
        display: flex;
        justify-content: center;
        margin-top: 10px;
        width: 100%;
        border: 1px solid $--color-primary-light-2;
        background: #f9f9f9;
        position: relative;
    }
}
.add-banner {
    width: 100%;
    margin-top: 20px;
}
.area {
    position: absolute;
    background-color: #4073fa;
    opacity: 0.9;
    display: flex;
    color: white;
    justify-content: center;
    align-items: center;
}
</style>
