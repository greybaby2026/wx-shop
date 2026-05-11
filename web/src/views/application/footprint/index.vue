<template>
    <div class="footprint-container">
        <!-- Header -->
        <div class="ls-card">
            <el-alert
                title="温馨提示：设置商城首页，商品详情页显示足迹气泡。营造活动氛围，增强气氛。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>

        <!-- Content -->
        <div class="ls-card m-t-16">
            <div class="footprint__content">
                <el-table ref="paneTable" :data="footprintModuleList" style="width: 100%" size="mini">
                    <el-table-column prop="name" label="类型"> </el-table-column>
                    <el-table-column prop="template" label="提醒内容"> </el-table-column>
                    <el-table-column label="气泡状态">
                        <template #default="{ row }">
                            <el-switch
                                v-model="row.status"
                                :active-value="1"
                                :inactive-value="0"
                                @change="changeFootprintModuleStatus(row.id)"
                            ></el-switch>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import { apiFootprintList, apiFootprintStatus } from '@/api/application/footprint'

interface Form {
    duration: number
    status: number
}

@Component
export default class Footprint extends Vue {
    // 气泡模块列表
    footprintModuleList: Array<object> = []

    /** S Methods **/

    // 获取足迹配置信息
    getFootprintData(): void {
        apiFootprintList().then(res => {
            this.footprintModuleList = res
        })
    }

    // 足迹模块状态被改变
    changeFootprintModuleStatus(identity: number): void {
        apiFootprintStatus({
            id: identity
        }).finally(() => {
            this.getFootprintData()
        })
    }

    /** E Methods **/

    /** Life Cycle **/
    created() {
        // 初始化数据
        this.getFootprintData()
    }
}
</script>

<style lang="scss" scoped></style>
