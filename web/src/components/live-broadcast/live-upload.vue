<template>
    <div class="">
        <el-upload
            :class="ishide ? 'hide' : ''"
            :action="action"
            :headers="{ token: $store.getters.token, version: version }"
            list-type="picture-card"
            :limit="1"
            :on-remove="handleRemove"
            :on-success="handleSuccess"
            :on-exceed="handleExceed"
            :on-error="handleError"
        >
            <i class="el-icon-plus"></i>
        </el-upload>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator'
import config from '@/config'
@Component({
    components: {}
})
export default class LsUpload extends Vue {
    // @Prop() value ? : string // imgUrl

    action = `${config.baseURL}/adminapi/upload/wechatMaterial`
    version = config.version
    ishide = false

    handleRemove(file: any, fileList: any) {
        this.ishide = false
    }
    handleSuccess(response: any, file: any, fileList: any[]) {
        this.$emit('setImage', response.data.media_id)
        this.ishide = true
    }
    handleError(err: any, file: any) {
        this.$message.error(`${file.name}文件上传失败`)
    }
    handleExceed() {
        this.$message.error('超出上传上限，请重新上传')
    }
}
</script>

<style lang="scss" scoped>
::v-deep .el-upload--picture-card {
    width: 98px;
    height: 98px;
}

::v-deep .el-upload {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .el-upload-list--picture-card .el-upload-list__item {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .el-upload-list--picture-card .el-upload-list__item-thumbnail {
    width: 98px;
    height: 98px;
    line-height: 108px;
}

::v-deep .avatar {
    width: 98px;
    height: 98px;
}
::v-deep .el-upload-list__item-status-label {
    visibility: hidden;
}
::v-deep .hide .el-upload--picture-card {
    display: none;
}
</style>
