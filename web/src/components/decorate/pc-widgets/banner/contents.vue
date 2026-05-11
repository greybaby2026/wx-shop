<template>
    <div class="banner">
        <el-carousel height="440px" autoplay arrow="always">
            <el-carousel-item v-for="(item, index) in content.data" :key="index" class="swiper-item">
                <el-image :src="$getImageUri(item.url)" fit="cover" style="width: 100%; height: 100%"></el-image>
            </el-carousel-item>
        </el-carousel>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
}
</script>

<style lang="scss" scoped>
.banner {
    height: 440px;
    padding-left: 160px;
}
</style>
