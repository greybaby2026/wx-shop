<template>
    <div class="mouse-cover flex row-center col-center">
        <span>释放鼠标将组建添加到此处</span>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class SearchWidget extends Vue {}
</script>

<style lang="scss" scoped>
.mouse-cover {
    height: 40px;
    margin: 2px 0;
    border: 1px dashed $--color-primary;
    background: rgba($--color-primary, 0.05);
    font-size: 14px;
    color: $--color-primary;
}
</style>
