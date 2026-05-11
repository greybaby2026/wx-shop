<template>
    <div class="shop-index">
        <div class="ls-card" style="padding-top: 0">
            <el-tabs v-model="activeName" @tab-click="handleTabClick">
                <el-tab-pane
                    :label="item.label"
                    :name="String(item.type)"
                    v-for="(item, index) in tabs"
                    :key="index"
                    v-loading="loading"
                >
                    <el-form ref="form" label-width="80px" size="small" label-position="left">
                        <div class="flex col-top flex-wrap">
                            <div class="preview m-r-30 m-t-20">
                                <img :src="item.bg" alt="" />
                                <el-image
                                    class="banner"
                                    :style="item.css"
                                    :src="$getImageUri(page.content.url)"
                                    alt=""
                                    fit="cover"
                                ></el-image>
                            </div>
                            <div class="setting m-t-20 flex-1">
                                <div class="title">
                                    <span class="lg weight-500">{{ item.title }}</span>
                                    <span class="muted m-l-10">{{ item.desc }}</span>
                                </div>
                                <el-form-item label-width="0">
                                    <div class="flex m-t-20">
                                        <material-select
                                            class="m-r-10"
                                            ref="materialSelect"
                                            v-model="page.content.url"
                                            :size="96"
                                            upload-bg="#fff"
                                            :enable-domain="false"
                                        >
                                            <i class="el-icon-plus lg"></i>
                                        </material-select>
                                        <div>
                                            <el-form-item label="广告名称">
                                                <el-input
                                                    style="width: 200px"
                                                    v-model="page.content.name"
                                                    placeholder="请输入广告名称"
                                                ></el-input>
                                            </el-form-item>
                                            <el-form-item label="链接地址">
                                                <link-select client="pc" v-model="page.content.link" />
                                            </el-form-item>
                                        </div>
                                    </div>
                                </el-form-item>
                                <el-form-item label-width="0">
                                    <el-button type="primary" @click="setData">保存</el-button>
                                </el-form-item>
                            </div>
                        </div>
                    </el-form>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { apiPcThemePageDetail, apiPcThemePageEdit } from '@/api/shop'
import { copyClipboard } from '@/utils/util'
import { Component, Prop, Vue } from 'vue-property-decorator'
import LinkSelect from '@/components/link-select/index.vue'
import MaterialSelect from '@/components/material-select/index.vue'
@Component({
    components: {
        MaterialSelect,
        LinkSelect
    }
})
export default class ShopIndex extends Vue {
    activeName = '2'
    loading = true
    tabs = [
        {
            type: 2,
            label: '登录',
            title: '登录页背景图',
            desc: '建议尺寸：750*440',
            bg: require('@/assets/images/ad_login_img.png'),
            css: {
                left: '148px',
                top: '83px',
                width: '315px',
                height: '192px'
            }
        },
        {
            type: 3,
            label: '限时秒杀',
            title: '限时秒杀广告',
            desc: '建议尺寸：1180*240',
            bg: require('@/assets/images/ad_seckill_img.png'),
            css: {
                left: '153px',
                top: '76px',
                width: '493px',
                height: '101px'
            }
        },
        {
            type: 4,
            label: '领券中心',
            title: '领券中心广告',
            desc: '建议尺寸：1180*240',
            bg: require('@/assets/images/ad_coupon_img.png'),
            css: {
                left: '153px',
                top: '76px',
                width: '493px',
                height: '101px'
            }
        },
        {
            type: 5,
            label: '商城公告',
            title: '商城公告广告',
            desc: '建议尺寸：1180*240',
            bg: require('@/assets/images/ad_notice_img.png'),
            css: {
                left: '153px',
                top: '76px',
                width: '493px',
                height: '101px'
            }
        }
    ]

    page = {
        content: {
            url: '',
            link: {},
            name: ''
        }
    }

    client = 1

    getData() {
        this.loading = true
        this.page = {
            content: {
                url: '',
                link: {},
                name: ''
            }
        }
        apiPcThemePageDetail({
            type: Number(this.activeName)
        }).then(res => {
            this.page = res
            this.loading = false
        })
    }

    handleTabClick() {
        this.getData()
    }

    setData() {
        apiPcThemePageEdit({
            ...this.page,
            content: JSON.stringify(this.page.content)
        }).then(res => {
            this.loading = false
        })
    }

    created() {
        this.getData()
    }
}
</script>
<style lang="scss" scoped>
.shop-index {
    .preview {
        width: 800px;
        height: 468px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        position: relative;
        img {
            width: 100%;
            height: 100%;
        }
        .banner {
            position: absolute;
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
