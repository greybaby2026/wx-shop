<template>
    <div class="goods-poster">
        <div class="ls-card">
            <el-alert
                title="温馨提示：在商品分享时的海报样式时为商家提供丰富的海报模板，可自定义设置，也可使用本商城提供的模板。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="ls-card m-t-16 flex col-top" v-loading="loading">
            <el-form
                style="max-width: 900px"
                class="flex-1"
                ref="form"
                :model="formData"
                :rules="formRules"
                label-width="80px"
                size="small"
            >
                <div class="nr weight-500 m-b-16">基础设置</div>
                <el-form-item label="海报样式" props="id">
                    <style-select title="选择海报样式" v-model="formData.id" :data="styleData">
                        <el-button slot="trigger" size="mini" type="text">
                            {{ getSelectStyleText }}
                        </el-button>
                    </style-select>
                </el-form-item>
                <el-form-item label="背景图片">
                    <el-radio-group v-model="formData.background_type" v-if="formData.id != 3">
                        <el-radio :label="1">系统默认</el-radio>
                        <el-radio :label="2">自定义</el-radio>
                    </el-radio-group>
                    <el-radio-group v-model="formData.background_type" v-if="formData.id == 3">
                        <!-- TODO处理其他样式为自定义背景跳转到样式三默认不勾选 -->
                        <el-radio :label="1" v-show="formData.background_type == 1">商品分享海报</el-radio>
                        <el-radio :label="2" v-show="formData.background_type == 2">商品分享海报</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item v-if="formData.background_type == 2 && formData.id != 3">
                    <material-select v-model="formData.background_url"> </material-select>
                    <div class="muted">建议尺寸：750*1280</div>
                </el-form-item>
                <el-form-item label="显示内容">
                    <div>
                        <el-checkbox
                            v-model="formData.show.user_avtar"
                            :true-label="1"
                            :false-label="0"
                            label="用户头像"
                        />
                        <el-checkbox
                            v-model="formData.show.user_name"
                            :true-label="1"
                            :false-label="0"
                            label="用户昵称"
                        />
                        <el-checkbox
                            v-model="formData.show.goods_img"
                            :true-label="1"
                            :false-label="0"
                            label="商品图"
                            v-if="formData.id != 3"
                        />
                        <el-checkbox
                            v-model="formData.show.goods_name"
                            :true-label="1"
                            :false-label="0"
                            label="商品名称"
                            v-if="formData.id != 3"
                        />
                    </div>
                    <div>
                        <el-checkbox
                            v-model="formData.show.goods_sale_price"
                            :true-label="1"
                            :false-label="0"
                            label="商品价格"
                            v-if="formData.id != 3"
                        />
                        <el-checkbox
                            v-model="formData.show.goods_origin_price"
                            :true-label="1"
                            :false-label="0"
                            label="商品原价"
                            v-if="formData.id != 3"
                        />
                        <el-checkbox v-model="formData.show.qrcode" :true-label="1" :false-label="0" label="二维码" />
                        <el-checkbox
                            v-model="formData.show.qrcode_title"
                            :true-label="1"
                            :false-label="0"
                            label="二维码标题"
                        />
                    </div>
                </el-form-item>
                <el-form-item label="二维码">
                    <el-radio-group v-model="formData.qrcode_align">
                        <el-radio :label="1">靠左</el-radio>
                        <el-radio :label="2">靠右</el-radio>
                    </el-radio-group>
                </el-form-item>
                <div class="border-bottom" style="width: 600px"></div>
                <div class="nr weight-500 m-b-16 m-t-16">样式设置</div>
                <el-form-item label="用户昵称">
                    <color-select style="width: 240px" v-model="formData.style.user_name" reset-color="#333333" />
                </el-form-item>
                <el-form-item label="商品价格" v-if="formData.id != 3">
                    <color-select
                        style="width: 240px"
                        v-model="formData.style.goods_sale_price"
                        reset-color="#FF0610"
                    />
                </el-form-item>
                <el-form-item label="商品原价" v-if="formData.id != 3">
                    <color-select
                        style="width: 240px"
                        v-model="formData.style.goods_origin_price"
                        reset-color="#999999"
                    />
                </el-form-item>
                <el-form-item label="商品名称" v-if="formData.id != 3">
                    <color-select style="width: 240px" v-model="formData.style.goods_name" reset-color="#333333" />
                </el-form-item>
                <el-form-item label="二维码标题">
                    <color-select style="width: 240px" v-model="formData.style.qrcode_title" reset-color="#999999" />
                </el-form-item>
            </el-form>
            <div class="preview flex row-center">
                <div
                    class="poster"
                    :style="{
                        'background-image':
                            formData.background_type == 2
                                ? formData.id == 3
                                    ? `url(${goodsImage})`
                                    : `url(${formData.background_url})`
                                : ''
                    }"
                    :class="{
                        style2: formData.id == 2,
                        style3: formData.id == 3
                    }"
                >
                    <div class="flex user-info">
                        <el-avatar
                            v-if="formData.show.user_avtar"
                            :size="28"
                            :src="require('@/assets/images/avatar.jpg')"
                        ></el-avatar>
                        <span v-if="formData.show.user_name" class="m-l-6" :style="{ color: formData.style.user_name }">
                            来自XXXX的分享
                        </span>
                    </div>
                    <el-image v-if="formData.show.goods_img && formData.id != 3" class="goods-image" fit="cover">
                        <img slot="error" class="image-error" src="@/assets/images/goods_image.png" alt="" />
                    </el-image>
                    <el-image v-if="formData.id == 3" class="goods-image" fit="cover">
                        <img slot="error" class="image-error" src="@/assets/images/goods_image2.png" alt="" />
                    </el-image>
                    <div class="goods-price" :class="{ left: formData.qrcode_align == 1 }">
                        <span
                            v-if="formData.show.goods_sale_price && formData.id != 3"
                            class="inline-flex col-baseline m-r-6"
                            :style="{ color: formData.style.goods_sale_price }"
                        >
                            <span class="xxs">¥</span>
                            <span class="xl">29</span>
                            <span class="xxs">.9</span>
                        </span>
                        <span
                            v-if="formData.show.goods_origin_price && formData.id != 3"
                            class="line-through"
                            :style="{ color: formData.style.goods_origin_price }"
                        >
                            ¥39.00
                        </span>
                    </div>
                    <div
                        v-if="formData.show.goods_name && formData.id != 3"
                        class="line-2 goods-name"
                        :class="{ left: formData.qrcode_align == 1 }"
                        :style="{ color: formData.style.goods_name }"
                    >
                        商品名称商品名称商品名称商品名称商品名称商品名称名称商品名称名称商品名称商品名称
                    </div>
                    <div class="qr-code" v-if="formData.show.qrcode" :class="{ left: formData.qrcode_align == 1 }">
                        <vue-qr :text="'null'" :size="64" :margin="0"></vue-qr>
                    </div>
                    <div
                        v-if="formData.show.qrcode_title"
                        class="qrcode-title"
                        :class="{ left: formData.qrcode_align == 1 }"
                        :style="{ color: formData.style.qrcode_title }"
                    >
                        长按识别二维码
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="setConfig">保存</el-button>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleSelect from '@/components/decorate/style-select.vue'
import { apiGoodsPosterGet, apiGoodsPosterSet } from '@/api/application/custom_poster'
import VueQr from 'vue-qr'

