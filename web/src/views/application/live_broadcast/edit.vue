<!-- 新增、编辑 -->
<template>
    <div class="live-broadcast-edit">
        <!-- 导航头部 -->
        <div class="ls-card">
            <el-page-header @back="$router.go(-1)" :content="mode === 'add' ? '新建直播间' : ''" />
        </div>

        <!-- 主要内容 -->
        <el-form :rules="formRules" ref="formRef" :model="form" label-width="120px" size="small">
            <div class="ls-card m-t-16">
                <div class="card-content m-t-24">
                    <el-form-item label="直播间标题" prop="name">
                        <el-input v-model="form.name" placeholder="请输入直播标题"></el-input>
                    </el-form-item>
                    <el-form-item label="直播类型" prop="type">
                        <el-radio-group class="m-r-16" v-model="form.type">
                            <el-radio class="m-r-16" :label="0">手机直播</el-radio>
                        </el-radio-group>
                        <div class="muted xs m-r-16">通过“小程序直播/推流设备”开播</div>
                    </el-form-item>

                    <el-form-item label="开播时间" prop="">
                        <el-date-picker
                            v-model="start"
                            :picker-options="pickerOptions"
                            type="datetime"
                            placeholder="请输入开播时间"
                            align="right"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        >
                        </el-date-picker>
                        <div class="muted xs m-r-16">
                            填写直播开始时间，开播时间需要在当前时间的10分钟后并且不能在6个月后
                        </div>
                    </el-form-item>
                    <el-form-item label="结束时间" prop="">
                        <el-date-picker
                            v-model="end"
                            :picker-options="pickerOptions"
                            type="datetime"
                            placeholder="请输入结束时间"
                            align="right"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        >
                        </el-date-picker>
                        <div class="muted xs m-r-16">开播时间和结束时间间隔不得短于30分钟，不得超过24小时</div>
                    </el-form-item>
                    <el-form-item label="主播昵称" prop="anchor_name">
                        <el-input v-model="form.anchor_name" placeholder="请输入主播昵称"></el-input>
                    </el-form-item>
                    <el-form-item label="主播微信号" prop="anchor_wechat">
                        <el-input v-model="form.anchor_wechat" placeholder="请输入主播微信号"></el-input>
                        <div class="muted xs m-r-16">
                            每个直播间需要绑定主播微信号身份，用以核实主播身份，不会展示给观众。
                        </div>
                    </el-form-item>
                    <el-form-item label="分享卡片封面" prop="share_img">
                        <!-- <material-select :limit="1" v-model="form.share_img" /> -->
                        <live-upload @setImage="setShareImg"></live-upload>
                        <div class="muted xs m-r-16">用户在微信对话框内分享的直播间将以分享卡片的形式呈现。</div>
                        <div class="muted xs m-r-16">建议尺寸：800像素 * 640像素，图片大小不得超过1M。</div>
                    </el-form-item>
                    <el-form-item label="直播卡片封面" prop="feeds_img">
                        <!-- <material-select :limit="1" v-model="form.feeds_img" /> -->
                        <live-upload @setImage="setFeedsImg"></live-upload>
                        <div class="muted xs m-r-16">图片建议大小为 800像素 * 800像素。图片大小不超过300KB。</div>
                    </el-form-item>
                    <el-form-item label="直播间背景墙" prop="cover_img">
                        <!-- <material-select :limit="1" v-model="form.cover_img" /> -->
                        <live-upload @setImage="setCoverImg"></live-upload>
                        <div class="muted xs m-r-16">
                            直播间背景墙是每个直播间的默认背景。 建议尺寸：600像素 * 1300像素，图片大小不得超过3M
                        </div>
                    </el-form-item>
                    <el-form-item label="直播间功能" prop="">
                        <el-checkbox v-model="form.close_like" :true-label="0" :false-label="1">开启点赞</el-checkbox>
                        <el-checkbox v-model="form.close_goods" :true-label="0" :false-label="1">开启货架</el-checkbox>
                        <el-checkbox v-model="form.close_comment" :true-label="0" :false-label="1"
                            >开启评论</el-checkbox
                        >
                        <el-checkbox v-model="form.close_replay" :true-label="0" :false-label="1">开启回放</el-checkbox>
                        <el-checkbox v-model="form.close_share" :true-label="0" :false-label="1">开启分享</el-checkbox>
                        <el-checkbox v-model="form.close_kf" :true-label="0" :false-label="1">开启客服</el-checkbox>
                        <el-checkbox v-model="form.is_feeds_public" :true-label="1" :false-label="0"
                            >开启官方收录
                        </el-checkbox>
                    </el-form-item>
                </div>
            </div>
        </el-form>

        <!-- 底部保存或取消 -->
        <div class="bg-white ls-fixed-footer">
            <div class="row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="onSubmit()">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { PageMode } from '@/utils/type'
