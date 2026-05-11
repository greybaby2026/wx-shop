<template>
    <div>
        <attribute-tabs title="悬浮设置">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="展示样式">
                        <style-chose v-model="content.style" :data="styleData" />
                    </attribute-item>
                    <attribute-item title="悬浮菜单">
                        <div class="nav-list">
                            <draggable v-model="content.data" animation="300">
                                <div
                                    class="nav-item ls-del-wrap item"
                                    v-for="(item, index) in content.data"
                                    :key="index"
                                >
                                    <div>
                                        <div class="flex m-b-10">
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
                                            <el-form-item label="名称" label-width="40px" v-if="content.style == 1">
                                                <el-input
                                                    style="width: 280px"
                                                    v-model="item.name"
                                                    maxlength="8"
                                                    show-word-limit
                                                    placeholder="请输入名称"
                                                ></el-input>
                                            </el-form-item>
                                            <el-form-item label="链接" label-width="40px" v-if="item.type == 'nav'">
                                                <link-select client="pc" v-model="item.link" />
                                            </el-form-item>
                                        </div>
                                    </div>
                                    <i @click="onDelete(index)" class="el-icon-close ls-icon-del"></i>
                                </div>
                            </draggable>
                        </div>
                        <el-form-item label-width="0" v-if="content.data.length < 5">
                            <el-button size="small" class="add-nav" @click="onAdd('nav')">+ 添加导航</el-button>
                            <el-button size="small" class="add-server" @click="onAdd('server')">+ 添加客服</el-button>
                        </el-form-item>
                    </attribute-item>
                </el-form>
            </div>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Draggable from 'vuedraggable'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        LinkSelect,
        MaterialSelect,
        Draggable
    }
})
export default class Attribute extends Vue {
    /** S data **/
    styleData = [
        {
            name: '图片+文字',
            value: 1
        },
        {
            name: '图片',
            value: 2
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
    onAdd(type: string) {
        const item = {
            icon: '',
            select_icon: '',
            name: '',
            type,
            link: {}
        }
        if (this.content.data.length > 5) {
            return this.$message.warning('最多五个导航')
        }
        this.content.data.push(item)
    }
    onDelete(index: number) {
        const { data } = this.content
        if (data.length <= 1) {
            return this.$message.warning('最少保留一个导航')
        }
        this.content.data.splice(index, 1)
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
</style>
