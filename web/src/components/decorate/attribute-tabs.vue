<template>
    <div class="attribute-tabs">
        <div class="flex attribute-header">
            <div class="attribute-title lg weight-500 flex-1">{{ title }}</div>
            <div class="tabs-header flex" v-if="!$slots.default">
                <div class="tabs-header__item" :class="{ active: active == 'content' }" @click="active = 'content'">
                    <span>内容</span>
                </div>
                <div class="tabs-header__item" :class="{ active: active == 'styles' }" @click="active = 'styles'">
                    <span>样式</span>
                </div>
            </div>
        </div>
        <div class="attribute-content" v-if="$slots.default">
            <slot></slot>
        </div>
        <div class="tabs" v-else>
            <div class="tabs-content">
                <div v-show="active == 'content'">
                    <slot name="content"></slot>
                </div>
                <div v-show="active == 'styles'">
                    <slot name="styles"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component({
    components: {}
})
export default class AttributeTabs extends Vue {
    @Prop() title!: string
    active = 'content'
}
</script>

<style lang="scss" scoped>
.attribute-tabs {
    .attribute-header {
        padding: 14px 20px;
        border-bottom: $--border-base;
        .tabs-header {
            height: 28px;
            line-height: 28px;
            background: #f5f5f5;
            border-radius: 14px;
            &__item {
                flex: 1;
                text-align: center;
                cursor: pointer;
                padding: 0 28px;
                &.active {
                    border-radius: 14px;
                    background-color: $--color-primary;
                    color: #fff;
                }
            }
        }
    }
}
</style>
