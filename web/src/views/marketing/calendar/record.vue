<template>
    <div>
        <div class="ls-card">
            <div class="m-t-16">
                <!-- 头部表单 -->
                <el-form ref="form" inline :model="SearchData" label-width="100px" size="small">
                    <el-form-item label="用户昵称">
                        <el-input style="width: 280px" v-model="SearchData.nickname" placeholder="请输入用户昵称">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="用户手机号">
                        <el-input style="width: 280px" v-model="SearchData.mobile" placeholder="请输入用户手机号">
                        </el-input>
                    </el-form-item>

                    <el-form-item label="" class="m-l-6">
                        <el-button size="small" type="primary" @click="getRecord(1)">查询</el-button>
                        <el-button size="small" @click="resetSearchData">重置</el-button>

                        <export-data
                            class="m-l-10"
                            :pageSize="pager.size"
                            :method="apiCalendarRecord"
                            :param="SearchData"
                        ></export-data>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="m-t-16 ls-card">
            <!-- 数据表格 -->
            <el-table ref="paneTable" :data="pager.lists" v-loading="pager.loading" style="width: 100%" size="mini">
                <el-table-column label="用户昵称" min-width="120">
                    <template slot-scope="scope">
                        <div class="flex">
                            <img :src="scope.row.avatar" alt="" style="width: 34px; height: 34px" />
                            <span class="m-l-10">{{ scope.row.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="integral" label="每日签到奖励" width=""> </el-table-column>
                <el-table-column prop="days" label="连续签到天数" width=""> </el-table-column>
                <el-table-column prop="continuous_integral" label="连续签到奖励" width=""> </el-table-column>
                <el-table-column prop="create_time" label="签到时间" min-width="120"> </el-table-column>
            </el-table>

            <div class="m-t-16 flex row-right">
                <ls-pagination v-model="pager" @change="getRecord()"></ls-pagination>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
// 订单头部筛选未完成
import { Component, Vue } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { RequestPaging } from '@/utils/util'
import { apiCalendarRecord } from '@/api/marketing/calendar'
import ExportData from '@/components/export-data/index.vue'
@Component({
    components: {
        LsPagination,
        LsDialog,
        ExportData
    }
})
export default class CalendarRecord extends Vue {
    /** S Data **/
    $refs!: { paneTable: any }

    apiCalendarRecord = apiCalendarRecord

    SearchData = {
        nickname: '',
        sn: '',
        mobile: ''
    }

    pager = new RequestPaging()

    /** E Data **/

    /** S Method **/

    // 获取充值记录数据
    getRecord(page?: number): void {
        page && (this.pager.page = page)
        this.pager.request({
            callback: apiCalendarRecord,
            params: {
                ...this.SearchData
            }
        })
    }

    // 重置搜索领取记录
    resetSearchData() {
        Object.keys(this.SearchData).map(key => {
            this.$set(this.SearchData, key, '')
        })
        this.getRecord()
    }

    /** E Method **/

    created() {
        this.getRecord()
    }
}
</script>

<style lang="scss" scoped>
img {
    width: 50px;
    height: 50px;
}
</style>
