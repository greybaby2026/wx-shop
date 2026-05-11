<template>
    <div class="ls-evaluation-edit">
        <div class="ls-card ls-evaluation-edit__header">
            <el-page-header @back="$router.go(-1)" content="添加评价"></el-page-header>
        </div>

        <div class="ls-card ls-evaluation-edit__form m-t-10">
            <el-form ref="form" :model="form" label-width="120px" size="small" :rules="rules">
                <el-form-item label="会员头像" prop="avatar">
                    <material-select v-model="form.avatar" :limit="1" />
                </el-form-item>
                <el-form-item label="会员昵称" prop="nickname">
                    <el-input v-model="form.nickname" placeholder="请输入会员昵称"></el-input>
                </el-form-item>

                <el-form-item label="会员等级" prop="level_id">
                    <el-select v-model="form.level_id" placeholder="请选择会员等级">
                        <el-option
                            v-for="(item, index) in levelList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="评价时间" prop="comment_time">
                    <el-date-picker
                        v-model="form.comment_time"
                        value-format="yyyy-MM-dd HH:mm:ss"
                        type="datetime"
                        placeholder="请选择评价时间"
                    ></el-date-picker>
                </el-form-item>
                <el-form-item label="评价等级" prop="goods_comment">
                    <div class="m-t-10">
                        <el-rate
                            size="large"
                            show-text
                            v-model="form.goods_comment"
                            :texts="['差评', '差评', '中评', '中评', '好评']"
                        ></el-rate>
                    </div>
                </el-form-item>
                <el-form-item label="评价图片">
                    <material-select v-model="form.comment_images" :limit="6" />
                    <div class="muted">支持上传多张</div>
                </el-form-item>
                <el-form-item label="评价内容">
                    <el-input
                        v-model="form.comment"
                        type="textarea"
                        style="width: 460px"
                        rows="6"
                        placeholder="请输入评价内容"
                    ></el-input>
                </el-form-item>
            </el-form>
        </div>
        <div class="ls-evaluation-edit__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" @click="$router.go(-1)">取消</el-button>
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import MaterialSelect from '@/components/material-select/index.vue'
import { apiGoodsCommentAssistantAdd } from '@/api/goods'
import { timeFormat } from '@/utils/util'
import { apiUserLevelList } from '@/api/user/user'
@Component({
    components: {
        MaterialSelect
    }
})
export default class AddEvaluation extends Vue {
    $refs!: { form: any }
    id!: any
    loading = false
    levelList: any[] = []
    form = {
        goods_id: '',
        goods_comment: 5,
        comment: '',
        avatar: '',
        nickname: '',
        level_id: '',
        comment_time: '',
        comment_images: ''
    }

    rules = {
        avatar: [
            {
                required: true,
                message: '请添加会员头像',
                trigger: ['blur', 'change']
            }
        ],
        nickname: [
            {
                required: true,
                message: '请输入用户昵称',
                trigger: ['blur', 'change']
            }
        ],
        level_id: [
            {
                required: true,
                message: '请选择会员等级',
                trigger: ['blur', 'change']
            }
        ],
        comment_time: [
            {
                required: true,
                message: '请选择评价时间',
                trigger: ['blur', 'change']
            }
        ],
        goods_comment: [
            {
                required: true,
                message: '请选择评价等级',
                trigger: ['blur', 'change']
            }
        ]
    }

    handleSave() {
        this.$refs.form.validate((valid: boolean, object: any) => {
            this.form.goods_id = this.$route.query.goods_id as string
            if (valid) {
                apiGoodsCommentAssistantAdd(this.form).then(() => {
                    this.$router.go(-1)
                })
            } else {
                return false
            }
        })
    }

    getUserLevel() {
        apiUserLevelList({
            page_type: 0
        }).then(res => {
            this.levelList = res.lists
        })
    }

    created() {
        this.getUserLevel()
    }
}
</script>

<style lang="scss" scoped>
.ls-evaluation-edit {
    padding-bottom: 80px;
}
</style>
