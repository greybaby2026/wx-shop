<template>
    <div class="banner">
        <el-carousel height="440px" autoplay arrow="always">
            <el-carousel-item
                v-for="(item, index) in content.data"
                :key="index"
                class="swiper-item"
            >
                <app-link
                    :to="{
                        path: item.link.path,
                        query: item.link.params,
                    }"
                >
                    <el-image
                        :src="$getImageUri(item.url)"
                        fit="cover"
                        style="width: 100%; height: 100%"
                    ></el-image>
                </app-link>
            </el-carousel-item>
        </el-carousel>
    </div>
</template>

<script>
export default {
    props: {
        content: {
            type: [Object, Array],
        },
        styles: {
            type: [Object, Array],
        },
    },
    computed: {},
}
</script>

<style lang="scss" scoped>
.banner {
    height: 440px;
    padding-left: 160px;
}
</style>