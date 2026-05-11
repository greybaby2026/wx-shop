<template>
    <div class="category" @mouseleave="leaveCate">
        <el-button type="primary" class="title">
            <div class="flex lg">
                <i class="el-icon-s-fold"></i>
                <div class="m-l-8">全部商品分类</div>
            </div>
        </el-button>
        <div class="category-con bg-white" v-show="category.length">
            <ul class="category-one">
                <li
                    v-for="(item, index) in category"
                    :key="index"
                    :class="{ active: index === selectIndex }"
                    @mouseenter="enterCate(index)"
                >
                    <div class="flex row-between">
                        <span class="line-1">{{ item.name }}</span>
                        <i v-if="item.sons" class="el-icon-arrow-right"></i>
                    </div>
                </li>

                <div class="category-float bg-white" v-show="showCateFloat && cateTwo.length">
                    <div class="float-con">
                        <div class="m-t-16" v-for="(item, index) in cateTwo" :key="index">
                            <div class="category-two weight-500 m-b-15">
                                <span>{{ item.name }}</span>
                            </div>
                            <div class="category-three flex flex-wrap">
                                <div class="item" v-for="(titem, idx) in item.sons" :key="idx">
                                    <div class="flex">
                                        <el-image
                                            style="width: 48px; height: 48px"
                                            :src="titem.image"
                                            fit="contain"
                                        ></el-image>
                                        <span class="m-l-8">{{ titem.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { apiCategoryLists } from '@/api/goods'
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component({})
export default class Category extends Vue {
    /** S data **/
    category: any = []
    cateTwo = []
    showCateFloat = false
    selectIndex = -1
    /** E data **/

    /** S methods **/
    getCategoryLists() {
        apiCategoryLists({
            page_type: 1
        }).then(res => {
            this.category = res.lists
        })
    }

    enterCate(index: number) {
        this.cateTwo = this.category[index].sons || []
        this.showCateFloat = true
        this.selectIndex = index
    }
    leaveCate() {
        this.selectIndex = -1
        this.showCateFloat = false
    }
    /** E methods **/

    created() {
        this.getCategoryLists()
    }
}
</script>

<style lang="scss" scoped>
.category {
    position: relative;
    .title {
        padding: 14px 20px;
        flex: none;
        width: 160px;
        box-sizing: border-box;
        border-radius: 0;
        background-color: #ff2c3c;
        border-color: #ff2c3c;
        text-align: center;
    }
    .category-con {
        position: absolute;
        width: 100%;
        z-index: 99999;
        height: 440px;
        padding: 10px 0;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);

        .category-one {
            height: 100%;
            overflow-y: auto;
            &::-webkit-scrollbar {
                display: none; /*隐藏滚动条*/
            }
            li {
                & > div {
                    height: 42px;
                    padding: 0 20px;
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
                div:hover {
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
