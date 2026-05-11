<template>
    <div>
        <attribute-tabs title="图片魔方">
            <div slot="content">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="风格选择">
                        <el-form-item label-width="0">
                            <style-chose v-model="content.style" :data="styleData" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="魔方布局">
                        <el-form-item label-width="0">
                            <cube-layout v-model="content.data" :facade="content.style" @change="currentChange" />
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="图片设置">
                        <div style="background: #f9f9f9" class="p-20">
                            <div class="flex m-b-8">
                                <div class="lighter m-r-20">图片</div>
                                <material-select
                                    class="m-r-10"
                                    ref="materialSelect"
                                    v-model="currentData.url"
                                    :size="50"
                                    upload-bg="#fff"
                                    :enable-domain="false"
                                >
                                    <i class="el-icon-plus lg"></i>
                                </material-select>
                            </div>
                            <div class="flex">
                                <div class="lighter m-r-20">链接</div>
                                <link-select class="flex-1" v-model="currentData.link" />
                            </div>
                        </div>
                    </attribute-item>
                </el-form>
            </div>
            <div slot="styles">
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="颜色设置">
                        <el-form-item label="底部背景">
                            <color-select v-model="styles.root_bg_color" reset-color="#FF2C3C" />
                        </el-form-item>
                    </attribute-item>

                    <attribute-item title="边距设置">
                        <el-form-item label="图片间距">
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
                </el-form>
            </div>
        </attribute-tabs>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import AttributeTabs from '@/components/decorate/attribute-tabs.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import Slider from '@/components/decorate/slider.vue'
import AttributeItem from '@/components/decorate/attribute-item.vue'
import Draggable from 'vuedraggable'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import StyleChose from '@/components/decorate/style-chose.vue'
import CubeLayout from '@/components/decorate/cube-layout.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        Slider,
        Draggable,
        AttributeItem,
        MaterialSelect,
        LinkSelect,
        StyleChose,
        CubeLayout
    }
})
export default class Attribute extends Vue {
    // S Data
    current = 0
    styleData = [
        {
            name: '一行1个',
            value: 1,
            num: 1
        },
        {
            name: '一行2个',
            value: 2,
            num: 2
        },
        {
            name: '一行3个',
            value: 3,
            num: 3
        },
        {
            name: '左1右2',
            value: 4,
            num: 3
        },
        {
            name: '左2右2',
            value: 5,
            num: 4
        },
        {
            name: '上1下2',
            value: 6,
            num: 3
        }
    ]

    // E Data

    /** S computed **/

    get content() {
        console.log(this.$store.getters.content)

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

    get currentData() {
        const index = this.current
        if (this.content.data.length > 0) {
            return this.content.data[index]
        }
    }

    /** E computed **/
    @Watch('content.style', { immediate: true })
    styleChange(val: number) {
        this.current = 0
        const num = this.styleData.find(item => item.value == val)?.num || 1
        this.content.data = this.handleArray(num)
    }
    /** S Methods **/
    currentChange(val: number) {
        this.current = val
    }

    handleArray(num: number) {
        const data = JSON.parse(JSON.stringify(this.content.data))

        const array = []
        for (let i = 0; i < num; i++) {
            if (data[i]) {
                array.push(data[i])
            } else {
                array.push({
                    url: '',
                    link: {}
                })
            }
        }
        return array
    }
    /** E Methods **/
}
</script>

<style lang="scss" scoped>
.style-item {
    float: left;
    color: #d7d7d7;
    width: 110px;
    height: 40px;
    margin-right: 10px;
    border-radius: 4px;
    background: #fff;
    margin-bottom: 10px;
    border: 1px solid #d7d7d7;
}
.active {
    border-color: $--color-primary;
    color: $--color-primary;
}
.style-item:nth-child(4n-1) {
    margin-right: 0;
}

.nav-list {
    .nav-item {
        background: #f9f9f9;
        padding: 15px 20px 1px 20px;
        cursor: move;
    }
}
</style>
