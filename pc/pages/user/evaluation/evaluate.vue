<template>
    <div class="evaluate bg-white">
        <div class="goods">
            <div class="goods-hd lighter flex">
                <div class="info flex flex-1">商品信息</div>
                <div class="price flex row-center">单价</div>
                <div class="num flex row-center">数量</div>
                <div class="total flex row-center">合计</div>
            </div>
            <div class="goods-con">
                <div class="goods-item flex">
                    <div class="info flex flex-1">
                        <img
                            class="goods-img"
                            :src="goodsInfo.goods_image"
                            alt=""
                        />
                        <div class="goods-info flex-1">
                            <div class="goods-name line-2">
                                {{ goodsInfo.goods_name }}
                            </div>
                            <div class="sm lighter m-t-8">
                                {{ goodsInfo.spec_value_str }}
                            </div>
                        </div>
                    </div>
                    <div class="price flex row-center">
                        <price-formate
                            :price="goodsInfo.goods_price"
                            weight="400"
                        />
                    </div>
                    <div class="num flex row-center">
                        {{ goodsInfo.goods_num }}
                    </div>
                    <div class="total flex row-center">
                        <price-formate
                            :price="goodsInfo.total_price"
                            weight="400"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="evaluate-con">
            <div class="goods-rate">
                <div class="flex item">
                    <div class="name">商品评价</div>
                    <el-rate
                        v-model="goodsRate"
                        show-text
                        text-color="#FF9E2C"
                        :texts="goodsTexts"
                    ></el-rate>
                </div>
                <div class="flex item">
                    <div class="name">描述相符</div>
                    <el-rate v-model="descRate"></el-rate>
                </div>
                <div class="flex item">
                    <div class="name">服务态度</div>
                    <el-rate v-model="serverRate"></el-rate>
                </div>
                <div class="flex item">
                    <div class="name">配送服务</div>
                    <el-rate v-model="deliveryRate"></el-rate>
                </div>
            </div>
            <div class="evaluate-input flex">
                <div class="name">商品评价</div>
                <div>
                    <el-input
                        type="textarea"
                        placeholder="收到商品您有什么想法或者反馈，用几个字来评价下商品吧～"
                        v-model="comment"
                        maxlength="150"
                        :rows="6"
                        show-word-limit
                        resize="none"
                    >
                    </el-input>
                    <div class="upload m-t-16">
                        <upload :limit="9" @success="onSuccess" />
                        <div class="muted m-t-8">
                            最多可上传9张图片，支持jpg、png格式，图片大小1M以内
                        </div>
                    </div>
                    <div
                        class="
                            submit-btn
                            white
                            bg-primary
                            m-t-16
                            flex
                            row-center
                        "
                        @click="onSubmit"
                    >
                        提交评价
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Upload from '~/components/public/upload'
import headerMixins from '@/mixins/header'
export default {
    mixins: [headerMixins],
    async asyncData({ $get, query }) {
        const id = query.id
        let { data } = await $get('goods_comment/commentGoodsInfo', {
            params: { order_goods_id: id },
        })
        return {
            goodsInfo: data,
            id,
        }
    },
    layout: 'user-layout',
    components: {
        Upload,
    },
    data() {
        return {
            goodsInfo: {},
            goodsRate: 0,
            descRate: 0,
            serverRate: 0,
            deliveryRate: 0,
            comment: '',
            fileList: [],
            goodsTexts: ['差评', '差评', '中评', '好评', '好评'],
        }
    },
    methods: {
        onSuccess(res) {
            this.fileList = res.map((item) => item.response.data)
        },
        onSubmit() {
            let {
                goodsRate,
                fileList,
                comment,
                deliveryRate,
                descRate,
                serverRate,
            } = this
            let image = fileList.map((item) => item.url)
            if (!goodsRate)
                return this.$message({
                    message: '请对商品进行评分',
                    type: 'error',
                })
            if (!descRate)
                return this.$message({
                    message: '请对描述相符进行评分',
                    type: 'error',
                })
            if (!serverRate)
                return this.$message({
                    message: '请对服务态度进行评分',
                    type: 'error',
                })
            if (!deliveryRate)
                return this.$message({
                    message: '请对配送服务进行评分',
                    type: 'error',
                })
            this.$post('goods_comment/add', {
                order_goods_id: parseInt(this.id),
                goods_comment: goodsRate,
                service_comment: serverRate,
                express_comment: deliveryRate,
                description_comment: descRate,
                comment,
                image,
            }).then((res) => {
                if (res.code == 1) {
                    this.$message({
                        message: '评价成功',
                        type: 'success',
                    })
                    setTimeout(() => {
                        this.$router.replace({
                            path: '/user/evaluation',
                            query: {
                                type: 1,
                            },
                        })
                    }, 1500)
                }
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.evaluate {
    padding: 10px 10px;
    .goods {
        .goods-hd,
        .goods-con {
            padding: 10px 20px;
            border-bottom: 1px solid #e5e5e5;
            .goods-item {
                padding: 10px 0;
            }
        }
        .info {
            .goods-img {
                width: 72px;
                height: 72px;
                margin-right: 10px;
            }
        }
        .price,
        .num,
        .total {
            width: 150px;
        }
    }
    .evaluate-con {
        padding: 20px;
        .goods-rate {
            .item {
                margin-bottom: 18px;
            }
        }
        .name {
            margin-right: 24px;
            flex: none;
        }
        .evaluate-input {
            align-items: flex-start;
            .el-textarea {
                width: 630px;
            }
            .submit-btn {
                width: 100px;
                height: 32px;
                cursor: pointer;
            }
        }
    }
}
</style>
