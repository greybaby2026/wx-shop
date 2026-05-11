<template>
    <div class="style-chose">
        <div class="chose-list flex flex-wrap">
            <div
                class="chose-item"
                v-for="(item, index) in data"
                :key="index"
                :class="{ active: select == item.value }"
                @click="select = item.value"
            >
                <div class="chose-content">
                    <span :style="{ 'padding-left': item.icon ? '8px' : '0' }">{{ item.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class StyleChose extends Vue {
    /** S props **/

    @Prop({ default: () => [] }) data!: any[]
    @Prop() value!: number | string
    /** E props **/

    /** S computed **/

    get select() {
        return this.value
    }

    set select(val) {
        this.$emit('input', val)
    }

    /** E computed **/
}
</script>

<style lang="scss" scoped>
.style-chose {
    .chose-list {
        .chose-item {
            height: 32px;
            line-height: 32px;
            padding: 0 20px;
            border: $--border-base;
            margin-right: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            cursor: pointer;
            &.active {
                border-color: $--color-primary;
                color: $--color-primary;
            }
        }
    }
}
</style>
