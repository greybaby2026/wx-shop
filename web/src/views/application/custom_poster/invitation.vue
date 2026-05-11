<template>
    <div class="invitation-poster">
        <div class="ls-card">
            <el-alert title="温馨提示：自定义邀请海报。" type="info" show-icon :closable="false" />
        </div>
        <div class="ls-card m-t-16 flex col-top" v-loading="loading">
            <el-form
                style="max-width: 900px"
                class="flex-1"
                ref="form"
                :model="formData"
                label-width="80px"
                size="small"
            >
                <div class="nr weight-500 m-b-16">基础设置</div>
                <el-form-item label="背景图片">
                    <el-radio-group v-model="formData.background_type">
                        <el-radio :label="1">系统默认</el-radio>
                        <el-radio :label="2">自定义</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item v-if="formData.background_type == 2">
                    <material-select v-model="formData.background_url"> </material-select>
                    <div class="muted">建议尺寸：520*830</div>
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
                        <el-checkbox v-model="formData.show.slogan" :true-label="1" :false-label="0" label="邀请语" />
                        <el-checkbox v-model="formData.show.qrcode" :true-label="1" :false-label="0" label="二维码" />
                        <el-checkbox
                            v-model="formData.show.slogan_code"
                            :true-label="1"
                            :false-label="0"
                            label="邀请码"
                        />
                    </div>
                </el-form-item>

                <el-form-item label="邀请语">
                    <el-input v-model="formData.slogan" maxlength="12" show-word-limit></el-input>
                </el-form-item>
                <div class="border-bottom" style="width: 600px"></div>
                <div class="nr weight-500 m-b-16 m-t-16">样式设置</div>
                <el-form-item label="用户昵称">
                    <color-select style="width: 240px" v-model="formData.style.user_name" reset-color="#333333" />
                </el-form-item>
                <el-form-item label="邀请语文本">
                    <color-select style="width: 240px" v-model="formData.style.slogan_text" reset-color="#FF0610" />
                </el-form-item>
                <el-form-item label="邀请码文本">
                    <color-select style="width: 240px" v-model="formData.style.slogan_code" reset-color="#999999" />
                </el-form-item>
                <el-form-item label="不透明度">
                    <slider style="width: 300px" v-model="formData.style.opacity" :max="100" :setOpacity="true" />
                </el-form-item>
            </el-form>
            <div class="preview flex row-center">
                <div
                    class="poster"
                    :style="{
                        'background-image': formData.background_type == 2 ? `url(${formData.background_url})` : ''
                    }"
                >
                    <div
                        class="poster-con"
                        v-if="showPosterCon"
                        :style="{
                            background: `rgba(255, 255, 255, ${formData.style.opacity / 100})`
                        }"
                    >
                        <div class="flex user-info">
                            <el-avatar
                                v-if="formData.show.user_avtar"
                                :size="32"
                                :src="require('@/assets/images/avatar.jpg')"
                            ></el-avatar>
                            <span
                                v-if="formData.show.user_name"
                                class="m-l-6 nr weight-500"
                                :style="{ color: formData.style.user_name }"
                            >
                                用户昵称
                            </span>
                        </div>
                        <div
                            v-if="formData.show.slogan"
                            class="slogan xs"
                            :style="{ color: formData.style.slogan_text }"
                        >
                            {{ formData.slogan }}
                        </div>
                        <div
                            v-if="formData.show.slogan_code"
                            class="slogan-code"
                            :style="{ color: formData.style.slogan_code }"
                        >
                            邀请码：9A00RE
                        </div>
                        <div class="qr-code" v-if="formData.show.qrcode">
                            <vue-qr :text="'null'" :size="64" :margin="0"></vue-qr>
                        </div>
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
import { Component, Vue } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import ColorSelect from '@/components/decorate/color-select.vue'
import StyleSelect from '@/components/decorate/style-select.vue'
import { apiInvitationPosterGet, apiInvitationPosterSet } from '@/api/application/custom_poster'
import VueQr from 'vue-qr'
import Slider from '@/components/decorate/slider.vue'
@Component({
    components: {
        ColorSelect,
        StyleSelect,
        VueQr,
        MaterialSelect,
        Slider
    }
})
export default class InvitationPoster extends Vue {
    loading = false
    formData = {
        background_type: 1,
        background_url: '',
        show: {
            user_avtar: 1,
            user_name: 1,
            slogan: 1,
            qrcode: 1,
            slogan_code: 1
        },
        slogan: '邀请你一起来赚大钱',
        style: {
            user_name: '#333333',
            slogan_text: '#FF0610',
            slogan_code: '#999999',
            opacity: 0
        }
    }

    get showPosterCon() {
        const { show } = this.formData as any
        return Object.keys(show).some(key => show[key])
    }

    async getConfig() {
        this.loading = true
        try {
            const data = await apiInvitationPosterGet()
            this.formData = data
            this.loading = false
        } catch (error) {
            this.loading = false
        }
    }
    async setConfig() {
        await apiInvitationPosterSet(this.formData)
        this.getConfig
    }

    get styles() {
        return this.$store.getters.styles
    }
    created() {
        this.getConfig()
    }
}
</script>

<style lang="scss" scoped>
.invitation-poster {
    padding-bottom: 80px;
    .preview {
        width: 300px;
        height: 534px;
        background: rgba(0, 0, 0, 0.3);
        .poster {
            position: relative;
            background: url(../../../assets/images/invitation_poster.png) no-repeat;
            background-size: 100% auto;
            width: 260px;
            height: 416px;
            border-radius: 6px;
            overflow: hidden;
            .poster-con {
                position: absolute;
                bottom: 0;
                width: 100%;
                background-color: #fff;
                border-radius: 6px;
                overflow: hidden;
                height: 125px;
                .user-info {
                    position: absolute;
                    top: 15px;
                    left: 15px;
                }
                .slogan {
                    position: absolute;
                    top: 60px;
                    left: 15px;
                }
                .slogan-code {
                    position: absolute;
                    top: 85px;
                    left: 15px;
                }
                .qr-code {
                    position: absolute;
                    top: 30px;
                    left: 180px;
                }
            }
        }
    }
}
</style>
