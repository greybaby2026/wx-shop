<template>
    <div>
        <attribute-tabs title="头部设置">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="导航菜单" desc="最多可添加9个">
                        <nav-list client="pc" :has-img="false" v-model="content.data" :limit="9" />
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
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem,
        NavList,
        MaterialSelect
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
