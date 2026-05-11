<template>
    <div class="decorate-right">
        <el-scrollbar class="ls-scrollbar" style="height: 100%">
            <div class="decorate-attr">
                <div v-if="selectIndex >= 0">
                    <component :is="widgets[widgetName].attribute" v-if="widgets[widgetName]"></component>
                </div>
                <div v-if="selectIndex == -1">
                    <page-info :config="config" />
                </div>
            </div>
        </el-scrollbar>
        <div class="btns" v-if="client == 'mobile'">
            <el-button size="small" type="promary" @click="selectIndex = -1">页面设置</el-button>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import widgets from '@/components/decorate/widgets'
import { Getter } from 'vuex-class'
import PageInfo from '@/components/decorate/page-info.vue'
import pcWidgets from './pc-widgets'
@Component({
    components: {
        PageInfo
    }
})
export default class DecoratePhone extends Vue {
    @Prop({ default: 'mobile' }) client!: string
    @Prop() config!: any
    @Getter('widgetName') widgetName!: string
    /** S data **/
    widgets = this.client == 'mobile' ? widgets : pcWidgets
    /** E data **/

    /** S computed **/

    get selectIndex() {
        return this.$store.state.decorate.selectIndex
    }
    set selectIndex(val) {
        this.$store.commit('setSelectIndex', val)
    }

    /** E computed **/
}
</script>

<style lang="scss" scoped>
.decorate-right {
    background: #fff;
    width: 400px;
    height: 100%;
    box-sizing: border-box;
    position: relative;
    flex: none;
    .btns {
        position: absolute;
        top: 20px;
        left: -100px;
        z-index: 999;
    }
}
</style>