import MaterialSelect from '@/components/material-select/index.vue'
import LiveUpload from '@/components/live-broadcast/live-upload.vue'
import { apiLiveRoomAdd } from '@/api/application/live_broadcast'
import { throttle } from '@/utils/util'
@Component({
    components: {
        MaterialSelect,
        LiveUpload
    }
})
export default class LiveBroadcastEdit extends Vue {
    pickerOptions = {
        shortcuts: [
            {
                text: '今天',
                onClick(picker: any) {
                    picker.$emit('pick', new Date())
                }
            },
            {
                text: '昨天',
                onClick(picker: any) {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24)
                    picker.$emit('pick', date)
                }
            },
            {
                text: '一周前',
                onClick(picker: any) {
                    const date = new Date()
                    date.setTime(date.getTime() - 3600 * 1000 * 24 * 7)
                    picker.$emit('pick', date)
                }
            }
        ]
    }

    mode: string = PageMode.ADD // 当前页面【add: 添加 | edit: 编辑】

    form = {
        type: 0, //【1: 推流，0：手机直播】
        name: '', // 直播间名称
        start_time: 0, // 直播开始时间
        end_time: 0, // 直播结束时间
        anchor_name: '', // 主播名称
        anchor_wechat: '', // 主播微信号 wapftp
        // sub_anchor_wechat: 'wapftp', // 主播副号微信号
        cover_img: '', // 背景图（调用上传素材接口， 拿返回的media_id）
        share_img: '', // 分享图（调用上传素材接口， 拿返回的media_id）
        feeds_img: '', // 购物直播频道封面图（调用上传素材接口， 拿返回的media_id）
        is_feeds_public: 0, // 是否开启官方收录 【1: 开启，0：关闭】，默认开启收录
        close_like: 0, // 是否关闭点赞 【0：开启，1：关闭】（若关闭，观众端将隐藏点赞按钮，直播开始后不允许开启）
        close_goods: 0, // 是否关闭货架 【0：开启，1：关闭】（若关闭，观众端将隐藏商品货架，直播开始后不允许开启）
        close_comment: 0, // 是否关闭评论 【0：开启，1：关闭】（若关闭，观众端将隐藏评论入口，直播开始后不允许开启）
        close_replay: 0, // 是否关闭回放 【0：开启，1：关闭】默认关闭回放（直播开始后允许开启）
        close_share: 0, // 是否关闭分享 【0：开启，1：关闭】默认开启分享（直播开始后不允许修改）
        close_kf: 0 // 是否关闭客服 【0：开启，1：关闭】 默认关闭客服（直播开始后允许开启）
    }

    start = '' // 直播开始时间
    end = '' // 直播结束时间

    formRules = {
        type: [
            {
                required: true,
                message: '请选择直播类型',
                trigger: 'change'
            }
        ],
        name: [
            {
                required: true,
                message: '请输入直播标题',
                trigger: 'blur'
            }
        ],
        start_time: [
            {
                required: true,
                message: '请选择直播开始时间',
                trigger: 'change'
            }
        ],
        end_time: [
            {
                required: true,
                message: '请选择直播结束时间',
                trigger: 'change'
            }
        ],
        anchor_name: [
            {
                required: true,
                message: '请输入主播昵称',
                trigger: 'blur'
            }
        ],
        anchor_wechat: [
            {
                required: true,
                message: '请输入主播微信号',
                trigger: 'blur'
            }
        ],
        cover_img: [
            {
                required: true,
                message: '请选择直播间背景墙图片',
                trigger: 'blur'
            }
        ],
        share_img: [
            {
                required: true,
                message: '请选择分享卡片封面图片',
                trigger: 'blur'
            }
        ],
        feeds_img: [
            {
                required: true,
                message: '请选择直播卡片封面图片',
                trigger: 'blur'
            }
        ]
    }
    $refs!: {
        formRef: any
    }

    setCoverImg(val: any) {
        this.form.cover_img = val
    }
    setShareImg(val: any) {
        this.form.share_img = val
    }
    setFeedsImg(val: any) {
        this.form.feeds_img = val
    }

    // 表单提交
    onSubmit() {
        // 验证表单格式是否正确
        this.$refs.formRef.validate((valid: boolean): any => {
            if (!valid) {
                return
            }
            // 请求发送
            // switch (this.mode) {
            // 	case PageMode['ADD']:
            // 		return this.handleUserLabelAdd()
            // 	case PageMode['EDIT']:
            // 		return this.handleUserLabelEdit()
            // }
            this.liveRoomAdd()
        })
    }

    // 创建直播间
    liveRoomAdd() {
        const dateStart = new Date(this.start)
        this.form.start_time = dateStart.getTime() / 1000
        const dateEnd = new Date(this.end)
        this.form.end_time = dateEnd.getTime() / 1000

        apiLiveRoomAdd(this.form)
            .then((res: any) => {
                setTimeout(() => this.$router.go(-1), 500)
            })
            .catch((err: any) => {})
    }

    created() {
        this.onSubmit = throttle(this.onSubmit, 1000)
    }
}
</script>

<style lang="scss" scoped>
.live-broadcast-edit {
    min-height: calc(100vh - #{$--header-height} - 92px);
    margin-bottom: 60px;

    &__header {
        flex: none;
    }

    .ls-card {
        .card-title {
            font-size: 14px;
            font-weight: 500;
        }
    }
}
</style>
