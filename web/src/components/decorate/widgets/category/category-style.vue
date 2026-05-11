<template>
    <div>
        <ls-dialog title="选择分类样式" width="1100px" top="15vh" @confirm="onConfirm">
            <div slot="trigger">
                <slot name="trigger"></slot>
            </div>
            <div class="category-select">
                <div>
                    <el-radio-group v-model="category">
                        <el-radio :label="1">一级分类</el-radio>
                        <el-radio :label="2">二级分类</el-radio>
                        <el-radio :label="3">三级分类</el-radio>
                    </el-radio-group>
                </div>
                <div>
                    <template v-if="category == 1">
                        <div class="img-wrap" :class="{ active: currentStyle == 1 }" @click="selectStyle(1)">
                            <img src="@/assets/images/sort_one.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 2 }" @click="selectStyle(2)">
                            <img src="@/assets/images/sort_one2.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 3 }" @click="selectStyle(3)">
                            <img src="@/assets/images/sort_one3.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 4 }" @click="selectStyle(4)">
                            <img src="@/assets/images/sort_one4.jpg" />
                        </div>
                    </template>
                    <template v-if="category == 2">
                        <div class="img-wrap" :class="{ active: currentStyle == 5 }" @click="selectStyle(5)">
                            <img src="@/assets/images/sort_two.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 6 }" @click="selectStyle(6)">
                            <img src="@/assets/images/sort_two2.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 9 }" @click="selectStyle(9)">
                            <img src="@/assets/images/sort_two3.jpg" />
                        </div>
                    </template>
                    <template v-if="category == 3">
                        <div class="img-wrap" :class="{ active: currentStyle == 7 }" @click="selectStyle(7)">
                            <img src="@/assets/images/sort_three2.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 8 }" @click="selectStyle(8)">
                            <img src="@/assets/images/sort_three.jpg" />
                        </div>
                        <div class="img-wrap" :class="{ active: currentStyle == 10 }" @click="selectStyle(10)">
                            <img src="@/assets/images/sort_three3.jpg" />
                        </div>
                    </template>
                </div>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class CategorySelect extends Vue {
    /** S data **/
    @Prop() value!: number
    category = 1
    currentStyle = 0
    /** E data **/

    /** S watch **/
    @Watch('value', { immediate: true })
    valueChange(val: number) {
        switch (val) {
            case 1:
            case 2:
            case 3:
            case 4:
                this.category = 1
                break
            case 5:
            case 6:
                this.category = 2
                break
            case 7:
            case 8:
                this.category = 3
                break
        }
        this.currentStyle = val
    }

    /** E watch **/

    /** S methods **/
    selectStyle(style: number) {
        this.currentStyle = style
    }
    onConfirm() {
        this.$emit('input', this.currentStyle)
    }
    /** E methods **/
}
</script>
<style lang="scss" scoped>
.category-select {
    padding: 20px;
    height: 500px;
    .img-wrap {
        display: inline-block;
        padding: 15px 20px;
        margin-top: 40px;
        cursor: pointer;
        border: 1px solid transparent;
        &.active {
            border-color: $--color-primary;
        }
    }
    img {
        width: 210px;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.05);
    }
}
</style>
