<template>
    <div class="delivery-index">
        <div class="delivery-item">
            <div class="delivery-item__header flex row-between">
                <div class="nr weight-500">
                    {{ form.express.express_name }}
                    <el-button
                        style="padding: 0"
                        class="ls-edit"
                        type="text"
                        icon="el-icon-edit"
                        @click="openDialog('快递发货')"
                    ></el-button>
                </div>
                <el-switch
                    v-model="form.express.is_express"
                    :active-value="1"
                    :inactive-value="0"
                    @change="handleChange($event, 'is_express')"
                >
                </el-switch>
            </div>
            <div class="delivery-item__content flex row-between">
                <div class="muted">启用快递发货后，买家下单可以选择快递发货，由卖家安排快递送货上门</div>
                <div class="btns">
                    <el-button type="text" size="small" @click="$router.push('/setting/delivery/freight')"
                        >运费模版</el-button
                    >
                    <el-button type="text" size="small" @click="$router.push('/setting/delivery/express')"
                        >快递公司</el-button
                    >
                    <el-button type="text" size="small" @click="$router.push('/setting/delivery/logistics')"
                        >物流接口</el-button
                    >
                </div>
            </div>
        </div>
        <div class="delivery-item m-t-16">
            <div class="delivery-item__header flex row-between">
                <div class="nr weight-500">
                    {{ form.selffetch.selffetch_name }}
                    <el-button
                        style="padding: 0"
                        class="ls-edit"
                        type="text"
                        icon="el-icon-edit"
                        @click="openDialog('上门自提')"
                    ></el-button>
                </div>
                <el-switch
                    v-model="form.selffetch.is_selffetch"
                    :active-value="1"
                    :inactive-value="0"
                    @change="handleChange($event, 'is_selffetch')"
                >
                </el-switch>
            </div>
            <div class="delivery-item__content flex row-between">
                <div class="muted">
                    启用上门自提后，买家下单可以选择就近门店自提点，卖家需要确保指定的自提点商品库存充足。
                </div>
                <div class="btns">
                    <el-button type="text" size="small" @click="$router.push('/selffetch/selffetch_shop')"
                        >查看自提点</el-button
                    >
                </div>
            </div>
        </div>
        <CustomName ref="customName" />
    </div>
</template>

<script lang="ts">
import { apideliveryWay, apideliveryWayset } from '@/api/setting/delivery'
import { Component, Prop, Vue } from 'vue-property-decorator'
import CustomName from './components/custom-name.vue'

@Component({
    components: {
        CustomName
    }
})
export default class DeliveryIndex extends Vue {
    $refs!: { customName: any }
    form = {
        express: {
            is_express: 0,
            express_name: ''
        },
        selffetch: {
            is_selffetch: 0,
            selffetch_name: ''
        }
    }
    handleChange($event: number, type: 'is_express' | 'is_selffetch') {
        if (!$event && !this.form.express.is_express && !this.form.selffetch.is_selffetch) {
            this.$message.warning('至少启用一种配送方式')
            this.form.express.is_express = 1
            this.form.selffetch.is_selffetch = 1
            return
        }
        apideliveryWayset({ ...this.form.express, ...this.form.selffetch })
    }
    getLogisticsConfig() {
        apideliveryWay().then(res => {
            this.form = res
        })
    }
    openDialog(way: string) {
        this.$refs.customName.openDialog(way, this.form)
    }
    created() {
        this.getLogisticsConfig()
    }
}
</script>
<style lang="scss" scoped>
.delivery-index {
    .delivery-item {
        border-radius: 8px;
        background-color: #fff;
        &__header {
            padding: 16px 24px;
            border-bottom: $--border-base;
        }
        &__content {
            padding: 8px 24px;
        }
    }
}
</style>
