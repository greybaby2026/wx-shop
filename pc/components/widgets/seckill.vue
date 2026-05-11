<template>
    <div class="seckill bg-white" v-if="goods.length">
        <div class="seckill-header flex row-between">
            <div class="title flex">
                <i class="el-icon-alarm-clock icon-seckill font-size-24"></i>
                <span class="m-l-10">{{ content.title }}</span>
            </div>
            <div class="more lighter" v-if="content.show_more">
                <nuxt-link to="/seckill">
                    更多 <i class="el-icon-arrow-right"></i>
                </nuxt-link>
            </div>
        </div>
        <el-carousel
            :interval="3000"
            arrow="never"
            height="320px"
            indicator-position="outside"
        >
            <el-carousel-item v-for="(item, index) in swiperSize" :key="index">
                <div class="goods-lists">
                    <nuxt-link
                        class="goods-item"
                        :to="`/goods_details_seckill?id=${item.id}`"
                        v-for="(item, index) in getSwiperList(index)"
                        :key="index"
                    >
                        <div class="goods-image">
                            <el-image :src="item.image" fit="cover"> </el-image>
                        </div>
                        <div class="goods-name line-2">
                            {{ item.name }}
                        </div>
                        <div class="goods-price flex-1 flex col-baseline">
                            <div class="price m-r-5 flex flex-1">
                                <span>秒杀价</span>
                                <price-formate
                                    :price="item.activity_price"
                                    :first-size="18"
                                />
                            </div>
                            <div class="btn">立即抢购</div>
                        </div>
                    </nuxt-link>
                </div>
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
    data() {
        return {
            pageSize: 5,
        }
    },
    computed: {
        goods() {
            return this.content.data
        },
        swiperSize() {
            console.log(Math.ceil(this.goods.length / this.pageSize))
            return Math.ceil(this.goods.length / this.pageSize)
        },
        getSwiperList() {
            return (index) => {
                return this.goods.slice(
                    index * this.pageSize,
                    (index + 1) * this.pageSize
                )
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.seckill {
    margin-top: 16px;
    border-radius: 8px;
    padding: 0 20px;
    .seckill-header {
        height: 66px;
        .icon-seckill {
            color: #ff2c3c;
        }
        .title {
            font-size: 20px;
        }
    }
    .goods-lists {
        display: flex;
        overflow: hidden;
        .goods-item {
            flex: none;
            width: 212px;
            margin-bottom: 24px;
            // box-shadow: 0 0 6px rgb(0 0 0 / 10%);
            border-radius: 4px;
            &:not(:nth-of-type(5n)) {
                margin-right: 20px;
            }
            .goods-image {
                height: 0;
                padding-top: 100%;
                position: relative;
                .el-image {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    top: 0;
                    z-index: 0;
                }
            }
            .goods-name {
                margin-bottom: 10px;
                margin-top: 14px;
                height: 40px;
                line-height: 20px;
            }
            .goods-price {
                color: #ff2c3c;
            }
            .btn {
                background-color: #ff2c3c;
                color: #fff;
                padding: 4px 12px;
                border-radius: 4px;
            }
        }
    }
}
</style>