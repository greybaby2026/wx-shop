<template>
    <div class="decorate-center flex-1">
        <el-scrollbar class="ls-scrollbar" style="height: 100%">
            <div class="decorate-phone" :class="{ immersive: immersive, pc: client == 'pc' }">
                <div v-if="client == 'mobile'" class="phone-header" @click="selectIndex = -1">
                    <div class="phone-title nr weight-500">
                        {{ pagesInfo.common.title }}
                    </div>
                </div>
                <!-- pc公共头部 -->
                <template v-if="client == 'pc'">
                    <div class="page-top"></div>
                    <div class="page-header"></div>
                    <div class="page-footer"></div>
                </template>
                <div class="phone-body" @dragover.prevent="dragover" @drop="drop" :data-index="-1" :style="[bodyStyle]">
                    <slot name="header"></slot>
                    <div class="decorate-index">
                        <draggable
                            v-model="pagesData"
                            animation="300"
                            @end="draggableEnd"
                            filter=".forbid"
                            :disabled="disabledDrag"
                            :move="onMove"
                        >
                            <div
                                v-for="(item, index) in pagesData"
                                :key="index"
                                :class="{ forbid: item.forbid }"
                                data-type="pages"
                            >
                                <mouse-cover v-if="showMouseCover(index, 'top')" />

                                <component
                                    :is="widgets[item.name].widget"
                                    :styles="item.styles"
                                    :content="item.content"
                                    :index="index"
                                    :operate="item.operate"
                                ></component>
                                <mouse-cover v-if="showMouseCover(index, 'bottom')" />
                            </div>
                        </draggable>

                        <!-- 页面为空时 -->
                        <div class="empty-pages" :data-index="-1">
                            <mouse-cover v-if="showMouseCover(-1, 'bottom')" />
                        </div>
                    </div>
                    <slot name="footer"></slot>
                </div>
            </div>
        </el-scrollbar>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import MouseCover from '@/components/decorate/mouse-cover.vue'
import Draggable from 'vuedraggable'
import widgets from './widgets'
import pcWidgets from './pc-widgets'
@Component({
    components: {
        MouseCover,
        Draggable
    }
})
export default class DecoratePhone extends Vue {
    @Prop({ default: false }) immersive!: boolean
    @Prop({ default: false }) disabledDrag!: boolean
    @Prop({ default: 'mobile' }) client!: string
    /** S data **/
    widgets: any = this.client == 'mobile' ? widgets : pcWidgets
    /** E data **/

    /** S computed **/
    get dragTarget() {
        return this.$store.state.decorate.dragTarget
    }

    get pagesInfo() {
        return this.$store.state.decorate.pagesInfo
    }

    get pagesData() {
        return this.$store.state.decorate.pagesData
    }

    set pagesData(val: any[]) {
        this.$store.commit('setPagesData', val)
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
    get dragPosition() {
        return this.$store.state.decorate.dragPosition
    }
    set dragPosition(val) {
        this.$store.commit('setDragPosition', val)
    }

    get selectIndex() {
        return this.$store.state.decorate.selectIndex
    }

    set selectIndex(val) {
        this.$store.commit('setSelectIndex', val)
    }

    get showMouseCover() {
        return (index: number, position: string) => {
            if (this.forbid) {
                return false
            }
            if (this.dragIndex === index && this.widgetData && position == this.dragPosition) {
                return true
            }
            return false
        }
    }

    get bodyStyle() {
        const { background_color, background_image, background_type } = this.pagesInfo.common
        switch (background_type) {
            case '0':
                return {}
            case '1':
                return { 'background-color': background_color }
            case '2':
                return { 'background-image': `url(${background_image})` }
        }
    }

    get forbid() {
        return this.pagesData[this.dragIndex] && this.pagesData[this.dragIndex].forbid
    }

    /** E computed **/

    /** S methods **/
    draggableEnd($event: any) {
        const { newIndex, oldIndex } = $event
        this.dragIndex = -2
        if (oldIndex == this.selectIndex) {
            this.selectIndex = newIndex
            return
        }
        if (oldIndex > this.selectIndex && this.selectIndex >= newIndex) {
            this.selectIndex++
            return
        }
        if (oldIndex < this.selectIndex && this.selectIndex <= newIndex) {
            this.selectIndex--
        }
    }

    drop() {
        if (this.dragTarget !== 'widget') {
            return
        }
        // 禁止拖拽到含有forbid的组件
        if (this.forbid) {
            return
        }
        this.$store.dispatch('addWidget')
    }
    dragover($event: any) {
        const index = parseInt($event.target.dataset.index)
        if (String(index) === 'NaN') {
            return
        }
        this.dragIndex = index
        if ($event.target.dataset.type == 'widget') {
            //获取Y轴移动值
            const y = parseFloat($event.offsetY)
            const h = parseFloat(String($event.target.offsetHeight / 2))
            if (y <= h) {
                this.dragPosition = 'top'
            } else {
                this.dragPosition = 'bottom'
            }
        }
        if (index == -1) {
            this.dragPosition = 'bottom'
        }
    }

    onMove(e: any) {
        if (e.relatedContext.element.forbid) {
            return false
        }
        return true
    }
    /** E methods **/
}
</script>

<style lang="scss" scoped>
.decorate-center {
    height: 100%;
    box-sizing: border-box;
    min-width: 450px;
    .decorate-phone {
        width: 375px;
        margin: 16px auto;
        background: #f5f5f5;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.05);
        margin-bottom: 100px;

        &.pc {
            width: calc(100% - 100px);
            position: relative;
            .page-top {
                width: 100%;
                background-color: #101010;
                height: 40px;
                position: absolute;
            }
            .page-header {
                width: 100%;
                background-color: #ffffff;
                height: 126px;
                top: 40px;
                position: absolute;
            }
            .page-footer {
                width: 100%;
                background-color: #101010;
                height: 180px;
                bottom: 0;
                position: absolute;
            }
            .phone-body {
                margin: 0 auto;
                width: 1180px;
                padding-bottom: 200px;
                min-height: 100vh;
            }
        }
        // &.immersive {
        //     //沉浸式状态栏
        //     .phone-header {
        //         position: absolute;
        //         top: 0;
        //     }
        //     .phone-body {
        //         padding-top: 64px;
        //     }
        // }
        .phone-header {
            overflow: hidden;
            cursor: pointer;
            background: url('../../assets/images/phone_header.png') no-repeat;
            background-size: 100%;
            background-color: #fff;
            width: 100%;
            height: 64px;
            box-sizing: border-box;
            .phone-title {
                margin-top: 20px;
                text-align: center;
                height: 44px;
                line-height: 44px;
            }
        }
        .phone-body {
            min-height: 650px;
            background-size: 100% auto;
            background-repeat: no-repeat;
        }
        .decorate-index {
            font-size: 14px;
        }
    }
}
</style>