@Component({
    components: {
        ColorSelect,
        StyleSelect,
        VueQr,
        MaterialSelect
    }
})
export default class GoodsPoster extends Vue {
    loading = false
    formData = {
        id: 1,
        background_type: 1,
        background_url: '',
        show: {
            user_avtar: 1,
            user_name: 1,
            goods_img: 1,
            goods_name: 1,
            goods_sale_price: 1,
            goods_origin_price: 1,
            qrcode: 1,
            qrcode_title: 1
        },
        qrcode_align: 2,
        style: {
            user_name: '#333333',
            goods_sale_price: '#FF0610',
            goods_origin_price: '#999999',
            goods_name: '#333333',
            qrcode_title: '#999999'
        }
    }
    goodsImage = require('@/assets/images/goods_image.png')
    styleData = [
        {
            value: 1,
            img: require('@/assets/images/goods_poster1.png'),
            text: '样式一'
        },
        {
            value: 2,
            img: require('@/assets/images/goods_poster2.png'),
            text: '样式二'
        },
        {
            value: 3,
            img: require('@/assets/images/goods_poster3.png'),
            text: '样式三'
        }
    ]
    formRules = {}
    get getSelectStyleText() {
        return this.styleData.find(item => item.value == this.formData.id)?.text
    }
    async getConfig() {
        this.loading = true
        try {
            const data = await apiGoodsPosterGet()
            this.formData = data
            this.loading = false
        } catch (error) {
            this.loading = false
        }
    }
    async setConfig() {
        await apiGoodsPosterSet(this.formData)
        this.getConfig
    }
    created() {
        this.getConfig()
    }
    /** S Computed **/
    // get goodsImage() {
    //     console.log(this.formData.id)
    //     return this.formData.id
    // if (this.formData.id == 3) {
    //     return require('@/assets/images/goods_image2.png')
    // } else {
    //     return require('@/assets/images/goods_image.png')
    // }
}
/** E Computed **/

