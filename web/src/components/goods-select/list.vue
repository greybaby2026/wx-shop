<template>
    <div class="list">
        <div class="goods-lists">
            <draggable class="flex flex-wrap" v-model="list" animation="300">
                <div class="goods-item ls-del-wrap" v-for="(item, index) in list" :key="index">
                    <el-image style="width: 100%; height: 100%" fit="cover" :src="item.image"></el-image>
                    <i @click="handleDelete(index)" class="el-icon-close ls-icon-del"></i>
                </div>
            </draggable>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import Draggable from 'vuedraggable'
@Component({
    components: {
        Draggable
    }
})
export default class List extends Vue {
    @Prop({ default: () => [] }) value!: any[]

    get list() {
        return this.value
    }

    set list(val) {
        this.$emit('input', val)
    }
    handleDelete(index: number) {
        this.list.splice(index, 1)
    }
}
</script>

<style scoped lang="scss">
.goods-lists {
    .goods-item {
        cursor: move;
        width: 64px;
        height: 64px;
        margin-right: 10px;
        margin-top: 14px;
    }
}
</style>
