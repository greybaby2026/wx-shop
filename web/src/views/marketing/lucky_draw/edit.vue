<template>
    <div class="lucky-draw-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header v-if="type == 'details'" @back="$router.go(-1)" content="抽奖活动详情" />
            <el-page-header v-else @back="$router.go(-1)" :content="mode === 'add' ? '新增积分活动' : '编辑积分活动'" />
        </div>

        <el-card shadow="never">
            <div style="display: flex">
                <div style="flex: 1">
                    <!-- 主要内容 -->
                    <el-form
                        :rules="formRules"
                        ref="formRef"
                        :model="form"
                        label-width="120px"
                        size="small"
                        :disabled="type == 'details'"
                    >
                        <el-tabs v-model="tabsValue">
                            <el-tab-pane label="基础设置" :name="1">
                                <el-form-item label="活动名称" prop="name">
                                    <el-input v-model="form.name" placeholder="请输入活动名称"></el-input>
                                </el-form-item>
                                <el-form-item label="活动时间" required>
                                    <date-picker-new
                                        type="datetimerange"
                                        :start-time.sync="form.start_time"
                                        :end-time.sync="form.end_time"
                                    />
                                </el-form-item>
                                <el-form-item label="活动备注" prop="remark">
                                    <el-input
                                        class="ls-input-textarea"
                                        v-model="form.remark"
                                        placeholder="请输入活动备注"
                                        type="textarea"
                                        :rows="3"
                                        :disabled="status == 1"
                                    >
                                    </el-input>
                                </el-form-item>
                            </el-tab-pane>
                            <el-tab-pane label="奖品设置" :name="2">
                                <el-form-item label="奖品设置" prop="prizes" required>
                                    <!-- 列表 -->
                                    <div class="list-table m-t-16">
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[0]"
                                                :index="0"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[0].image">
                                                        <el-image
                                                            :src="form.prizes[0].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[0].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[0].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[0].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[1]"
                                                :index="1"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[1].image">
                                                        <el-image
                                                            :src="form.prizes[1].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[1].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[1].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[1].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[2]"
                                                :index="2"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[2].image">
                                                        <el-image
                                                            :src="form.prizes[2].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[2].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[2].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[2].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[3]"
                                                :index="3"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[3].image">
                                                        <el-image
                                                            :src="form.prizes[3].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[3].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[3].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[3].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item list-item-none">
                                            <div class="list-item-title">奖励池</div>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[4]"
                                                :index="4"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[4].image">
                                                        <el-image
                                                            :src="form.prizes[4].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[4].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[4].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[4].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[5]"
                                                :index="5"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[5].image">
                                                        <el-image
                                                            :src="form.prizes[5].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[5].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[5].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[5].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[6]"
                                                :index="6"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[6].image">
                                                        <el-image
                                                            :src="form.prizes[6].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[6].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[6].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[6].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                        <div class="list-item">
                                            <ls-lucky-draw-change
                                                title="编辑奖品"
                                                :val="form.prizes[7]"
                                                :index="7"
                                                @setPrize="setPrize"
                                                :status="status"
                                                :mode="mode"
                                            >
                                                <div class="list-item-box" slot="trigger">
                                                    <div class="list-item-img" v-if="form.prizes[7].image">
                                                        <el-image
                                                            :src="form.prizes[7].image"
                                                            style="width: 54px; height: 54px"
                                                        >
                                                        </el-image>
                                                    </div>
                                                    <div class="list-item-title">
                                                        {{ form.prizes[7].name || '请选择' }}
                                                    </div>
                                                    <div v-if="form.prizes[7].type_desc === 4" class="list-item-desc">
                                                        剩余：{{ form.prizes[7].num }}
                                                    </div>
                                                </div>
                                            </ls-lucky-draw-change>
                                        </div>
                                    </div>
                                </el-form-item>

                                <el-form-item label="中奖概率" prop="probability">
                                    <el-input
                                        class="ls-input-textarea"
                                        v-model="form.probability"
                                        placeholder="请输入中奖概率"
                                        type="number"
                                        :rows="3"
                                    >
                                    </el-input>
                                    <p>1、各奖品的中奖概率由奖品数量决定，数量越少，中奖几率就越低</p>
                                    <p>2、单个奖品的中奖概率 =（该奖品剩余数量 / 总奖品剩余数量）× 设置的中奖概率</p>
                                </el-form-item></el-tab-pane
                            >
                            <el-tab-pane label="活动设置" :name="3">
                                <el-form-item label="抽奖权限" prop="auth_type">
                                    <el-radio v-model="form.auth_type" :label="0">全部用户</el-radio>
                                    <el-radio v-model="form.auth_type" :label="1">部分用户 </el-radio>
                                    <div v-show="form.auth_type === 1" style="width: 360px; margin-top: 12px">
                                        <el-select
                                            multiple
                                            style="width: 100%"
                                            :disabled="form.auth_type === 0"
                                            v-model="form.user_level"
                                            placeholder="请选择会员等级（多选）"
                                        >
                                            <el-option
                                                v-for="(item, index) in levelList"
                                                :key="index"
                                                :label="item.name"
                                                :value="item.id"
                                            ></el-option
                                        ></el-select>
                                    </div>
                                </el-form-item>
                                <el-form-item label="消耗积分" prop="need_integral">
                                    <el-input
                                        v-model="form.need_integral"
                                        placeholder="请输入消耗积分"
                                        :disabled="status == 1"
                                    ></el-input>
                                    <div class="muted xs">每次抽奖消耗的积分数量</div>
                                </el-form-item>

                                <el-form-item label="抽奖次数" prop="frequency_type">
                                    <!-- <el-radio-group class="m-r-16" v-model="form.frequency_type" :disabled="status == 1"> -->
                                    <div class="">
                                        <el-radio
                                            class="m-r-16"
                                            v-model="form.frequency_type"
                                            :label="0"
                                            :disabled="status == 1"
                                            >不限制抽奖次数</el-radio
                                        >
                                    </div>
                                    <div class="">
                                        <el-radio v-model="form.frequency_type" :label="1" :disabled="status == 1">
                                            <span class="m-r-5">每人每天抽奖不超过</span>
                                            <el-input
                                                class="ls-input"
                                                placeholder="请输入抽奖次数"
                                                v-model="form.frequency"
                                                :disabled="status == 1"
                                            >
                                            </el-input>
                                            <span class="m-l-5">次</span>
                                        </el-radio>
                                    </div>
                                    <!-- </el-radio-group> -->
                                </el-form-item>
                                <el-form-item label="活动说明" prop="describe">
                                    <el-input
                                        class="ls-input-textarea"
                                        v-model="form.describe"
                                        placeholder="请输入活动说明"
                                        type="textarea"
                                        :rows="3"
                                        :disabled="status == 1"
                                    >
                                    </el-input>
                                </el-form-item>
                                <el-form-item label="中奖名单" prop="show_winning_list">
                                    <div class="flex">
                                        <el-switch
                                            v-model="form.show_winning_list"
                                            :active-value="1"
                                            :inactive-value="0"
                                            :active-color="styleConfig.primary"
                                            inactive-color="#f4f4f5"
                                            :disabled="status == 1"
                                        />
                                        <span class="m-l-16">{{ form.show_winning_list ? '显示' : '隐藏' }}</span>
                                    </div>
                                </el-form-item></el-tab-pane
                            >
                            <el-tab-pane label="页面装修" :name="4">
                                <!-- <el-form-item label="抽奖规则" prop="rule_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.rule_image" />
                                        <p>建议图片尺寸132px * 132px</p>
                                    </div>
                                </el-form-item>
                                <el-form-item label="我的奖品" prop="prize_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.prize_image" />
                                        <p>建议图片尺寸120px * 120px</p>
                                    </div>
                                </el-form-item> -->
                                <el-form-item label="顶部图片" prop="top_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.top_image" />
                                        <p>建议图片尺寸750px * 440px</p>
                                    </div>
                                </el-form-item>
                                <el-form-item label="开始按钮" prop="start_button_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.start_button_image" />
                                        <p>建议图片尺寸160px * 160px</p>
                                    </div>
                                </el-form-item>
                                <!-- <el-form-item label="谢谢参与" prop="not_win_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.not_win_image" />
                                        <p>建议图片尺寸160px * 160px</p>
                                    </div>
                                </el-form-item> -->
                                <el-form-item label="奖品底图" prop="prize_base_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.prize_base_image" />
                                        <p>建议图片尺寸160px * 160px</p>
                                    </div>
                                </el-form-item>
                                <el-form-item label="容器" prop="container_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.container_image" />
                                        <p>建议图片尺寸600px * 600px</p>
                                    </div>
                                </el-form-item>
                                <el-form-item label="背景图片" prop="background_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.background_image" />
                                        <p>建议图片尺寸750px * 1900px</p>
                                    </div>
                                </el-form-item>
                            </el-tab-pane>
                            <el-tab-pane label="分享设置" :name="5">
                                <el-form-item label="分享封面" prop="share_image">
                                    <div class="flex">
                                        <material-select :limit="1" v-model="form.share_image" />
                                        <p>建议图片尺寸750px * 1900px</p>
                                    </div>
                                </el-form-item>
                                <el-form-item label="分享描述" prop="share_describe">
                                    <el-input
                                        v-model="form.share_describe"
                                        placeholder="请输入分享描述"
                                        type="textarea"
                                        :rows="3"
                                    >
                                    </el-input>
                                </el-form-item>
                            </el-tab-pane>
                        </el-tabs>
                    </el-form>
                </div>
                <div style="height: 100%; margin-left: 48px">
                    <div class="review-title">预览图</div>
                    <div class="review-box">
                        <div class="list-table review-list m-t-16">
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[0].image">
                                        <el-image :src="form.prizes[0].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[0].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[0].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[0].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[1].image">
                                        <el-image :src="form.prizes[1].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[1].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[1].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[1].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[2].image">
                                        <el-image :src="form.prizes[2].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[2].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[2].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[2].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[3].image">
                                        <el-image :src="form.prizes[3].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[3].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[3].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[3].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item list-item-none">
                                <div class="list-item-title">奖励池</div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[4].image">
                                        <el-image :src="form.prizes[4].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[4].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[4].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[4].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[5].image">
                                        <el-image :src="form.prizes[5].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[5].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[5].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[5].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[6].image">
                                        <el-image :src="form.prizes[6].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[6].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[6].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[6].num }}
                                    </div>
                                </div>
                            </div>
                            <div class="list-item">
                                <div class="list-item-box" slot="trigger">
                                    <div class="list-item-img" v-if="form.prizes[7].image">
                                        <el-image :src="form.prizes[7].image" style="width: 54px; height: 54px">
                                        </el-image>
                                    </div>
                                    <div class="list-item-title">
                                        {{ form.prizes[7].name || '请选择' }}
                                    </div>
                                    <div v-if="form.prizes[7].type_desc === 4" class="list-item-desc">
                                        剩余：{{ form.prizes[7].num }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </el-card>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="tabsValue = tabsValue - 1" :disabled="tabsValue === 1"
                    >上一步</el-button
                >
                <el-button size="small" type="primary" @click="onSubmit()" :disabled="type == 'details'">{{
                    tabsValue === 5 ? '保存' : '下一步'
                }}</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import {
    apiLuckyDrawEdit,
    apiLuckyDrawDetail,
    apiLuckyDrawAdd,
    apiLuckyDrawGetPrizeType
} from '@/api/marketing/lucky_draw'
import { PageMode } from '@/utils/type'
import LsPagination from '@/components/ls-pagination.vue'
import DatePickerNew from '@/components/date-picker-new.vue'
import MaterialSelect from '@/components/material-select/index.vue'
import LsLuckyDrawChange from '@/components/lucky-draw/ls-lucky-draw-change.vue'
import { deepClone } from '@/utils/util.ts'
import { apiUserLevelList } from '@/api/user/user'
@Component({
    components: {
        LsPagination,
        DatePickerNew,
        LsLuckyDrawChange,
        MaterialSelect
    }
})
export default class LuckyDrawEdit extends Vue {
    mode: string = PageMode.ADD // 当前页面【add: 添加 | edit: 编辑】
    identity: number | null = null // 当前编辑的ID  valid: mode = 'edit'

    status: number | null = null // 当前编辑的状态  valid: status = 0-未开始 1-进行中
    type = ''

    tabsValue = 1

    levelList: any[] = []

    prizeType = 0 //  0-未中奖; 1-积分; 2-优惠券; 3-余额;

    form: any = {
        name: '', // 活动名称
        start_time: '', // 开始时间，时间戳
        end_time: '', // 结束时间，时间戳
        need_integral: 0, // 需要消耗的积分
        frequency_type: 0, // 抽奖次数类型
        frequency: 0, // 抽奖次数
        rule: '', // 抽奖规则
        show_winning_list: 0, // 是否显示中奖名单
        remark: '', // 备注
        prizes: [{}], // 奖品
        auth_type: 0,
        top_image: this.$getImageUri('/resource/image/adminapi/default/luck_draw_default_top_banner.png'),
        start_button_image: this.$getImageUri('/resource/image/adminapi/default/luck_draw_default_start.png'),
        prize_base_image: this.$getImageUri('/resource/image/adminapi/default/luck_draw_default_reward_bg.png'),
        container_image: this.$getImageUri('/resource/image/adminapi/default/luck_draw_default_box_bg.png'),
        background_image: this.$getImageUri('/resource/image/adminapi/default/luck_draw_default_bg.png')
        // prizes.name	: '', // 奖品名称
        // prizes.image: '', // 奖品图片
        // prizes.type: '', // 奖品类型
        // prizes.type_value: '', // 奖品类型值
        // prizes.num: '', // 奖品数量
        // prizes.probability: '', // 中奖概率
        // prizes.tips: '', // 中奖提示
    }

    $refs!: {
        formRef: any
    }
    formRules = {
        name: [
            {
                required: true,
                message: '请输入活动名称',
                trigger: 'blur'
            }
        ],
        start_time: [
            {
                required: true,
                message: '请选择活动时间',
                trigger: 'change'
            }
        ],
        end_time: [
            {
                required: true,
                message: '请选择活动时间',
                trigger: 'change'
            }
        ],
        need_integral: [
            {
                required: true,
                message: '请输入消耗积分',
                trigger: 'blur'
            }
        ],
        frequency_type: [
            {
                required: true,
                message: '请选择抽奖次数',
                trigger: 'change'
            }
        ],
        rule: [
            {
                required: true,
                message: '请输入抽奖规则',
                trigger: 'blur'
            }
        ],
        show_winning_list: [
            {
                required: true,
                message: '请选择中奖名单是否隐藏',
                trigger: 'blur'
            }
        ],
        probability: [
            {
                required: true,
                message: '请输入中奖概率',
                trigger: 'blur'
            }
        ],
        auth_type: [
            {
                required: true,
                message: '请选择抽奖权限',
                trigger: 'blur'
            }
        ],
        user_level: [
            {
                required: true,
                message: '请选择抽奖权限',
                trigger: 'blur'
            }
        ],
        describe: [
            {
                required: true,
                message: '请输入活动说明',
                trigger: 'blur'
            }
        ],
        // rule_image: [
        //     {
        //         required: true,
        //         message: '请选择抽奖规则图片',
        //         trigger: 'blur'
        //     }
        // ],
        // prize_image: [
        //     {
        //         required: true,
        //         message: '请选择我的奖品图片',
        //         trigger: 'blur'
        //     }
        // ],
        top_image: [
            {
                required: true,
                message: '请选择顶部图片',
                trigger: 'blur'
            }
        ],
        start_button_image: [
            {
                required: true,
                message: '请选择开始按钮图片',
                trigger: 'blur'
            }
        ],
        // not_win_image: [
        //     {
        //         required: true,
        //         message: '请选择谢谢参与图片',
        //         trigger: 'blur'
        //     }
        // ],
        prize_base_image: [
            {
                required: true,
                message: '请选择奖品底图图片',
                trigger: 'blur'
            }
        ],
        container_image: [
            {
                required: true,
                message: '请选择容器图片',
                trigger: 'blur'
            }
        ],
        background_image: [
            {
                required: true,
                message: '请选择背景图片',
                trigger: 'blur'
            }
        ]
    }
    lists = [
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        },
        {
            name: '', // 奖品名称
            image: '', // 奖品图片
            type: 0, // 奖品类型 // 0-未中奖; 1-积分; 2-优惠券; 3-余额;
            type_value: 0, // 奖品值
            type_desc: '', // 奖品描述
            num: 0, // 奖品数量
            probability: 0, // 中奖概率
            tips: '', // 中奖提示
            status: '',
            probability_desc: 0
        }
    ]

    setPrize(obj: any, index: any) {
        // this.form.prizes[index] = deepClone(obj)
        this.$set(this.form.prizes, index, deepClone(obj))
        this.$forceUpdate()
    }

    checkPrizes() {
        let isPass = true
        // 验证礼品

        for (let i = 0; i < this.form.prizes.length; i++) {
            const type = (this.form.prizes[i] as any).type

            if ((this.form.prizes[i] as any).name == '') {
                this.$message.error(`请输入位置${i + 1}的奖品名称`)
                return (isPass = false)
            }
            if ((this.form.prizes[i] as any).image == '') {
                this.$message.error(`请选择位置${i + 1}的奖品图片`)
                return (isPass = false)
            }
            // if ((this.form.prizes[i] as any).tips == '') {
            //     this.$message.error(`请输入位置${i + 1}的抽中提示语`)
            //     return (isPass = false)
            // }
            if (type != 0 && !(this.form.prizes[i] as any).num) {
                this.$message.error(`请输入位置${i + 1}的奖品数量`)
                return (isPass = false)
            }
            // if (type != 0 && !(this.form.prizes[i] as any).probability) {
            //     this.$message.error(`请输入位置${i + 1}的中奖概率`)
            //     return (isPass = false)
            // }
            if (type != 0 && (this.form.prizes[i] as any).type_value == '') {
                this.$message.error(
                    `请输入位置${i + 1}的${type == 1 ? '积分' : type == 2 ? '优惠券' : type == 3 ? '余额' : ''}`
                )
                return (isPass = false)
            }
        }

        return isPass
    }

    onSubmit() {
        if (this.tabsValue === 5) {
            this.$refs.formRef.validate((valid: boolean): any => {
                if (!valid) {
                    console.log(this.form)
                    // []
                    const turnTabsOne = ['name', 'start_time', 'end_time', 'remark']
                    const turnTabsTwo = ['probability']
                    const turnTabsThree = ['need_integral', 'frequency_type', 'describe', 'show_winning_list']
                    const turnTabsFour = [
                        // 'rule_image',
                        // 'prize_image',
                        'top_image',
                        'start_button_image',
                        // 'not_win_image',
                        'prize_base_image',
                        'container_image',
                        'background_image',
                        'share_image'
                    ]
                    if (
                        turnTabsOne.reduce((prev, next: any) => {
                            return this.form[next] === '' || this.form[next] === undefined ? true : prev
                        }, false)
                    ) {
                        this.$message.error(`请完善基础设置信息`)
                        this.tabsValue = 1
                    } else if (
                        turnTabsTwo.reduce((prev, next: any) => {
                            return this.form[next] === '' || this.form[next] === undefined ? true : prev
                        }, false)
                    ) {
                        this.$message.error(`请完善奖品设置信息`)
                        this.tabsValue = 2
                    } else if (
                        turnTabsThree.reduce((prev, next: any) => {
                            return this.form[next] === '' || this.form[next] === undefined ? true : prev
                        }, false)
                    ) {
                        this.$message.error(`请完善活动设置信息`)
                        this.tabsValue = 3
                    } else if (
                        turnTabsFour.reduce((prev, next: any) => {
                            return this.form[next] === '' || this.form[next] === undefined ? true : prev
                        }, false)
                    ) {
                        this.$message.error(`请完善页面装修信息`)
                        this.tabsValue = 4
                    }
                    // const turnTabsOne = ['name', 'start_time', 'end_time', 'remark']
                    return
                }

                this.$nextTick(() => {
                    if (!this.checkPrizes()) {
                        return
                    }

                    // 提交请求
                    if (this.mode == PageMode.ADD) {
                        this.luckyDrawAdd()
                    } else if (this.mode == PageMode.EDIT) {
                        this.luckyDrawEdit()
                    }
                })
            })
        } else {
            this.tabsValue = this.tabsValue + 1
        }
    }

    luckyDrawDetail() {
        apiLuckyDrawDetail({
            id: this.identity
        }).then((res: any) => {
            res.start_time = res.start_time_desc
            res.end_time = res.end_time_desc

            this.form = res
        })
    }

    luckyDrawEdit() {
        apiLuckyDrawEdit({
            ...this.form,
            prizes: this.form.prizes.slice(0, 8) // 限制最多8个奖品
        })
            .then((res: any) => {
                setTimeout(() => {
                    this.$router.go(-1)
                }, 500)
            })
            .catch((err: any) => {})
    }

    luckyDrawAdd() {
        if (this.form.prizes.length < 8) {
            this.$message.error('请至少添加8个奖品')
            return
        }
        if (this.form.prizes.every((item: any) => item.type !== 0)) {
            this.$message.error('请至少添加一个未中奖奖品')
            return
        }
        apiLuckyDrawAdd({
            ...this.form,
            prizes: this.form.prizes.slice(0, 8) // 限制最多8个奖品
        })
            .then((res: any) => {
                setTimeout(() => {
                    this.$router.go(-1)
                }, 500)
            })
            .catch((err: any) => {})
    }

    getUserLevel() {
        apiUserLevelList({
            page_type: 0
        }).then(res => {
            this.levelList = res.lists
        })
    }

    created() {
        const query: any = this.$route.query

        if (query.mode) {
            this.mode = query.mode
        }

        // 编辑模式：初始化数据
        if (this.mode === PageMode.EDIT) {
            this.identity = query.id * 1
            this.status = query.status
            this.type = query.type
            this.luckyDrawDetail()
        } else {
            this.form.prizes = this.lists
        }
        this.getUserLevel()
    }
}
</script>

