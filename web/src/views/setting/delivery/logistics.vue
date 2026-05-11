<template>
    <div class="ls-logistics">
        <div class="ls-card ls-logistics__header">
            <el-page-header @back="$router.go(-1)" :content="$route.meta.title"></el-page-header>
        </div>
        <div class="ls-card ls-logistics__form m-t-10">
            <el-form class="ls-form" ref="form" :model="form" label-width="120px" size="small">
                <el-form-item label="选择类型">
                    <el-radio-group v-model="form.express_type">
                        <el-radio label="express_bird">快递鸟</el-radio>
                        <el-radio label="express_hundred">快递100</el-radio>
                    </el-radio-group>
                </el-form-item>
                <template v-if="form.express_type == 'express_bird'">
                    <el-form-item label="快递鸟套餐">
                        <el-radio-group v-model="form.express_bird.set_meal">
                            <el-radio label="free">免费</el-radio>
                            <el-radio label="pay">收费</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="EBussiness ID">
                        <el-input v-model="form.express_bird.ebussiness_id" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="APPKEY">
                        <el-input v-model="form.express_bird.app_key" placeholder=""></el-input>
                    </el-form-item>
                </template>
                <template v-else>
                    <el-form-item label="接口类型">
                        <el-radio-group v-model="form.express_hundred.interface_type">
                            <el-radio label="free">免费版</el-radio>
                            <el-radio label="limited_free">限量免费</el-radio>
                            <el-radio label="enterprise">企业接口</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="CUSTOMER">
                        <el-input v-model="form.express_hundred.customer" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="APPKEY">
                        <el-input v-model="form.express_hundred.app_key" placeholder=""></el-input>
                    </el-form-item>
                </template>
            </el-form>
        </div>
        <div class="ls-logistics__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiLogisticsConfig, apiSetLogisticsConfig } from '@/api/setting/delivery'
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component
export default class Logistics extends Vue {
    form = {
        express_bird: {
            set_meal: 'free',
            app_key: '',
            ebussiness_id: ''
        },
        express_hundred: {
            interface_type: 'enterprise',
            app_key: '',
            customer: ''
        },
        express_type: 'express_bird'
    }
    handleSave() {
        apiSetLogisticsConfig(this.form)
    }
    getLogisticsConfig() {
        apiLogisticsConfig().then(res => {
            this.form = res
        })
    }
    created() {
        this.getLogisticsConfig()
    }
}
</script>
<style lang="scss" scoped></style>
