<template>
    <div class="ad-item" @click.stop="goPage(item)">
        <el-image :src="$getImageUri(item.url)" style="width: 100%; height: 100%" v-if="ossDomain && item.url"></el-image>
    </div>
</template>

<script>
    import {
        paramsToStr
    } from "~/utils/tools";
    import { mapGetters } from 'vuex'
    export default {
        components: {},
        props: {
            item: {
                type: Object,
                default: () => ({}),
            },
        },
        
        computed: {
            ...mapGetters(['ossDomain']),
        },
        
        methods: {
            goPage(item) {
                let {
                    type,
                    path,
                    params
                } = item.link;
                switch (type) {
                    case 'goods':   // 普通商品
                        path += `?id=${params.id}`;
                        this.$router.push({
                            path: path,
                        });
                        break;
                    case 'seckill': // 秒杀商品
                        path += `?id=${params.id}`;
                        this.$router.push({
                            path: path,
                        });
                        break;
                    case 'category': // 分类页面
                        path += `?id=${params.id}`;
                        this.$router.push({
                            path: path,
                        });
                        break;
                    // case 3:
                    //     window.open(item.path);
                    //     break;
                    default:
                        this.$router.push({
                            path: path,
                        });
                }
            },
        },
    };
</script>

<style lang="scss" scoped>
    .ad-item {
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
</style>
