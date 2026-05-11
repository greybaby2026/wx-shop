<template>
    <div class="shop-index">
        <div class="ls-card" v-loading="loading">
            <div class="nr weight-500">当前首页</div>
            <div class="flex flex-wrap m-b-50 col-top">
                <!-- <div class="shop-phone m-t-30">
                    <iframe
                        v-if="pclink"
                        class="phone-iframe"
                        :src="pclink"
                        alt=""
                        @load="loading = false"
                    ></iframe>
                    <div class="phone-iframe-mask"></div>
                </div> -->
                <div class="shop-info m-t-30">
                    <el-button
                        type="primary"
                        size="small"
                        @click="
                            $router.push({
                                path: '/decorate/pc_index',
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
                        <el-form-item label="最近更新：" v-if="indexData.update_time">
                            <div class="xs">{{ indexData.update_time }}</div>
                        </el-form-item>
                        <el-form-item label="pc商城链接：">
                            <el-input :value="pclink" style="width: 460px" readonly>
                                <div slot="append">
                                    <el-button type="primary" @click="handleCopy">复制</el-button>
                                </div>
                            </el-input>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiPcThemePageIndex } from '@/api/shop'
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
    indexData = {}
    pclink = ''
    client = 1
    getIndex() {
        apiPcThemePageIndex().then(res => {
            const { home, uri } = res
            this.indexData = home
            this.pclink = uri
            this.loading = false
        })
    }
    handleCopy() {
        copyClipboard(this.pclink)
            .then(() => {
                this.$message.success('复制成功')
            })
            .catch(err => {
                this.$message.error('复制失败')
            })
    }

    created() {
        // document.body.setAttribute('style', 'min-width: 1500px')
        this.getIndex()
    }
    beforeDestroy() {
        // document.body.setAttribute('style', '')
    }
}
</script>
<style lang="scss" scoped>
.shop-index {
    .shop-phone {
        position: relative;
        border-radius: 4px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.16);
        overflow: hidden;
        width: 1200px;
        height: 800px;
        margin-right: 60px;
        .phone-iframe {
            width: 100%;
            height: 100%;
            outline: none;
            border: none;
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
    }
}
</style>
