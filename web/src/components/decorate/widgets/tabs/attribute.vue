<template>
    <div>
        <attribute-tabs title="选项卡">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="选项卡设置">
                        <el-form-item label-width="0">
                            <div class="tabs-list">
                                <draggable v-model="content.data" animation="300" @end="draggableEnd">
                                    <div
                                        class="tabs-item"
                                        v-for="(item, index) in content.data"
                                        :key="index"
                                        :class="{
                                            active: content.active == index
                                        }"
                                        @click="content.active = index"
                                    >
                                        <div class="flex row-between">
                                            <span>{{ item.name }}</span>
                                            <div class="del-btn">
                                                <el-button type="text" @click.stop="onDelete(index)">删除</el-button>
                                            </div>
                                        </div>
                                    </div>
                                </draggable>
                            </div>
                            <el-button style="width: 100%" size="small" @click="onAdd">+ 添加选项卡</el-button>
                        </el-form-item>
                    </attribute-item>
                    <template v-for="(item, index) in content.data">
                        <div :key="index" v-if="content.active == index">
                            <attribute-item title="选项卡内容">
                                <el-form-item label-width="0">
                                    <div class="p-20" style="background: #f9f9f9">
                                        <el-form-item label="选项名称">
                                            <el-input
                                                style="width: 200px"
                                                v-model="item.name"
                                                maxlength="5"
                                                show-word-limit
                                                placeholder="请输入选项名称"
                                            ></el-input>
                                        </el-form-item>
                                        <g-select :content="item" />
                                    </div>
                                </el-form-item>
                            </attribute-item>
                            <g-attr :content="item" :styles="item" />
                        </div>
                    </template>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="背景设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="选项卡样式">
                        <el-form-item label="选项卡背景">
                            <color-select v-model="styles.bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="未选中标题">
                            <color-select v-model="styles.color" reset-color="#101010" />
                        </el-form-item>
                        <el-form-item label="已选中标题">
                            <color-select v-model="styles.active_color" reset-color="#FF2C3C" />
                        </el-form-item>
                        <el-form-item label="选项条">
                            <el-radio-group v-model="content.show_line">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="选项条颜色" v-if="content.show_line == 1">
                            <color-select v-model="styles.line_color" reset-color="#FF2C3C" />
                        </el-form-item>
                        <el-form-item label="选中背景">
                            <el-radio-group v-model="content.has_active_bg">
                                <el-radio :label="0">无</el-radio>
                                <el-radio :label="1">背景色</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="" v-if="content.has_active_bg">
                            <color-select v-model="styles.active_bg_color" reset-color="" />
                        </el-form-item>
                        <el-form-item label="间距">
                            <slider v-model="styles.padding" :min="10" />
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
                        <el-form-item label="上圆角">
                            <slider v-model="styles.border_radius_top" />
                        </el-form-item>
                        <el-form-item label="下圆角">
                            <slider v-model="styles.border_radius_bottom" />
                        </el-form-item>
                    </attribute-item>
                    <template v-for="(item, index) in content.data">
                        <attribute-item v-if="content.active == index" title="商品样式" :key="index">
                            <el-form-item label="商品背景">
                                <color-select v-model="item.bg_color" reset-color="#FFFFFF" />
                            </el-form-item>
                            <el-form-item label="组件背景">
                                <color-select v-model="item.content_bg_color" reset-color="" />
                            </el-form-item>
                            <el-form-item label="商品间距">
                                <slider v-model="item.margin" />
                            </el-form-item>
                            <el-form-item label="组件边距">
                                <slider v-model="item.padding" />
                            </el-form-item>
                            <el-form-item label="上边距">
                                <slider v-model="item.padding_top" />
                            </el-form-item>
                            <el-form-item label="下边距">
                                <slider v-model="item.padding_bottom" />
                            </el-form-item>
                            <el-form-item label="左右边距">
                                <slider v-model="item.padding_horizontal" />
                            </el-form-item>
                            <el-form-item label="上圆角">
                                <slider v-model="item.border_radius_top" />
                            </el-form-item>
                            <el-form-item label="下圆角">
                                <slider v-model="item.border_radius_bottom" />
                            </el-form-item>
                            <el-form-item label="商品圆角">
                                <slider v-model="item.goods_border_radius" />
                            </el-form-item>
                        </attribute-item>
                    </template>
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
import GoodsSelect from '@/components/goods-select/index.vue'
import CategorySelect from '@/components/category-select/index.vue'
import GSelect from '../../goods-select.vue'
import GAttr from '../goodsgroup/common-attr.vue'
import Draggable from 'vuedraggable'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        GoodsSelect,
        CategorySelect,
        GSelect,
        GAttr,
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
    draggableEnd($event: any) {
        const { newIndex, oldIndex } = $event

        if (oldIndex == this.content.active) {
            this.content.active = newIndex
            return
        }
        if (oldIndex > this.content.active && this.content.active >= newIndex) {
            this.content.active++
            return
        }
        if (oldIndex < this.content.active && this.content.active <= newIndex) {
            this.content.active--
        }
    }
    onAdd() {
        this.content.data.push({
            name: '选项卡',
            style: 1,
            goods_type: 1,
            show_title: 1,
            show_price: 1,
            show_scribing_price: 1,
            show_btn: 1,
            btn_text: '购买',
            btn_bg_type: 1,
            category: {
                id: '',
                name: '',
                number: 1
            },
            data: [],
            title_color: '#101010',
            scribing_price_color: '#999999',
            price_color: '#FF2C3C',
            btn_bg_color: '#FF2C3C',
            btn_color: '#FFFFFF',
            btn_border_radius: 30,
            btn_border_color: '',
            root_bg_color: '',
            bg_color: '#FFFFFF',
            margin: 10,
            padding_top: 10,
            padding_horizontal: 10,
            padding_bottom: 10,
            border_radius_top: 4,
            border_radius_bottom: 4
        })
    }
    onDelete(index: number) {
        if (this.content.data.length <= 1) {
            return this.$message.warning('最少保留一个')
        }
        this.content.data.splice(index, 1)
        this.content.active = 0
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped>
.tabs-list {
    .tabs-item {
        height: 32px;
        line-height: 32px;
        padding: 0 10px;
        border-radius: 4px;
        border: $--border-base;
        margin-bottom: 10px;
        cursor: move;
        .del-btn {
            display: none;
        }
        &:hover {
            .del-btn {
                display: block;
            }
        }
        &.active {
            border-color: $--color-primary;
        }
    }
}
</style>