// @Watch('formData.id', { immediate: true })
// handle(value: any) {
//     console.log(value)
// }
// }
</script>

<style lang="scss" scoped>
.goods-poster {
    padding-bottom: 80px;
    .preview {
        width: 300px;
        height: 534px;
        background: rgba(0, 0, 0, 0.3);
        .poster {
            position: relative;
            background: #fff no-repeat;
            background-size: 100% auto;
            width: 260px;
            height: 394px;
            border-radius: 6px;
            overflow: hidden;

            .user-info {
                position: absolute;
                top: 12px;
                left: 15px;
                z-index: 1;
            }
            .goods-image {
                position: absolute;
                left: 15px;
                top: 50px;
                right: 15px;
            }
            .goods-price {
                position: absolute;
                left: 15px;
                top: 290px;
                &.left {
                    left: 100px;
                }
            }
            .goods-name {
                position: absolute;
                left: 15px;
                top: 320px;
                width: 145px;
                line-height: 1.5em;
                &.left {
                    left: 100px;
                }
            }
            .qr-code {
                position: absolute;
                left: 170px;
                top: 290px;
                width: 64px;
                height: 64px;
                &.left {
                    left: 25px;
                }
            }
            .qrcode-title {
                position: absolute;
                left: 160px;
                top: 360px;
                transform: scale(0.9);
                &.left {
                    left: 15px;
                }
            }
            &.style2 {
                .goods-image {
                    top: 0;
                    right: 0;
                    left: 0;
                }
                .goods-name {
                    width: 230px;
                    top: 270px;
                    left: 15px;
                    .left {
                        left: 100px;
                    }
                }
                .goods-price {
                    top: 320px;
                }
                .qr-code {
                    left: 180px;
                    top: 315px;
                    &.left {
                        left: 15px;
                    }
                }
                .qrcode-title {
                    left: 15px;
                    top: 350px;
                    &.left {
                        left: 100px;
                    }
                }
            }
            &.style3 {
                .goods-image {
                    top: 0;
                    right: 0;
                    left: 0;
                    bottom: 0;
                }
                .qr-code {
                    left: 170px;
                    top: 290px;
                    &.left {
                        left: 25px;
                    }
                }
            }
        }
    }
}
</style>
