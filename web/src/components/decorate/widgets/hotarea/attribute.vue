<template>
    <div>
        <attribute-tabs title="热区">
            <div slot="content">
                <div class="hotarea">
                    <div class="hotarea-tip">建议图片宽度750px，高度200px-2000px。</div>
                    <div class="image">
                        <div v-if="!content.data.imgurl" style="height: 180px; line-height: 180px">750x高度不限</div>
                        <el-image fit="cover" :src="$getImageUri(content.data.imgurl)" v-else></el-image>
                        <div
                            v-if="content.data.imgurl"
                            class="area"
                            v-for="(item, index) in content.data.areaLists"
                            :style="{
                                top: item.areaY + 'px',
                                left: item.areaX + 'px',
                                height: item.areaHeight + 'px',
                                width: item.areaWidth + 'px'
                            }"
                            :key="index"
                        >
                            {{ item.url.name }}
                        </div>
                    </div>
                    <div style="background: #f9f9f9; margin-top: 10px" class="p-20">
                        <div class="flex m-b-8">
                            <div class="lighter m-r-20">图片</div>
                            <material-select
                                class="m-r-10"
                                ref="materialSelect"
                                v-model="content.data.imgurl"
                                :size="50"
                                upload-bg="#fff"
                                :enable-domain="false"
                                @change="handlechange"
                            >
                                <i class="el-icon-plus lg"></i>
                            </material-select>
                        </div>
                    </div>
                    <HotareaSelect
                        :imgSrc="$getImageUri(content.data.imgurl)"
                        :imgUrl="content.data.imgurl"
                        v-model="content.data.areaLists"
                    ></HotareaSelect>
                </div>
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
import HotareaSelect from '@/components/decorate/hotarea-select.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        LinkSelect,
        Draggable,
        HotareaSelect
    }
})
export default class SearchAttribute extends Vue {
    /** S data **/

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

    /** E computed **/
    handlechange() {
        return (this.$store.getters.content.data.areaLists = [])
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
