<template>
    <div class="template-select">
        <div class="template-select__trigger" @click="onTrigger">
            <slot name="trigger"></slot>
        </div>
        <el-dialog
            :visible="visible"
            title="选择模板"
            width="1100px"
            top="15vh"
            :modal-append-to-body="false"
            center
            :before-close="close"
        >
            <detail @select="onSelect" />
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import Detail from './detail.vue'
@Component({
    components: {
        Detail
    }
})
export default class TemplateSelect extends Vue {
    visible = false
    onTrigger() {
        this.visible = true
    }
    close() {
        this.visible = false
    }
    onSelect(value: any) {
        this.close()
        this.$emit('select', value)
    }
}
</script>

<style scoped lang="scss"></style>
