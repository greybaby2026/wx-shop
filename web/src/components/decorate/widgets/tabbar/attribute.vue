<template>
    <div>
        <attribute-tabs title="底部导航">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="选择样式">
                        <el-form-item label-width="0">
                            <el-radio-group v-model="content.style">
                                <el-radio label="1">图片+文字</el-radio>
                                <el-radio label="2">图片</el-radio>
                                <el-radio label="3">文字</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="导航内容">
                        <div class="nav-list">
                            <draggable v-model="content.data" animation="300" draggable=".draggable" :move="onMove">
                                <div
                                    class="nav-item ls-del-wrap item"
                                    :class="{ draggable: index != 0 }"
                                    v-for="(item, index) in content.data"
                                    :key="index"
                                >
                                    <div>
                                        <div class="flex m-b-10" v-if="content.style != 3">
                                            <material-select
                                                class="m-r-10"
                                                ref="materialSelect"
                                                v-model="item.icon"
                                                :size="48"
                                                upload-bg="#fff"
                                                :enable-domain="false"
                                            >
                                                <div class="text-center">
                                                    <i class="el-icon-plus"></i>
                                                    <div>未选中</div>
                                                </div>
                                            </material-select>
                                            <material-select
                                                ref="materialSelect"
                                                v-model="item.select_icon"
                                                :size="48"
                                                upload-bg="#fff"
                                                :enable-domain="false"
                                            >
                                                <div class="text-center">
                                                    <i class="el-icon-plus"></i>
                                                    <div>选中</div>
                                                </div>
                                            </material-select>
                                        </div>
                                        <div>
                                            <el-form-item label="名称" label-width="40px" v-if="content.style != 2">
                                                <el-input
                                                    style="width: 200px"
                                                    v-model="item.name"
                                                    maxlength="6"
                                                    show-word-limit
                                                    placeholder="请输入导航名称"
                                                ></el-input>
                                            </el-form-item>
                                            <el-form-item label="链接" label-width="40px">
                                                <link-select :disabled="index == 0" v-model="item.link" />
                                            </el-form-item>
                                        </div>
                                    </div>
                                    <i v-if="index > 0" @click="onDelete(index)" class="el-icon-close ls-icon-del"></i>
                                </div>
                            </draggable>
                        </div>
                        <el-form-item label-width="0">
                            <el-button size="small" v-if="content.data.length < 5" class="add-nav" @click="onAdd"
                                >+ 添加导航{{ content.data.length }}/5</el-button
                            >
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="背景颜色">
                            <color-select v-model="styles.bg_color" reset-color="#FFFFFF" />
                        </el-form-item>
                        <el-form-item label="字体颜色">
                            <el-radio-group v-model="content.color_type">
                                <el-radio :label="1">跟随系统主题</el-radio>
                                <el-radio :label="2">自定义</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <template v-if="content.color_type == 2">
                            <el-form-item label="默认颜色">
                                <color-select v-model="styles.color" reset-color="#666666" />
                            </el-form-item>
                            <el-form-item label="选中颜色">
                                <color-select v-model="styles.select_color" reset-color="#FF2C3C" />
                            </el-form-item>
                        </template>
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
        LinkSelect,
        Draggable
    }
})
export default class Attribute extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
    /** S data **/
    $refs!: { materialSelect: any }
    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/
    onAdd() {
        if (this.content.data.length > 5) {
            return this.$message.warning('最多五个导航')
        }
        this.content.data.push({
            icon: '',
            select_icon: '',
            name: '导航',
            link: {}
        })
    }
    onDelete(index: number) {
        const { data } = this.content
        if (index == 0) {
            return this.$message.warning('首页导航不能删除')
        }
        if (data.length <= 2) {
            return this.$message.warning('最少两个导航')
        }
        this.content.data.splice(index, 1)
    }
    onMove(e: any) {
        if (e.relatedContext.index == 0) {
            return false
        }
        return true
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped>
.nav-list {
    .nav-item {
        background: #f9f9f9;
        padding: 20px 20px 1px;
        margin-bottom: 20px;
        cursor: move;
    }
}
.add-nav {
    width: 100%;
}
</style>
