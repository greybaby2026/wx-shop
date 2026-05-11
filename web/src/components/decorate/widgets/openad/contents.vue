<template>
    <div class="openad">
        <el-image :src="$getImageUri(content.image)">
            <div slot="error" class="image-error muted flex row-center">
                <i class="el-icon-picture font-size-40"></i>
            </div>
        </el-image>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
@Component
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
}
</script>

<style lang="scss" scoped>
.openad {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
