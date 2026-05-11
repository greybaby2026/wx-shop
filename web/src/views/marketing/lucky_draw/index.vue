<!--  -->
<template>
    <div class="lucky-draw">
        <!-- 顶部 -->
        <div class="ls-card header">
            <el-alert
                class="xxl"
                title="温馨提示：进行中的积分抽奖活动可以修改名称和活动时间。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>

            <div class="m-t-16">
                <el-form ref="form" inline :model="form" label-width="70px" size="small">
                    <el-form-item label="活动名称">
                        <el-input class="" v-model="form.name" placeholder="请输入活动名称"> </el-input>
                    </el-form-item>

                    <el-form-item label="活动时间">
                        <date-picker :start-time.sync="form.start_time" :end-time.sync="form.end_time" />
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset()">重置</el-button>
                    <!-- 导出按钮 -->
                    <export-data class="m-l-10" :method="apiLuckyDrawLists" :param="form" :pageSize="pager._size">
                    </export-data>
                </el-form>
            </div>
        </div>

        <!--  -->
        <div class="ls-card m-t-16">
            <el-tabs v-model="form.status" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane :label="`全部(${extend.all})`" name=" ">
                    <lucky-draw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`未开始(${extend.wait})`" name="0">
                    <lucky-draw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`进行中(${extend.ing})`" name="1">
                    <lucky-draw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`已结束(${extend.end})`" name="2">
                    <lucky-draw-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { RequestPaging } from '@/utils/util'
import { apiLuckyDrawLists, apiLuckyDrawDelete, apiLuckyDrawEnd, apiLuckyDrawStart } from '@/api/marketing/lucky_draw'
import { PageMode } from '@/utils/type'
import LuckyDrawPane from '@/components/lucky-draw/lucky-draw-pane.vue'
import DatePicker from '@/components/date-picker.vue'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LuckyDrawPane,
        DatePicker,
        ExportData
    }
})
export default class LuckyDraw extends Vue {
    apiLuckyDrawLists = apiLuckyDrawLists // 传递给导出组件的api

    // 分页查询
    pager: RequestPaging = new RequestPaging()

    form = {
        status: ' ', // 活动状态 0-未开始 1-进行中 2-已结束，默认：全部
        name: '',
        start_time: '',
        end_time: ''
    }

    extend = {
        all: 0,
        wait: 0,
        ing: 0,
        end: 0
    }

    // 重置
    onReset() {
        ;(this.form.status = ' '),
            (this.form.name = ''),
            (this.form.start_time = ''),
            (this.form.end_time = ''),
            this.getList()
    }

    // 获取列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiLuckyDrawLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {
                this.extend = res?.extend
            })
            .catch(() => {})
    }

    created() {
        this.getList()
    }
}
</script>

<style lang="scss" scoped>
.lucky-draw {
    .header {
        padding-bottom: 0px;
    }
}
</style>
