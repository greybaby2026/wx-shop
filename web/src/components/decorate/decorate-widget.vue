<template>
    <div class="decorate-left" :class="{ pc: client == 'pc' }">
        <el-scrollbar class="ls-scrollbar" style="height: 100%">
            <ul class="widget-list flex flex-wrap" v-if="client == 'pc'">
                <li
                    class="widget-item flex-col row-center col-center"
                    v-for="(citem, cindex) in leftMenu"
                    :key="cindex"
                    :draggable="true"
                    @dragstart="dragStart(citem)"
                    @dragend="dragEnd"
                >
                    <img :draggable="false" class="item-icon" :src="citem.icon" mode="" />
                    <div class="m-t-5">{{ citem.title }}</div>
                </li>
            </ul>
            <div class="p-16" v-else>
                <el-collapse :value="['basics', 'goods', 'marketing', 'auxiliary']">
                    <el-collapse-item v-for="(item, index) in leftMenu" :key="index" :name="item.name">
                        <template slot="title">
                            <span class="sm">{{ item.title }}({{ item.children.length }})</span>
                        </template>
                        <ul class="widget-list flex flex-wrap">
                            <li
                                class="widget-item flex-col row-center col-center"
                                v-for="(citem, cindex) in item.children"
                                :key="cindex"
                                :draggable="true"
                                @dragstart="dragStart(citem)"
                                @dragend="dragEnd"
                            >
                                <img :draggable="false" class="item-icon" :src="citem.icon" mode="" />
                                <div class="m-t-5">{{ citem.title }}</div>
                            </li>
                        </ul>
                    </el-collapse-item>
                </el-collapse>
            </div>
        </el-scrollbar>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import { decorateMenu, pcDecorateMenu } from '@/utils/decorate'
import { Getter } from 'vuex-class'
@Component
export default class DecorateLeft extends Vue {
    @Prop({ default: 'mobile' }) client!: string
    @Getter('widgetName') widgetName!: string
    /** S data **/
    leftMenu = this.client == 'pc' ? pcDecorateMenu : decorateMenu
    /** E data **/

    /** S computed **/

    get dragTarget() {
        return this.$store.state.decorate.dragTarget
    }

    set dragTarget(val) {
        this.$store.commit('setDragTarget', val)
    }

    get dragIndex() {
        return this.$store.state.decorate.dragIndex
    }

    set dragIndex(val) {
        this.$store.commit('setDragIndex', val)
    }

    get widgetData() {
        return this.$store.state.decorate.widgetData
    }

    set widgetData(val) {
        this.$store.commit('setWidgetData', val)
    }

    /** E computed **/

    /** S methods **/
    dragStart(item: any) {
        this.dragTarget = 'widget'
        this.widgetData = item
    }
    dragEnd() {
        this.dragTarget = ''
        this.widgetData = null
        this.dragIndex = -2
    }

    /** E methods **/
}
</script>

<style lang="scss" scoped>
.decorate-left {
    background: #fff;
    width: 290px;
    height: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
    flex: none;
    &.pc {
        width: 120px;
        .widget-list {
            padding-left: 16px;
        }
    }
    ::v-deep .el-collapse {
        border: none;

        &-item__header {
            border: $--border-base;
            height: 40px;
            line-height: 40px;
            padding-left: 16px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        &-item__wrap {
            border: none;
            overflow: unset;
        }
    }
    .widget-list {
        .widget-item {
            position: relative;
            width: 72px;
            height: 72px;
            margin-right: 18px;
            margin-top: 16px;
            cursor: move;
            border-radius: 5px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0px 0px 6px rgba(0, 0, 0, 0.06);
            box-sizing: border-box;
            .item-icon {
                width: 26px;
                height: 26px;
            }
            &:hover {
                z-index: 99;
                border: 1px solid $--color-primary;
            }
            &:nth-of-type(3n) {
                margin-right: 0;
            }
        }
    }
}
</style>
