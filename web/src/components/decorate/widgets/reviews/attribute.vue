<template>
    <div>
        <attribute-tabs title="商品评价">
            <div>
                <el-form ref="form" label-width="80px" size="small" label-position="left">
                    <attribute-item title="商品评价">
                        <el-alert :closable="false" type="info" show-icon>
                            <span slot="title">该组件只能控制显示、隐藏。</span>
                        </el-alert>
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
@Component({
    components: {
        AttributeTabs,
        ColorSelect,
        StyleChose,
        Slider,
        AttributeItem
    }
})
export default class Attribute extends Vue {
    /** S data **/
    showSelect = false
    category = 1
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
    selectStyle(style: number) {
        this.content.style = style
        this.showSelect = false
    }
    /** E methods **/
}
</script>
<style lang="scss" scoped>
.style-chose {
    background: #f9f9f9;
    padding: 15px 0;
    height: 260px;
    text-align: center;
    box-sizing: border-box;
}
::v-deep .el-alert__content {
    line-height: 1;
}
</style>
