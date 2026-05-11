<template>
    <div class="service-setting">
        <!-- Header -->
        <div class="ls-card">
            <el-alert
                title="1.为买家和卖家提供多元化的客服渠道与专业服务支持。2.用户可在微信内、外各个场景中对商家发起咨询，还有智能客服助手快速响应常见问题。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="ls-card m-t-24">
            <div class="nr weight-500 m-b-20">客服配置</div>
            <el-form ref="formRef" :model="form" label-width="120px" size="small">
                <el-form-item label="场景">
                    <div class="flex">
                        <div class="option-item" @click="handleClick(0)" :class="activeIndex == 0 ? 'actived' : ''">
                            <span style="line-height: 20px"> 微信小程序</span>
                            <span style="line-height: 20px" class="muted xxs"> {{ scene[form.mnp.way - 1] }}</span>
                        </div>
                        <div class="option-item" @click="handleClick(1)" :class="activeIndex == 1 ? 'actived' : ''">
                            <span style="line-height: 20px"> 微信公众号</span>
                            <span style="line-height: 20px" class="muted xxs"> {{ scene[form.oa.way - 1] }}</span>
                        </div>
                        <div class="option-item" @click="handleClick(2)" :class="activeIndex == 2 ? 'actived' : ''">
                            <span style="line-height: 20px"> H5商城</span>
                            <span style="line-height: 20px" class="muted xxs"> {{ scene[form.h5.way - 1] }}</span>
                        </div>
                        <div class="option-item" @click="handleClick(3)" :class="activeIndex == 3 ? 'actived' : ''">
                            <span style="line-height: 20px"> PC商城</span>
                            <span style="line-height: 20px" class="muted xxs"> {{ scene[form.pc.way - 1] }}</span>
                        </div>
                        <div class="option-item" @click="handleClick(4)" :class="activeIndex == 4 ? 'actived' : ''">
                            <span style="line-height: 20px"> APP商城</span>
                            <span style="line-height: 20px" class="muted xxs"> {{ scene[form.app.way - 1] }}</span>
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="客服方式">
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="1"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(1)"
                        >扫二维码</el-radio
                    >
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="2"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(2)"
                        >拨打电话</el-radio
                    >
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="3"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(3)"
                        >系统客服</el-radio
                    >
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="4"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(4)"
                        >企业微信客服</el-radio
                    >
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="5"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(5)"
                        >微信小程序客服
                    </el-radio>
                    <el-radio
                        v-model="form[`${serviceName[activeIndex]}`].way"
                        label="6"
                        v-if="sceneLimit[`${serviceName[activeIndex]}`].includes(6)"
                        >第三方客服
                    </el-radio>
                </el-form-item>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 1">
                    <el-form-item label="平台名称">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].name"></el-input>
                    </el-form-item>
                    <el-form-item label="备注">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].remarks"></el-input>
                    </el-form-item>
                    <el-form-item label="客服电话">
                        <el-input
                            type="number"
                            placeholder
                            v-model="form[`${serviceName[activeIndex]}`].phone"
                        ></el-input>
                    </el-form-item>
                    <el-form-item label="服务时间">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].business_time"></el-input>
                    </el-form-item>
                    <!--  :rules="[
                        {
                            required: true,
                            message: '请选择客服二维码',
                            trigger: ['blur', 'change']
                        }
                    ]" -->
                    <el-form-item label="客服二维码" prop="qr_code">
                        <material-select v-model="form[`${serviceName[activeIndex]}`].qr_code"></material-select>
                    </el-form-item>
                </template>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 2">
                    <el-form-item label="拨打电话">
                        <el-input
                            type="number"
                            placeholder
                            v-model="form[`${serviceName[activeIndex]}`].phone"
                        ></el-input>
                    </el-form-item>
                </template>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 3">
                    <el-form-item>
                        <div>请参考部署文档配置系统客服</div>
                    </el-form-item>
                </template>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 4">
                    <el-form-item label="企业ID">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].enterprise_id"></el-input>
                    </el-form-item>
                    <el-form-item label="企业客服链接">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].kefu_link"></el-input>
                        <div>
                            如何接入企业微信客服请查看
                            <a
                                :href="serviceLink[`${serviceName[activeIndex]}`]"
                                style="color: #5a86fa; text-decoration: none"
                                target="_blank"
                            >
                                企业微信客服指南
                            </a>
                        </div>
                    </el-form-item>
                </template>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 5">
                    <el-form-item>
                        <div>需前往小程序后台绑定客服人员</div>
                    </el-form-item>
                </template>
                <template v-if="form[`${serviceName[activeIndex]}`].way == 6">
                    <el-form-item label="客服链接">
                        <el-input placeholder v-model="form[`${serviceName[activeIndex]}`].kefu_link"></el-input>
                        <div>
                            请填写客服链接，聊天时跳转至第三方聊天窗口 例如：
                            <a
                                href="https://www.meiqia.com/"
                                style="color: #5a86fa; text-decoration: none"
                                target="_blank"
                            >
                                美恰客服
                            </a>
                        </div>
                    </el-form-item>
                </template>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer flex row-center">
            <div class="row-center flex">
                <el-button size="small" type="primary" @click="onSubmit">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiServiceGetConfig, apiServiceSetConfig } from '@/api/application/service'
