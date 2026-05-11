<template>
    <div class="seckill bg-white">
        <div class="seckill-header flex row-between">
            <div class="title flex">
                <i class="el-icon-alarm-clock icon-seckill font-size-24"></i>
                <span class="m-l-10">{{ content.title }}</span>
            </div>
            <div class="more lighter" v-if="content.show_more">更多 <i class="el-icon-arrow-right"></i></div>
        </div>
        <div class="goods-lists">
            <div class="goods-item" v-for="(item, index) in goods" :key="index">
                <div class="goods-image">
                    <el-image :src="item.image" fit="cover">
                        <img slot="error" class="image-error" src="@/assets/images/goods_image.png" alt="" />
                    </el-image>
                </div>
                <div class="goods-name line-2">
                    {{ item.name || '这里是商品标题' }}
                </div>
                <div class="goods-price flex-1 flex col-baseline">
                    <span class="">秒杀价</span>
                    <div class="price weight-500 m-r-5 xl flex-1">
                        <span class="xs">￥</span>{{ parseFloat(item.min_activity_price) || '0' }}
                    </div>
                    <div class="btn">立即抢购</div>
                </div>
            </div>
        </div>
        <div class="el-carousel__indicators--outside">
            <li class="el-carousel__indicator el-carousel__indicator--horizontal is-active">
                <button style="background-color: #ff2c3c" class="el-carousel__button">
                    <!---->
                </button>
            </li>
        </div>
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
    get goods() {
        const { data, data_type, num } = this.content
        if (data_type == 1) {
            return [...Array(num).keys()].map(() => ({}))
        }
        return data.length ? data : [{}, {}, {}, {}, {}]
    }
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
