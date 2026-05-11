<template>
    <div class="member-price-edit">
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" content="设置会员价" />
        </div>

        <el-form ref="formRef" :model="formData" label-width="120px" size="small">
            <!-- 商品信息 -->
            <div class="ls-card m-t-15">
                <div class="card-title">商品信息</div>

                <div class="card-content m-t-24">
                    <el-form-item label="商品编号">
                        {{ formData.code }}
                    </el-form-item>

                    <el-form-item label="商品图片" prop="logo">
                        <img class="flex-none" style="width: 100px; height: 100px" :src="formData.image" />
                    </el-form-item>

                    <el-form-item label="商品名称">
                        {{ formData.name }}
                    </el-form-item>
                </div>
            </div>

            <!-- 会员价设置 -->
            <div class="ls-card m-t-15">
                <div class="card-title">会员价设置</div>

                <div class="card-content m-t-24">
                    <el-form-item label="参与折扣">
                        <el-radio-group v-model="formData.is_discount">
                            <el-radio :label="1">参与</el-radio>
                            <el-radio :label="0">不参与</el-radio>
                        </el-radio-group>

                        <div class="muted xs m-r-16">是否参与会员折扣，选择不参与用户消费将不产生会员折扣</div>
                    </el-form-item>

                    <el-form-item label="折扣规则">
                        <el-radio-group v-model="formData.discount_rule">
                            <el-radio :label="memberPriceType.MEMBER_LEVEL_PRICE"> 根据会员等级设置 </el-radio>

                            <el-radio :label="memberPriceType.ALONE_SETUP"> 单独设置 </el-radio>
                        </el-radio-group>

                        <div class="muted xs m-r-16">默认使用会员等级设置的折扣</div>

                        <!-- 根据会员等级设置的折扣 -->
                        <div v-if="formData.discount_rule == memberPriceType.MEMBER_LEVEL_PRICE">
                            <el-table
                                :data="discountRulesData"
                                style="width: 100%"
                                size="mini"
                                :header-cell-style="{ background: '#f5f8ff' }"
                            >
                                <el-table-column prop="name" label="会员等级"> </el-table-column>

                                <el-table-column prop="spec_value_str" label="商品规格">
                                    <template slot-scope="scope">
                                        <div
                                            v-for="(item, index) in scope.row.goods_item"
                                            :key="index"
                                            class="m-b-10 m-t-10"
                                        >
                                            {{ item.spec_value_str }}
                                        </div>
                                    </template>
                                </el-table-column>

                                <el-table-column prop="sell_price" label="价格">
                                    <template slot-scope="scope">
                                        <div
                                            v-for="(item, index) in scope.row.goods_item"
                                            :key="index"
                                            class="m-b-10 m-t-10"
                                        >
                                            ¥{{ item.sell_price }}
                                        </div>
                                    </template>
                                </el-table-column>

                                <el-table-column prop="discount" label="会员价">
                                    <template slot-scope="scope">
                                        <div v-if="scope.row.discount <= 0">无折扣</div>
                                        <div v-else>{{ scope.row.discount }} 折</div>
                                        <!-- <div>{{ scope.row.discount }} 折</div> -->
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>

                        <!-- 单独设置 -->
                        <div v-if="formData.discount_rule == memberPriceType.ALONE_SETUP">
                            <!-- 单规格 -->
                            <div v-if="formData.spec_type == specType.ALONE_SPECIFICATION">
                                <el-table
                                    :data="discountRulesData"
                                    style="width: 100%"
                                    size="mini"
                                    :header-cell-style="{ background: '#f5f8ff' }"
                                >
                                    <el-table-column prop="name" label="会员等级"> </el-table-column>

                                    <el-table-column prop="spec_value_str" label="商品规格">
                                        <template slot-scope="scope">
                                            <div
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                class="m-b-10 m-t-10"
                                            >
                                                {{ item.spec_value_str }}
                                            </div>
                                        </template>
                                    </el-table-column>

                                    <el-table-column prop="sell_price" label="价格">
                                        <template slot-scope="scope">
                                            <div
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                class="m-b-10 m-t-10"
                                            >
                                                ¥{{ item.sell_price }}
                                            </div>
                                        </template>
                                    </el-table-column>

                                    <el-table-column prop="discount_price" label="会员价 ">
                                        <template slot-scope="scope">
                                            <el-input
                                                style="width: 220px"
                                                placeholder="请输入"
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                v-model="item.discount_price"
                                                oninput="value=value.replace(/[^0-9.]/g,'')"
                                            >
                                                <template slot="append"> 元 </template>
                                            </el-input>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>

                            <!-- 多规格 -->
                            <div v-if="formData.spec_type == specType.MUCH_SPECIFICATION">
                                <el-table
                                    :data="discountRulesData"
                                    style="width: 100%"
                                    size="mini"
                                    :header-cell-style="{ background: '#f5f8ff' }"
                                >
                                    <el-table-column prop="name" label="会员等级"> </el-table-column>

                                    <el-table-column prop="spec_value_str" label="商品规格">
                                        <template slot-scope="scope">
                                            <div
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                class="m-b-10 m-t-10"
                                            >
                                                {{ item.spec_value_str }}
                                            </div>
                                        </template>
                                    </el-table-column>

                                    <el-table-column prop="sell_price" label="价格">
                                        <template slot-scope="scope">
                                            <div
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                class="m-b-10 m-t-10"
                                            >
                                                ¥{{ item.sell_price }}
                                            </div>
                                        </template>
                                    </el-table-column>

                                    <el-table-column prop="discount_price" label="会员价 ">
                                        <template slot-scope="scope">
                                            <el-input
                                                style="width: 220px"
                                                placeholder="请输入"
                                                v-for="(item, index) in scope.row.goods_item"
                                                :key="index"
                                                class="m-b-10 m-t-10"
                                                v-model="item.discount_price"
                                                oninput="value=value.replace(/[^0-9.]/g,'')"
                                            >
                                                <template slot="append"> 元 </template>
                                            </el-input>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.push({ name: 'member_price' })">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()"> 保存 </el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { PageMode } from '@/utils/type'
