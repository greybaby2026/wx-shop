<template>
    <div class="shop-index">
        <div class="ls-card" v-loading="loading">
            <div class="nr weight-500">当前首页</div>
            <div class="flex m-t-30 m-b-50 col-top">
                <div class="shop-phone">
                    <iframe
                        v-if="oaData.uri"
                        class="phone-iframe"
                        :src="oaData.uri"
                        alt=""
                        @load="loading = false"
                    ></iframe>
                    <div class="phone-iframe-mask"></div>
                </div>
                <div class="shop-info">
                    <el-button
                        type="primary"
                        size="small"
                        @click="
                            $router.push({
                                path: '/decorate/index',
                                query: { id: indexData.id }
                            })
                        "
                        >去编辑首页</el-button
                    >
                    <el-form class="m-t-20" size="small" label-width="100px">
                        <el-form-item label="首页名称：">
                            <div class="nr weight-500">
                                {{ indexData.name }}
                            </div>
                        </el-form-item>
                        <el-form-item label="最近更新：">
                            <div class="xs">{{ indexData.update_time }}</div>
                        </el-form-item>
                        <el-form-item label="实时预览：">
                            <el-radio-group v-model="client">
                                <el-radio :label="1">微信小程序</el-radio>
                                <el-radio :label="2">微信公众号</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <template v-if="client == 1">
                            <el-form-item label="">
                                <img v-if="mnpData.qr_code" class="qr-code" :src="mnpData.qr_code" />
                                <div class="muted" v-else>提示：小程序信息未配置或未发布最新小程序线上版本</div>
                            </el-form-item>
                        </template>
                        <template v-if="client == 2">
                            <el-form-item label="公众号链接：">
                                <el-input :value="oaData.uri" style="width: 460px" readonly>
                                    <div slot="append">
                                        <el-button type="primary" @click="handleCopy">复制</el-button>
                                    </div>
                                </el-input>
                            </el-form-item>
                            <el-form-item label="">
                                <vue-qr :text="oaData.uri" :size="140"></vue-qr>
                            </el-form-item>
                        </template>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiThemePageIndex } from '@/api/shop'
import { copyClipboard } from '@/utils/util'
import { Component, Prop, Vue } from 'vue-property-decorator'
import VueQr from 'vue-qr'
@Component({
    components: {
        VueQr
    }
})
export default class ShopIndex extends Vue {
    loading = true
    indexData: any = {}
    mnpData: any = {}
    oaData: any = {}
    client = 1
    getIndex() {
        apiThemePageIndex().then(res => {
            const { home, mp, oa } = res
            this.indexData = home
            this.mnpData = mp
            this.oaData = oa
        })
    }
    handleCopy() {
        copyClipboard(this.oaData.uri)
            .then(() => {
                this.$message.success('复制成功')
            })
            .catch(err => {
                this.$message.error('复制失败')
            })
    }

    created() {
        this.getIndex()
    }
}
</script>
<style lang="scss" scoped>
.shop-index {
    .shop-phone {
        position: relative;
        width: 300px;
        height: 530px;
        border-radius: 4px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.16);
        overflow: hidden;
        margin-right: 60px;
        .phone-iframe {
            outline: none;
            border: none;
            width: 100%;
            height: 100%;
        }
        .phone-iframe-mask {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 9999;
            top: 0;
        }
    }
    .shop-info {
        ::v-deep.el-input-group__append {
            background-color: $--color-primary;
            color: #fff;
        }
        .qr-code {
            width: 140px;
            height: 140px;
        }
    }
}
</style>