import MaterialSelect from '@/components/material-select/index.vue'

@Component({
    components: {
        MaterialSelect
    }
})
export default class ServiceSetting extends Vue {
    activeIndex = 0
    serviceName = ['mnp', 'oa', 'h5', 'pc', 'app']
    serviceLink: any = {
        mnp: 'https://work.weixin.qq.com/nl/act/p/a733314375294bdd',
        oa: 'https://work.weixin.qq.com/nl/act/p/3f8820e724cb44c5',
        h5: 'https://work.weixin.qq.com/nl/act/p/4030a5b69149404d',
        pc: 'https://work.weixin.qq.com/nl/act/p/4030a5b69149404d',
        app: 'https://www.likeshop.cn/doc/13/4183'
    }
    scene = ['扫二维码', '拨打电话', '系统客服', '微信客服', '小程序客服', '第三方客服']
    sceneLimit: any = {
        mnp: [1, 2, 3, 4, 5],
        oa: [1, 2, 3, 4],
        h5: [1, 2, 3, 4],
        pc: [1, 4],
        app: [1, 2, 3, 4]
    }
    form: any = {
        mnp: {
            way: 1,
            name: '平台名称',
            remarks: '备注',
            phone: '',
            business_time: '9:00-18:00',
            qr_code: ''
        },
        oa: {
            way: 2,
            phone: ''
        },
        h5: {
            way: 1
        },
        pc: {
            way: 1,
            enterprise_id: '',
            kefu_link: ''
        },
        app: {
            way: 1,
            kefu_link: ''
        }
    }

    /** S Methods **/

    getServicegetConfig() {
        apiServiceGetConfig({}).then(res => {
            this.form = res
        })
    }
    handleClick(val: any) {
        this.activeIndex = val
    }
    onSubmit() {
        const refs = this.$refs.formRef as HTMLFormElement
        refs.validate((valid: boolean): any => {
            if (!valid) {
                return
            }
            // 请求发送
            apiServiceSetConfig({ ...this.form }).then(res => {
                this.getServicegetConfig()
            })
        })
    }

    /** E Methods **/

    /** Life Cycle **/
    created() {
        // 初始化数据
        this.getServicegetConfig()
    }
}
</script>

<style lang="scss" scoped>
.option-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    border-radius: 10px;
    padding: 5px 20px;
    margin-right: 20px;
    border: 1px solid rgba(238, 238, 238, 1);
    cursor: pointer;
}
.actived {
    background-color: #f5f8ff;
    border: 1px solid rgba(64, 115, 250, 1);
}
</style>
