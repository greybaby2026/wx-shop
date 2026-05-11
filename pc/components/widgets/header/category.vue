<template>
    <div class="category" @mouseenter="showCate = true" @mouseleave="leaveCate">
        <nuxt-link to="/category" class="title bg-primary lg white flex">
            <i class="el-icon-s-fold"></i>
            <div class="m-l-8">全部商品分类</div>
        </nuxt-link>
        <div
            class="category-con bg-white"
            v-show="category.length && showCate"
        >
            <ul class="category-one">
                <li
                    v-for="(item, index) in category"
                    :key="index"
                    :class="{ active: index === selectIndex }"
                    @mouseenter="enterCate(index)"
                >
                    <nuxt-link class="flex row-between" :to="`/category?id=${item.id}`">
                        <span class="line-1">{{ item.name }}</span>
                        <i class="el-icon-arrow-right" v-if="item.sons"></i>
                    </nuxt-link>
                </li>
                <div class="category-float bg-white" v-show="showCateFloat && cateTwo.length">
                    <div class="float-con">
                        <div
                            class="m-t-16"
                            v-for="(item, index) in cateTwo"
                            :key="index"
                        >
                            <div class="category-two bold m-b-8">
                                <nuxt-link :to="`/category?id=${item.id}`">{{
                                    item.name
                                }}</nuxt-link>
                            </div>
                            <div class="category-three flex flex-wrap">
                                <div
                                    class="item"
                                    v-for="(titem, idx) in item.sons"
                                    :key="idx"
                                >
                                    <nuxt-link
                                        :to="`/category?id=${titem.id}`"
                                        class="flex"
                                    >
                                        <el-image
                                            style="width: 48px; height: 48px"
                                            :src="titem.image"
                                            fit="contain"
                                        >
                                        </el-image>
                                        <span class="m-l-8">{{
                                            titem.name
                                        }}</span>
                                    </nuxt-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
    data() {
        return {
            cateTwo: [],
            showCateFloat: false,
            showCate: false,
            selectIndex: '',
        }
    },
    mounted() {
        console.log(this.category)
    },
    methods: {
        enterCate(index) {
            this.cateTwo = this.category[index].sons || []
            this.showCateFloat = true
            this.selectIndex = index
        },
        leaveCate() {
            this.$route.path != '/' && (this.showCate = false)
            this.selectIndex = ''
            this.showCateFloat = false
        },
    },
    watch: {
        $route: {
            immediate: true,
            handler: function (val) {
                this.showCate = val.path == '/' ? true : false
            },
        },
    },
    computed: {
        ...mapState(['category']),
    },
}
</script>

<style lang="scss" scoped>
.category {
    position: relative;
    .title {
        padding: 12px 20px;
        height: 46px;
        flex: none;
        width: 160px;
        box-sizing: border-box;
    }
    .category-con {
        position: absolute;
        width: 100%;
        z-index: 999;
        height: 440px;
        padding: 10px 0;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        .category-one {
            height: 100%;
            overflow: hidden;
            li {
                & > a {
                    height: 42px;
                    padding: 0 20px;
                }
                &.active {
                    background-color: #ffeeef;
                    a {
                        color: #ff2c3c;
                    }
                }
            }
            .category-float {
                box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
                position: absolute;
                left: 160px;
                top: 0;
                width: 880px;
                height: 440px;
                padding: 0 24px;
                overflow-y: auto;
                &::-webkit-scrollbar {
                    display: none; /*隐藏滚动条*/
                }
                a:hover {
                    color: #ff2c3c;
                }
                .float-con {
                    .category-three {
                        border-bottom: 1px dashed $--border-color-base;
                        .item {
                            width: 20%;
                            margin-bottom: 20px;
                            padding-right: 10px;
                        }
                    }
                }
            }
        }
    }
}
</style>