<template>
    <div>
        <attribute-tabs title="商品组">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="秒杀设置">
                        <el-form-item label="标题名称">
                            <el-input v-model="content.title" placeholder="请输入标题名称"></el-input>
                        </el-form-item>
                        <el-form-item label="更多按钮">
                            <el-radio-group v-model="content.show_more">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </attribute-item>
                    <attribute-item title="选择秒杀商品">
                        <el-form-item label="" label-width="0">
                            <el-radio-group v-model="content.data_type">
                                <el-radio :label="1">自动获取</el-radio>
                                <el-radio :label="2">手动添加</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="显示数量" v-if="content.data_type == 1">
                            <slider :min="1" :max="10" v-model="content.num" />
                        </el-form-item>
                        <el-form-item v-else label-width="0">
                            <activity-select type="seckill" v-model="content.data" />
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
import ActivitySelect from '@/components/activity-select/index.vue'
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
        ActivitySelect
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