import { apiMemberPriceDetail, apiMemberPriceEdit } from '@/api/marketing/member_price'

@Component({
    components: {
        MaterialSelect
    }
})
export default class memberPriceEdit extends Vue {
    mode: string = PageMode.EDIT // 当前页面【add: 添加用户等级 | edit: 编辑用户等级】

    // 折扣规则：1-根据会员等级设置，2-单独设置
    memberPriceType = {
        MEMBER_LEVEL_PRICE: 1, // 1-根据会员等级设置
        ALONE_SETUP: 2 // 2-单独设置
    }

    // 规格类型：1-单规格；2-多规格
    specType = {
        ALONE_SPECIFICATION: 1, // 单规格
        MUCH_SPECIFICATION: 2 // 多规格
    }

    goods_id = ''

    // 表单数据
    formData = {
        discount_rule: '',
        spec_type: '',
        is_discount: ''
    }

    $refs!: {
        formRef: any
    }

    // 折扣规则数据
    discountRulesData: any = []

    // 获取详情
    getMemberDiscountDetail() {
        apiMemberPriceDetail({
            goods_id: this.goods_id
        }).then((res: any) => {
            console.log(res, 'res')
            this.formData = res

            this.discountRulesData = res.level_goods_item
            console.log('discountRulesData', this.discountRulesData)
        })
    }

    // 编辑
    handleMemberDiscountEdit() {
        apiMemberPriceEdit({
            ...this.formData,
            id: this.goods_id
        }).then(() => {
            setTimeout(() => this.$router.push({ name: 'member_price' }), 500)
        })
    }

    // 保存
    onSubmit() {
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }

            switch (this.mode) {
                case PageMode.EDIT:
                    return this.handleMemberDiscountEdit()
            }
        })
    }

    created() {
        const query: any = this.$route.query
        if (query.id) {
            this.goods_id = query.id
        }
        // if (query.page) {
        //     this.page = query.page
        // }
        this.getMemberDiscountDetail()
    }
}
</script>

<style lang="scss" scoped>
.card-title {
    font-size: 14px;
    font-weight: 500;
}
</style>
