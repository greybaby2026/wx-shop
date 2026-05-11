<template>
    <div class="ls-add-admin">
        <!-- 分销设置 -->
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">分销设置</div>
            <el-form ref="list" :model="list" label-width="120px" size="small">
                <el-form-item label="分销功能" prop="switch">
                    <el-radio v-model="list.switch" :label="typeof list.switch == 'number' ? 0 : '0'">关闭</el-radio>
                    <el-radio v-model="list.switch" :label="typeof list.switch == 'number' ? 1 : '1'">开启</el-radio>
                    <span class="desc">关闭分销功能时，不会再产生新的分销佣金，商城分销入口会关闭.</span>
                </el-form-item>

                <el-form-item label="分销层级" prop="level">
                    <el-radio v-model="list.level" :label="typeof list.level == 'number' ? 1 : '1'">一级分销</el-radio>
                    <el-radio v-model="list.level" :label="typeof list.level == 'number' ? 2 : '2'">二级分销</el-radio>
                    <span class="desc">允许发放佣金的分销层级，等级默认佣金比例在 分销等级 进行设置</span>
                </el-form-item>

                <el-form-item label="分销自购返佣" prop="self">
                    <el-radio v-model="list.self" :label="typeof list.self == 'number' ? 0 : '0'"> 关闭 </el-radio>
                    <el-radio v-model="list.self" :label="typeof list.self == 'number' ? 1 : '1'"> 开启 </el-radio>
                    <span class="desc">开启后，分销商自购时可以获得自购返佣</span>
                </el-form-item>
                <el-form-item label="商品详情显示佣金" prop="is_show_earnings">
                    <el-radio
                        v-model="list.is_show_earnings"
                        :label="typeof list.is_show_earnings == 'number' ? 1 : '1'"
                    >
                        显示
                    </el-radio>
                    <el-radio
                        v-model="list.is_show_earnings"
                        :label="typeof list.is_show_earnings == 'number' ? 0 : '0'"
                    >
                        隐藏
                    </el-radio>
                    <span class="desc">是否在商品详情显示佣金奖励提示</span>
                </el-form-item>

                <el-form-item label="详情页佣金可见用户" prop="show_earnings_scope">
                    <el-radio
                        v-model="list.show_earnings_scope"
                        :label="typeof list.show_earnings_scope == 'number' ? 0 : '0'"
                    >
                        全部用户
                    </el-radio>
                    <el-radio
                        v-model="list.show_earnings_scope"
                        :label="typeof list.show_earnings_scope == 'number' ? 1 : '1'"
                    >
                        分销商
                    </el-radio>
                    <span class="desc">选择全部用户，则所有人在商品详情都可以看到佣金奖励提示</span>
                </el-form-item>
                <!-- <el-form-item label="分销海报背景图" prop="poster">
                    <material-select
                        :limit="1"
                        :disabled="false"
                        v-model="list.poster"
                    />
                    <span class="desc"
                        >分销海报建议图片尺寸：600*960像素，底部留白：600*272像素</span
                    >
                </el-form-item> -->
            </el-form>
        </div>

        <!-- 分销资格 -->
        <div class="ls-card ls-coupon-edit__form m-t-10">
            <div class="nr weight-500 m-b-20">分销资格</div>

            <el-form ref="list" :model="list" label-width="120px" size="small" class="m-t-15">
                <el-form-item label="开通分销商条件" prop="send_total_type">
                    <el-radio v-model="list.open" :label="typeof list.open == 'number' ? 1 : '1'">无条件</el-radio>
                    <el-radio v-model="list.open" :label="typeof list.open == 'number' ? 2 : '2'">申请分销</el-radio>
                    <el-radio v-model="list.open" :label="typeof list.open == 'number' ? 3 : '3'">指定分销</el-radio>
                    <span class="desc" v-if="list.open == 1">
                        开通分销商条件切换至无条件时，所有用户都将开通分销商资格。
                    </span>
                    <span class="desc" v-if="list.open == 2">
                        用户需在前端提交分销申请，后台管理员同意后即可成为分销商。
                    </span>
                    <span class="desc" v-if="list.open == 3">
                        指定某个用户成为分销商。在【分销商】-【开通分销商】选择某个用户即可
                    </span>
                </el-form-item>

                <el-form-item label="申请页顶部宣传图" prop="send_total_type">
                    <material-select :limit="1" :disabled="false" v-model="list.apply_image" />
                    <div class="desc">不上传则显示系统默认背景图，建议尺寸750*280</div>
                </el-form-item>

                <el-form-item label="申请协议" prop="send_total_type">
                    <el-radio v-model="list.protocol_show" :label="typeof list.protocol_show == 'number' ? 0 : '0'">
                        隐藏
                    </el-radio>
                    <el-radio v-model="list.protocol_show" :label="typeof list.protocol_show == 'number' ? 1 : '1'">
                        显示
                    </el-radio>
                </el-form-item>

                <el-form-item label="申请协议内容" prop="send_total_type">
                    <ls-editor width="200" height="400" v-model="list.protocol_content" />
                </el-form-item>
            </el-form>
        </div>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer row-center flex">
            <div class="row-center flex">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit('form')">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import LsEditor from '@/components/editor.vue'
import lsDialog from '@/components/ls-dialog.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import { Component, Vue } from 'vue-property-decorator'
import { apiDistributionDetails, apiDistributionSet } from '@/api/distribution/distribution'
@Component({
    components: {
        MaterialSelect,
        lsDialog,
        LsEditor
    }
})
export default class DistributionGradeEdit extends Vue {
    /** S Data **/

    list: any = {}

    /** E Data **/

    /** S Methods **/

    onSubmit() {
        apiDistributionSet({ ...this.list })
            .then(() => {
                this.detail()
                this.$message.success('修改成功!')
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    // 详情
    detail() {
        apiDistributionDetails({})
            .then(res => {
                this.list = res
            })
            .catch(() => {
                this.$message.error('数据获取失败!')
            })
    }

    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.detail()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped>
.ls-add-admin {
    padding-bottom: 80px;

    .ls-input {
        width: 380px;
    }

    .desc {
        display: block;
        color: #999;
        font-size: 12px;
    }
}
</style>
