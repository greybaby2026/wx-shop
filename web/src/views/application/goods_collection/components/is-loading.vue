<template>
    <div>
        <ls-dialog
            ref="lsDialog"
            width="510px"
            top="30vh"
            :confirmButtonText="false"
            :cancelButtonText="false"
            :title="title"
        >
            <!-- v-if="checking" -->

            <div v-loading="checking" element-loading-text="正在处理中，请稍等..." style="height: 200px"></div>
        </ls-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import LsDialog from '@/components/ls-dialog.vue'
@Component({
    components: {
        LsDialog
    }
})
export default class GoodsIsGather extends Vue {
    $refs!: { lsDialog: any }
    @Prop() title?: string
    checking = true
    closeDialog() {
        this.checking = false
        this.$refs.lsDialog.close()
    }
    openDialog() {
        this.checking = true
        this.$refs.lsDialog.open()
    }
}
</script>
