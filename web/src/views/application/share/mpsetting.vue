<template>
    <div>
        <div class="ls-card">
            <el-alert
                title="温馨提示：您可以自定义下面页面的分享内容，配置后，当用户分享该页面时，将按照您的设置进行展示。"
                type="info"
                show-icon
                :closable="false"
            />
        </div>
        <div class="ls-card m-t-24">
            <el-table :data="pager" size="mini" style="width: 100%" :row-style="{ height: '58px' }">
                <el-table-column label="类型" min-width="70" prop="page_desc"> </el-table-column>
                <el-table-column label="分享标题" min-width="70" prop="title"> </el-table-column>
                <el-table-column label="分享图片" min-width="70">
                    <template slot-scope="scope">
                        <el-image
                            v-if="scope.row.image"
                            class="flex-none"
                            style="width: 58px; height: 58px"
                            :src="scope.row.image"
                            fit="cover"
                        ></el-image>
                    </template>
                </el-table-column>
                <el-table-column label="操作">
                    <!-- 操作 -->
                    <template slot-scope="scope">
                        <el-button type="text" size="mini" @click="handleAdd(scope.row)">编辑 </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <Dialog ref="lsDislog" @reflsh="getList"></Dialog>
    </div>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import { apiashareLists } from '@/api/application/share'
import Dialog from './Dialog.vue'

@Component({
    components: {
        LsDialog,
        Dialog
    }
})
export default class ArticleLists extends Vue {
    $refs!: {
        lsDislog: any
    }
    /** S Data **/
    pager: any = [{ title: '', image: '' }]

    /** E Data **/

    /** S Methods **/
    handleAdd(row?: any) {
        this.$refs.lsDislog.openDialog(row.id)
    }

    // 获取列表
    async getList() {
        const res = await apiashareLists()
        this.pager = res.map((item: any, index: any) => {
            if (index < 2) {
                return item
            }
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
