<!-- 用户提现 -->
<template>
    <div class="user-withdrawal">
        <div class="ls-card">
            <el-alert
                class="xxl"
                title="温馨提示： 1.进行中的砍价商品可以修改名称和活动时间。"
                type="info"
                :closable="false"
                show-icon
            >
            </el-alert>
            <div class="journal-search m-t-16">
                <el-form ref="formRef" inline :model="form" label-width="70px" size="small" class="ls-form">
                    <el-form-item label="活动信息" prop="sn">
                        <el-input v-model="form.activity_info" placeholder="请输入活动名称/活动编号查询"></el-input>
                    </el-form-item>
                    <el-form-item label="商品信息">
                        <el-input v-model="form.goods_info" placeholder="请输入商品名称/编号查询"></el-input>
                    </el-form-item>
                    <!-- <el-form-item label="活动状态" prop="status">
						<el-select v-model="form.status" placeholder="全部">
							<el-option label="全部" value=" "></el-option>
							<el-option label="未开始" value="1"></el-option>
							<el-option label="进行中" value="2"></el-option>
							<el-option label="已结束" value="3"></el-option>
						</el-select>
					</el-form-item> -->
                    <el-form-item label="活动时间">
                        <el-date-picker
                            v-model="tableData"
                            type="datetimerange"
                            align="right"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始时间"
                            end-placeholder="结束时间"
                            :picker-options="pickerOptions"
                            @change="splitTime"
                            value-format="yyyy-MM-dd HH:mm:ss"
                        >
                        </el-date-picker>
                    </el-form-item>

                    <el-button size="small" type="primary" @click="getList(1)">查询</el-button>
                    <el-button size="small" @click="onReset">重置</el-button>
                </el-form>
            </div>
        </div>

        <div class="ls-withdrawal__centent ls-card m-t-16">
            <el-tabs v-model="form.status" v-loading="pager.loading" @tab-click="getList(1)">
                <el-tab-pane :label="`全部(${tabCount.all})`" name=" ">
                    <bargain-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane :label="`未开始(${tabCount.wait})`" name="1">
                    <bargain-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`进行中(${tabCount.ing})`" name="2">
                    <bargain-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
                <el-tab-pane lazy :label="`已结束(${tabCount.end})`" name="3">
                    <bargain-pane v-model="pager.lists" :pager="pager" @refresh="getList()" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator'
import { apiBargainActivityLists } from '@/api/marketing/bargain'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
import BargainPane from '@/components/marketing/bargain/bargain-pane.vue'
@Component({
    components: {
        LsPagination,
        BargainPane
    }
})
export default class BargainLists extends Vue {
    /** S Data **/
    // 日期选择器
    pickerOptions = {
        shortcuts: [
            {
                text: '最近一周',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近一个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
                    picker.$emit('pick', [start, end])
                }
            },
            {
                text: '最近三个月',
                onClick(picker: any) {
                    const end = new Date()
                    const start = new Date()
                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
                    picker.$emit('pick', [start, end])
                }
            }
        ]
    }
    tableData = []
    // 顶部查询表单
    pager: RequestPaging = new RequestPaging()
    // 顶部查询表单
    form = {
        status: ' ', // 砍价活动状态
        start_time: '',
        end_time: '',
        activity_info: '', // 砍价活动
        goods_info: '' // 商品信息
    }
    // 各状态数量
    tabCount = {
        all: 0,
        end: 0,
        ing: 0,
        wait: 0
    }
    /** E Data **/

    /** S Methods **/
    splitTime() {
        if (this.tableData != null) {
            this.form.start_time = this.tableData[0]
            this.form.end_time = this.tableData[1]
        }
    }
    // 重置
    onReset() {
        this.tableData = []
        this.form.status = ' '
        this.form.start_time = ''
        this.form.end_time = ''
        this.form.activity_info = '' // 砍价活动
        this.form.goods_info = '' // 商品信息

        this.getList()
    }
    // 提现列表
    getList(page?: number): void {
        page && (this.pager.page = page)
        this.pager
            .request({
                callback: apiBargainActivityLists,
                params: {
                    ...this.form
                }
            })
            .then((res: any) => {
                //
                this.tabCount = res?.extend
            })
    }
    /** E Methods **/

    /** S Life Cycle **/
    created() {
        this.getList()
    }
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
