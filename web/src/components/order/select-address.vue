<template>
    <ls-dialog
        title="选择地址"
        class="m-l-10 inline"
        width="800px"
        top="20vh"
        ref="Dialog"
        @confirm="handleConfirm"
        @close="handleclose"
        :confirmButtonText="false"
        :cancelButtonText="false"
        async
    >
        <el-button type="text" size="medium" @click="handleToaddress">从地址库新增</el-button>
        <el-button type="text" size="medium" @click="getList">刷新</el-button>

        <el-table :data="pager.lists" ref="paneTable" style="width: 100%" size="mini" v-loading="pager.loading">
            <el-table-column label="地址">
                <template slot-scope="scope">
                    {{ scope.row.province }} {{ scope.row.city }} {{ scope.row.district }},{{ scope.row.contact }},{{
                        scope.row.mobile
                    }}
                    <el-tag type="danger" size="mini" v-if="scope.row.is_deliver_default == 1 && type == 'delivery'"
                        >默认</el-tag
                    >
                    <el-tag type="danger" size="mini" v-if="scope.row.is_return_default == 1 && type == 'return'"
                        >默认</el-tag
                    >
                </template>
            </el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <el-button round size="medium" v-if="selectAddressid == scope.row.id" :disabled="true"
                        >已选择</el-button
                    >

                    <el-button round size="medium" @click="handleSelect(scope.row)" v-else>选择</el-button>
                </template>
            </el-table-column>
        </el-table>
        <!-- 分页 -->
        <div class="m-t-24 flex row-right">
            <ls-pagination v-model="pager" @change="getList" />
        </div>
    </ls-dialog>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import AreaSelect from '@/components/area-select.vue'
import LsDialog from '@/components/ls-dialog.vue'
import { apiaddressLists, apiaddDetial, apiaddressAdd, apiaddressEdit } from '@/api/application/address'
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'

@Component({
    components: {
        AreaSelect,
        LsDialog,
        LsPagination
    }
})
export default class LsWithdrawalDetails extends Vue {
    $refs!: {
        Dialog: any
    }
    pager: RequestPaging = new RequestPaging()
    type: any = ''
    selectAddressid: any = ''
    @Prop() value: any

    // 获取列表
    getList() {
        this.pager
            .request({
                callback: apiaddressLists,
                params: {}
            })
            .catch((err: any) => {})
    }
    openDialog(val: any, id: number) {
        this.$refs.Dialog.open()
        this.getList()
        this.type = val
        this.selectAddressid = id
    }

    async handleConfirm() {}
    handleToaddress() {
        this.$router.push({
            path: '/address/lists'
        })
    }
    handleSelect(row: any) {
        this.$emit('select', row, this.type)
        this.$refs.Dialog.close()
    }
    handleclose() {
        this.$emit('close')
    }
}
</script>