<style lang="scss" scoped>
.ls-card {
    margin-bottom: 16px;
    .ls-input {
        width: 180px;
    }

    .ls-input-textarea {
        width: 300px;
    }

    .card-title {
        font-size: 14px;
        font-weight: 500;
    }
}

.list-table {
    display: flex;
    width: 500px;
    height: 500px;
    background: #f3f3f3;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding: 0 12px;
    border-radius: 4px;
    .list-item {
        width: 30%;
        height: 30%;
        display: flex;
        border-radius: 4px;
        background: #fff;
        border: 1px solid #e5e7eb;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
    .list-item-box {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        .list-item-title {
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 12px;
            text-align: center;
        }
    }
    .list-item-none {
        background: transparent;
        border: none;
        cursor: default;
    }
}

.review-box {
    flex: 1;
    display: flex;
    height: 500px;
    justify-content: center;
    align-items: center;
    background: #f3f3f3;
    flex-direction: column;
    border-radius: 8px;
}
.review-title {
    font-size: 14px;
    font-weight: 500;
    color: #111827;
    margin-bottom: 48px;
    text-align: center;
}
.review-list {
    height: 320px;
    width: 320px;
    .list-item {
        width: 96px;
        height: 96px;
        display: flex;
        border-radius: 4px;
        background: #fff;
        border: 1px solid #e5e7eb;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: default;
    }
    .list-item-box {
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .list-item-none {
        background: transparent;
        border: none;
        cursor: default;
    }
}

.lucky-draw-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;
}
</style>
