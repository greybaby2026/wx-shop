<template>
    <div class="attribute-item">
        <div v-if="title" class="title flex nr">
            {{ title }}
            <span class="muted xs flex-1 m-l-8">{{ desc }}</span>
            <slot name="right"></slot>
        </div>
        <div class="content">
            <slot></slot>
        </div>
        <div class="footer"></div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class AttributeItem extends Vue {
    /** S props **/

    @Prop() title!: string

    @Prop() desc!: string
    /** E props **/

    /** S computed **/

    /** E computed **/
}
</script>

<style lang="scss" scoped>
.attribute-item {
    .title {
        line-height: 1.3;
        padding: 20px 20px 0;
    }
    .content {
        padding: 20px 20px 0;
    }
    .footer {
        height: 6px;
        background: $--background-color-base;
    }
    &:last-of-type {
        .footer {
            height: 0;
        }
    }
}
</style>
