<template>
    <div class="adv">
        <div class="adv-content flex">
            <div
                class="adv-image"
                v-for="(item, index) in content.data"
                :key="index"
            >
                <app-link
                    :to="{
                        path: item.link.path,
                        query: item.link.params,
                    }"
                    target="_blank"
                    >
                    <el-image
                        style="width: 100%; height: 100%"
                        :src="$getImageUri(item.url)"
                        fit="cover"
                    >
                    </el-image>
                </app-link>
            </div>
        </div>
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
.adv {
    margin-top: 16px;
    .adv-content {
        .adv-image {
            flex: 1;
            height: 180px;
            & ~ .adv-image {
                margin-left: 10px;
            }
        }
    }
}
</style>