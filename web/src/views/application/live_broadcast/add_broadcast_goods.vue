<template>
    <div class="add-broadcast-goods">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="mode === 'add' ? '添加直播商品' : ''" />
        </div>

        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-content m-t-24">
                    <el-form-item label="产品来源" prop="" required>
                        <el-select v-model="goodsType" placeholder="请选择产品来源">
                            <el-option label="商品库" value="1"> </el-option>
                            <el-option label="自定义" value="2"> </el-option>
                        </el-select>
                        <div class="m-t-16" v-if="goodsType == '1'">
                            <ls-goods-select title="选择商品" @getGoods="getGoods">
                                <el-button slot="trigger" type="primary" size="small">选择商品</el-button>
                            </ls-goods-select>
                        </div>
                    </el-form-item>

                    <el-form-item label="商品名称" prop="name">
                        <el-input v-model="form.name" placeholder="请输入商品名称"></el-input>
                    </el-form-item>
                    <el-form-item label="商品封面" prop="image">
                        <!-- <material-select :limit="1" v-model="form.image" /> -->
                        <el-upload
                            :class="ishide ? 'hide' : ''"
                            :action="action"
                            :headers="{
                                token: $store.getters.token,
                                version: version
                            }"
                            list-type="picture-card"
                            :limit="1"
                            :on-remove="handleRemove"
                            :on-success="handleSuccess"
                            :on-exceed="handleExceed"
                            :on-error="handleError"
                        >
                            <i class="el-icon-plus"></i>
                        </el-upload>
                        <div class="muted xs m-r-16">
                            建议尺寸：200像素 * 200像素，图片尺寸不能超过300像素 * 300像素。
                        </div>
                    </el-form-item>

                    <el-form-item label="价格形式" prop="price_type">
                        <div class="">
                            <el-radio :label="1" v-model="form.price_type">
                                <span class="m-r-30 first-desc">一口价</span>
                                <span class="m-r-5">价格</span>
                                <el-input class="ls-input" v-model="form.price" size="small"> </el-input>
                                <span class="m-l-5">元</span>
                            </el-radio>
                        </div>
                        <div class="m-t-16">
                            <el-radio :label="2" v-model="form.price_type">
                                <span class="m-r-20 first-desc">价格区间</span>
                                <span class="m-r-5">价格</span>
                                <el-input class="ls-input" v-model="form.price" size="small"> </el-input>
                                <span class="m-l-5">元</span>
                                <span class="m-l-5 m-r-5">-</span>
                                <el-input class="ls-input" v-model="form.price2" size="small"> </el-input>
                                <span class="m-l-5">元</span>
                            </el-radio>
                        </div>
                        <div class="m-t-16">
                            <el-radio :label="3" v-model="form.price_type">
                                <span class="m-r-10 first-desc">显示折扣价</span>
                                <span class="m-r-5">原价</span>
                                <el-input class="ls-input" v-model="form.price" size="small"> </el-input>
                                <span class="m-l-5">元</span>
                                <span class="m-l-5 m-r-5">现价</span>
                                <el-input class="ls-input" v-model="form.price2" size="small"> </el-input>
                                <span class="m-l-5">元</span>
                            </el-radio>
                        </div>
                    </el-form-item>

                    <el-form-item label="商品链接" prop="url">
                        <el-input v-model="form.url" placeholder="请输入商品链接"></el-input>
                        <div class="muted xs m-r-16">
                            请确保小程序页面路径可被访问，例如：pages/goods_detail/goods_detail?id=goods_id
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import { PageMode } from '@/utils/type'
import MaterialSelect from '@/components/material-select/index.vue'
import LsGoodsSelect from '@/components/live-broadcast/ls-goods-select.vue'
import { apiLiveGoodsAdd } from '@/api/application/live_broadcast'
import config from '@/config'
import { throttle } from '@/utils/util'
@Component({
    components: {
        MaterialSelect,
        LsGoodsSelect
    }
})
export default class AddBroadcastGoods extends Vue {
    action = `${config.baseURL}/adminapi/upload/wechatMaterial`
    version = config.version
    ishide = false

    mode: string = PageMode.ADD // 当前页面【add: 添加 | edit: 编辑】

    goodsType = '1'

    form = {
        name: '', // 商品名称，最长14个汉字
        image: '', // 商品封面图
        price_type: 1, // 价格类型，1：一口价（只需要传入price，price2不传） 2：价格区间（price字段为左边界，price2字段为右边界，price和price2必传） 3：显示折扣价（price字段为原价，price2字段为现价， price和price2必传）
        price: 0, // 数字，最多保留两位小数，单位元
        price2: 0, // 数字，最多保留两位小数，单位元
        url: '' // 商品详情页的小程序路径
    }

    allPrice = {}

    formRules = {
        name: [
            {
                required: true,
                message: '请输入商品名称',
                trigger: 'blur'
            }
        ],
        image: [
            {
                required: true,
                message: '请选择图片',
                trigger: 'blur'
            }
        ],
        url: [
            {
                required: true,
                message: '请输入商品链接',
                trigger: 'change'
            }
        ],
        price_type: [
            {
                required: true,
                message: '请选择价格类型',
                trigger: 'change'
            }
        ]
    }
    $refs!: {
        formRef: any
    }

    selecGoods = {} // 选中的商品

    // 获取选择用户
    getGoods(val: any) {
        // 未选中用户不修改
        if (val == null) {
            return
        }
        this.selecGoods = val
        this.form.url = 'pages/goods_detail/goods_detail?id=' + val.id
        this.form.name = val.name
        this.form.price = val.min_price
    }

    // 提交
    onSubmit() {
        // 验证表单格式是否正确
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }
            // 请求发送
            // switch (this.mode) {
            // 	case PageMode['ADD']:
            // 		return this.handleUserLabelAdd()
            // 	case PageMode['EDIT']:
            // 		return this.handleUserLabelEdit()
            // }
            this.liveGoodsAdd()
        })
    }

    // 创建直播商品
    liveGoodsAdd() {
        apiLiveGoodsAdd(this.form)
            .then((res: any) => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch((err: any) => {})
    }

    handleRemove(file: any, fileList: any) {
        this.ishide = false
    }
    handleSuccess(response: any, file: any, fileList: any[]) {
        this.form.image = response.data.media_id
        this.ishide = true
    }
    handleError(err: any, file: any) {
        this.$message.error(`${file.name}文件上传失败`)
    }
    handleExceed() {
        this.$message.error('超出上传上限，请重新上传')
    }

    created() {
        this.onSubmit = throttle(this.onSubmit, 1000)
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-upload--picture-card {
    width: 98px;
    height: 98px;
}

::v-deep .el-upload {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .el-upload-list--picture-card .el-upload-list__item {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .el-upload-list--picture-card .el-upload-list__item-thumbnail {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .avatar {
    width: 98px;
    height: 98px;
}
::v-deep .el-upload-list__item-status-label {
    visibility: hidden;
}
::v-deep .hide .el-upload--picture-card {
    display: none;
}

.add-broadcast-goods {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }

    .ls-card {
        .card-title {
            font-size: 14px;
            font-weight: 500;
        }
    }

    .ls-input {
        width: 100px;
    }

    .first-desc {
        width: 80px;
    }
}
</style>
