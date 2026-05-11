<template>
    <div class="setting-goods">
        <!-- 提示 -->
        <!-- <div class="ls-card">
			<el-alert title="温馨提示：应工信部的要求,请务必填写公安备案号和网站备案号，保存的备案信息，将展示在后台登陆页面。" type="info" :closable="false"
				show-icon  />
		</div> -->
        <!-- 主要内容 -->
        <el-form ref="goodsSettingsRef" :model="goodsSettings" label-width="120px" size="small">
            <!-- 店铺信息 -->
            <div class="ls-card">
                <div class="card-title">商品信息</div>
                <div class="card-content m-t-24">
                    <!-- <el-form-item label="商品默认主图">
						<material-select :limit="1" v-model="goodsSettings.image" />
						<div class="flex">
							<div class="muted xs m-r-16">建议尺寸：800*800像素，支持jpg，jpeg，png格式</div>
							<el-popover placement="right" width="200" trigger="hover">
								<el-image
									src="https://img2.baidu.com/it/u=3357699356,1912406716&fm=26&fmt=auto&gp=0.jpg" />
								<el-button slot="reference" type="text">查看示例</el-button>
							</el-popover>
						</div>
					</el-form-item> -->
                    <el-form-item label="商品详情显示库存">
                        <div class="flex">
                            <el-switch
                                v-model="goodsSettings.is_show"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ goodsSettings.is_show ? '显示' : '不显示' }}</span>
                        </div>
                        <div class="muted xs">商品详情显示剩余库存数量，默认不显示</div>
                    </el-form-item>
                    <el-form-item label="商品划线价显示">
                        <div class="flex">
                            <el-switch
                                v-model="goodsSettings.show_price"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ goodsSettings.show_price ? '显示' : '不显示' }}</span>
                        </div>
                        <div class="muted xs">默认显示，设置为不显示则商城的商品都不显示划线价，包括列表页</div>
                        <div class="muted xs">注：划线价实际是原价</div>
                    </el-form-item>
                    <el-form-item label="销量显示">
                        <div class="flex">
                            <el-switch
                                v-model="goodsSettings.sales_num"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ goodsSettings.sales_num ? '显示' : '不显示' }}</span>
                        </div>
                        <div class="muted xs">默认显示，设置为不显示则商城的商品都不显示销量，包括列表页</div>
                    </el-form-item>
                    <el-form-item label="浏览量显示">
                        <div class="flex">
                            <el-switch
                                v-model="goodsSettings.click_num"
                                :active-value="1"
                                :inactive-value="0"
                                :active-color="styleConfig.primary"
                                inactive-color="#f4f4f5"
                            />
                            <span class="m-l-16">{{ goodsSettings.click_num ? '显示' : '不显示' }}</span>
                        </div>
                        <div class="muted xs">默认显示，设置为不显示则商城的商品都不显示浏览量，包括列表页</div>
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <!--  表单功能键  -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <!-- <el-button size="small" @click="$router.go(-1)">取消</el-button> -->
                <el-button size="small" type="primary" @click="putGoodsSettings()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiGoodsSettings, apiGoodsSettingsAdd } from '@/api/setting/goods'
import { GoodsSettings_Req } from '@/api/setting/goods.d.ts'
@Component({
    components: {
        MaterialSelect
    }
})
export default class SettingGoods extends Vue {
    /** S Data **/
    // 表单数据
    goodsSettings: GoodsSettings_Req = {
        image: '', //商品主图
        is_show: 0, //是否显示:0-否;1-是;
        show_price: 0, //是否显示:0-否;1-是;
        sales_num: 0, //是否显示:0-否;1-是;
        click_num: 0 //是否显示:0-否;1-是;
    }
    /** E Data **/

    // 获取商品设置
    getGoodsSettings() {
        apiGoodsSettings()
            .then((res: any) => {
                this.goodsSettings = res
            })
            .catch(() => {})
    }
    // 修改商品设置
    putGoodsSettings() {
        apiGoodsSettingsAdd(this.goodsSettings)
            .then((res: any) => {
                this.getGoodsSettings()
            })
            .catch(() => {})
    }
    /** S Life Cycle **/
    created() {
        this.getGoodsSettings()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-card {
    .ls-input {
        width: 280px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }

    .login_limit-unit {
        display: inline-block;
        width: 2em;
        text-align: center;
    }
}
</style>
