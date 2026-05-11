<template>
    <ls-dialog
        ref="dialog"
        class="link-select"
        title="热区选择器"
        width="1250px"
        top="10vh"
        :disabled="imgUrl == ''"
        @confirm="onConfirm"
        :clickModalClose="false"
        :async="true"
    >
        <div slot="trigger">
            <el-button size="small" class="add-banner" :disabled="imgUrl == ''">+ 添加热区</el-button>
            <div class="area-tip" v-if="imgUrl == ''">请先选择图片</div>
            <div class="area-tip" v-else>当前创建了{{ areaLists.length }}/20个热区</div>
        </div>
        <el-scrollbar class="area-scrollbar ls-scrollbar">
            <div class="container">
                <div class="image">
                    <img :src="imgSrc" class="image" />
                    <VueDragResize
                        v-for="(item, index) in areaLists"
                        :isActive="activeIndex == index"
                        :w="item.areaWidth"
                        :h="item.areaHeight"
                        class="area-select"
                        parentLimitation
                        :minw="50"
                        :minh="50"
                        :x="item.areaX"
                        :y="item.areaY"
                        @dragging="handleDragging"
                        @resizing="handleResizing"
                        @clicked="handleClick(index)"
                        :key="index"
                    >
                        <div class="text-tip">
                            <div>
                                {{ item.url.name }}
                                <!-- {{ item.url.params.name ? item.url.params.name : item.url.name }} -->
                            </div>
                        </div>
                    </VueDragResize>
                </div>

                <div class="area">
                    <el-scrollbar class="ls-scrollbar" style="height: 310px">
                        <div
                            class="link"
                            :class="{ 'active-area': activeIndex == index }"
                            @click="handleAreaSelct(index)"
                            v-for="(item, index) in areaLists"
                            :key="index"
                        >
                            <link-select v-model="item.url" />
                            <i class="el-icon-close del" @click="handleDel(index)"></i>
                        </div>
                    </el-scrollbar>
                    <div style="margin: 10px 20px">当前创建了{{ areaLists.length }}/20个热区</div>
                    <div class="btn">
                        <el-button size="small" @click="handleAddarea">添加热区</el-button>
                    </div>
                </div>
            </div>
        </el-scrollbar>
    </ls-dialog>
</template>
<script lang="ts">
import { Component, Emit, Prop, Vue, Watch } from 'vue-property-decorator'
import LinkSelect from '@/components/link-select/index.vue'
import LsDialog from '@/components/ls-dialog.vue'
import VueDragResize from 'vue-drag-resize'
import { Message } from 'element-ui'
import { deepClone } from '@/utils/util'

@Component({
    components: {
        LsDialog,
        LinkSelect,
        VueDragResize
    }
})
export default class HotareaSelect extends Vue {
    $refs!: { dialog: any }

    @Prop({ default: '' }) imgSrc!: string
    @Prop({ default: '' }) imgUrl!: string
    @Prop({ default: () => [] }) value!: any[]

    // area = {
    //     areaX: 0,
    //     areaY: 0,
    //     areaWidth: 100,
    //     areaHeight: 100,
    //     url: ''
    // }
    areaLists: any[] = []
    @Watch('value', { immediate: true, deep: true })
    valueChange(val: any) {
        this.areaLists = deepClone(this.value)
    }
    // get areaLists() {
    //     this.areaList = deepClone(this.value)
    //     return this.value
    // }
    activeIndex = -1
    handleDragging(newRect: any) {
        const activeIndex = this.activeIndex
        console.log(newRect)
        this.areaLists[activeIndex].areaX = newRect.left
        this.areaLists[activeIndex].areaY = newRect.top
    }
    handleResizing($event: any) {
        const activeIndex = this.activeIndex
        this.areaLists[activeIndex].areaHeight = $event.height
        this.areaLists[activeIndex].areaWidth = $event.width
    }
    handleAddarea() {
        if (this.areaLists.length >= 20) {
            return Message({ type: 'error', message: '最多允许创建20个热区' })
        }
        this.areaLists.push({
            areaX: 0,
            areaY: 0,
            areaWidth: 100,
            areaHeight: 100,
            url: ''
        })
    }
    handleClick(index: any) {
        this.activeIndex = index
        console.log(this.areaLists)
    }
    handleAreaSelct(index: any) {
        this.activeIndex = index
    }
    handleDel(index: any) {
        this.areaLists.splice(index, 1)
    }

    onConfirm() {
        let check = false
        check = this.areaLists.some((i: any) => {
            return i.url == ''
        })
        if (check) {
            return Message({ type: 'error', message: '请完善热区信息' })
        }
        this.$emit('input', this.areaLists)
        this.activeIndex = -1
        this.$refs.dialog.close()
    }
}
</script>
<style lang="scss">
.area-scrollbar {
    height: 600px;
    background-color: #eff3f3;
}
.add-banner {
    width: 100%;
    margin-top: 20px;
}
.container {
    display: flex;
    justify-content: center;
    position: relative;
}
.image {
    width: 375px;
    position: relative;
}
.area {
    position: absolute;
    top: 10px;
    right: 10px;
    /* background-color: skyblue; */
    width: 350px;
    /* height: 150px; */
    padding: 10px;
    background-color: white;
    border-radius: 8px;
}

.link {
    margin: 10px 20px;
    position: relative;
    padding: 10px;
    border-radius: 5px;

    background: #f9f9f9;
    &:hover .del {
        display: block;
    }
}
.del {
    display: none;
    position: absolute;
    top: -8px;
    right: -8px;
    line-height: 16px;
    width: 16px;
    height: 16px;
    background-color: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    text-align: center;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
}
.btn {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 10px;
}
.area-select {
    cursor: move;
    background-color: #4073fa;
    opacity: 0.9;
}
.active-area {
    border: 1px solid $--color-primary;
}
.area-tip {
    margin: 20px 0;
}
.text-tip {
    height: 100%;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}
</style>
