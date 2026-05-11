<template>
    <div>
        <attribute-tabs title="底部设置">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="底部图标">
                        <draggable v-model="content.data" animation="300">
                            <div class="item" v-for="(item, index) in content.data" :key="index">
                                <el-form-item label="图标">
                                    <material-select
                                        class="m-r-10"
                                        ref="materialSelect"
                                        v-model="item.url"
                                        :size="60"
                                        upload-bg="#fff"
                                        :enable-domain="false"
                                    >
                                        <i class="el-icon-plus lg"></i>
                                    </material-select>
                                </el-form-item>
                                <el-form-item label="名称">
                                    <el-input
                                        style="width: 200px"
                                        v-model="item.name"
                                        maxlength="8"
                                        show-word-limit
                                        placeholder="请输入标题名称"
                                    ></el-input>
                                </el-form-item>
                            </div>
                        </draggable>
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
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        MaterialSelect,
        Draggable
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

<style lang="scss" scoped>
.item {
    background: #f9f9f9;
    padding: 15px 20px 1px;
    cursor: move;
    margin-bottom: 20px;
}
</style>
