<template>
    <div class="container">
        <div class="header" @click="openDialog">
            <el-tag effect="dark" size="small">免费更新</el-tag>
        </div>

        <ls-dialog
            ref="lsDialog"
            width="510px"
            @confirm="goPage"
            :confirmButtonText="!checking ? '前往官网' : false"
            :cancelButtonText="!checking ? '关闭' : false"
            title="免费更新"
        >
            <div v-if="checking" v-loading="checking" element-loading-text="正在检测中" style="height: 200px" />

            <div v-else>
                <template v-for="(item, index) in state">
                    <div v-if="item.flag" :key="index">
                        <div class="text-center">
                            <el-image style="width: 48px; height: 48px" :src="item.img" :fit="'cover'" class="m-b-20" />
                        </div>
                        <div class="m-b-60 p-l-45 p-r-45">
                            {{ item.ctx_one }}
                            <span style="color: #4073fa">{{ item.ctx_two }}</span>
                            {{ item.ctx_three }}
                        </div>
                    </div>
                </template>
            </div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
import { apiCheckRefresh } from '@/api/home'

@Component({
    components: {
        LsDialog
    }
})
export default class IsCopyRight extends Vue {
    $refs!: { lsDialog: any }
    checking = true
    state = [
        {
            flag: true,
            img: require('@/assets/images/success.png'),
            ctx_one: `恭喜您，系统检测到您的域名`,
            ctx_two: '已授权',
            ctx_three: '，可前往likeshop.cn官网享受所有正版权益'
        },
        {
            flag: false,
            img: require('@/assets/images/error.png'),
            ctx_one: '很遗憾，系统检测到您的域名',
            ctx_two: '未授权',
            ctx_three: '，请前往likeshop.cn官网【个人中心】-【产品授权】登记域名授权，否则将视为盗版行为!'
        }
    ]

    openDialog() {
        this.$refs.lsDialog.open()
        setTimeout(() => {
            this.checkRefresh()
        }, 1000)
    }

    goPage() {
        window.open('https://www.likeshop.cn/', '_blank')
    }

    checkRefresh() {
        apiCheckRefresh()
            .then(res => {
                this.state.forEach(item => {
                    if (!res.result) {
                        item.flag = !item.flag
                    }
                })
            })
            .catch(err => this.$message.error(err))
            .finally(() => {
                this.checking = false
            })
    }
}
</script>

<style scoped lang="scss">
.container {
    height: 100%;
    .header {
        cursor: pointer;
        display: flex;
        align-content: center;
        padding: 0 8px;
    }
}
</style>
