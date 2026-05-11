<template>
    <div class="widget-package" @click.stop="setSelect">
        <div class="widget-suspension">
            <!-- 悬浮窗 -->
            <div class="widget-mask" :data-index="index" data-type="widget">
                <div class="mask" v-show="selectIndex == index" :data-index="index" data-type="widget">
                    <div class="widget-btns">
                        <el-tooltip effect="dark" :content="hidden ? '显示' : '隐藏'" placement="right">
                            <div
                                class="widget-btns__item"
                                @click.stop="onHidden"
                                :class="{ disable: !enabledBtn('hidden') }"
                            >
                                <i class="iconfont" :class="[hidden ? 'icon_show' : 'icon_hide']"></i>
                            </div>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="删除组件" placement="right">
                            <div
                                class="widget-btns__item"
                                @click.stop="onDelete"
                                :class="{ disable: !enabledBtn('delete') }"
                            >
                                <i class="el-icon-delete"></i>
                            </div>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="复制组件" placement="right">
                            <div
                                class="widget-btns__item"
                                :class="{ disable: !enabledBtn('copy') }"
                                @click.stop="onCopy"
                            >
                                <i class="el-icon-document-copy"></i>
                            </div>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="上移组件" placement="right">
                            <div class="widget-btns__item" :class="{ disable: !isUp }" @click.stop="onMove(-1)">
                                <i class="el-icon-arrow-up"></i>
                            </div>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="下移组件" placement="right">
                            <div class="widget-btns__item" :class="{ disable: !isDown }" @click.stop="onMove(1)">
                                <i class="el-icon-arrow-down"></i>
                            </div>
                        </el-tooltip>
                    </div>
                </div>
            </div>

            <!-- 组件内容 -->
            <div class="widget-content">
                <slot></slot>
            </div>
            <!-- 隐藏层 -->
            <div class="widget-hidden white flex row-center" v-if="hidden">已隐藏</div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component({})
export default class Widget extends Vue {
    /** S props **/

    @Prop() index!: number
    @Prop({ default: () => ['hidden', 'delete', 'copy', 'moveup', 'movedown'] })
    operate!: string[]
    /** E props **/

    /** S computed **/
    get enabledBtn() {
        return (name: string) => this.operate.includes(name)
    }

    get isUp() {
        return this.enabledBtn('moveup') && (this.index === 0 ? false : true) && !this.forbid(-1)
    }

    get isDown() {
        return (
            this.enabledBtn('movedown') && (this.index === this.pagesData.length - 1 ? false : true) && !this.forbid(1)
        )
    }

    get selectIndex() {
        return this.$store.state.decorate.selectIndex
    }

    set selectIndex(val) {
        this.$store.commit('setSelectIndex', val)
    }

    get pagesData() {
        return this.$store.state.decorate.pagesData
    }

    set pagesData(val) {
        this.$store.commit('setPagesData', val)
    }

    get hidden() {
        const { pagesData, index } = this
        return !pagesData[index].show
    }
    /** E computed **/

    /** S methods **/
    forbid(index: number) {
        const widget = this.pagesData[this.selectIndex + index]
        return widget && widget.forbid
    }
    /**
     * @description 设置选中索引
     */
    setSelect() {
        this.selectIndex = this.index
    }

    /**
     * @description 隐藏组件
     */
    onHidden() {
        if (!this.enabledBtn('hidden')) {
            return
        }
        const { pagesData, selectIndex } = this
        pagesData[selectIndex].show = pagesData[selectIndex].show ? 0 : 1
    }
    /**
     * @description 删除当前组件
     */
    onDelete() {
        if (!this.enabledBtn('delete')) {
            return
        }
        this.pagesData.splice(this.index, 1)
        if (this.pagesData.length == this.selectIndex) {
            this.selectIndex--
        }
    }

    /**
     * @description 复制组件
     */
    onCopy() {
        if (!this.enabledBtn('copy')) {
            return
        }
        const { index } = this
        const item = JSON.parse(JSON.stringify(this.pagesData[index]))
        this.pagesData.splice(index, 0, item)
    }
    /**
     * @description 移动组件
     */
    onMove(num: number) {
        const { index, isUp, isDown } = this
        if ((num < 0 && !isUp) || (num > 0 && !isDown)) {
            return
        }
        this.pagesData[index] = this.pagesData.splice(index + num, 1, this.pagesData[index]).pop()
        this.selectIndex = index + num
    }

    /** E methods **/
}
</script>

<style lang="scss" scoped>
.widget-package {
    cursor: move;
    // &.forbid {
    //     cursor: pointer;
    // }
    .widget-suspension {
        position: relative;
        z-index: 99;
        .widget-hidden {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 9999;
        }
    }

    .widget-mask {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 101;
        .mask {
            left: -2px;
            top: -2px;
            z-index: 100;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            border: 2px solid $--color-primary;
            box-shadow: 0px 0px 5px 0px $--color-primary-light-2;
            overflow: hidden;
        }
    }

    .widget-btns {
        position: absolute;
        right: -50px;
        top: 0;
        width: 36px;
        background: $--color-primary;
        border-radius: 4px;
        z-index: 999;
        &__item {
            width: 36px;
            height: 36px;
            line-height: 45px;
            text-align: center;
            cursor: pointer;
            &.disable {
                i {
                    color: $--color-primary-light-2;
                    cursor: not-allowed;
                }
            }
            i {
                font-weight: 500;
                font-size: 20px;
                color: #ffffff;
            }
        }
    }
    .widget-content {
        // overflow: hidden;
    }
}
</style>
