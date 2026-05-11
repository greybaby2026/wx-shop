<template>
    <div class="v-upload">
        <el-upload list-type="picture-card" :action="url + '/shopapi/Upload/image'" :headers="{'version':config.version,'token': $store.state.token}" :limit="limit" :on-success="success"
            :on-error="error" :on-change="onChange" :auto-upload="autoUpload"
			accept="image/jpg,image/jpeg,image/png"
			:before-upload="beforeAvatarUpload">
            <slot v-if="isSlot"></slot>
            <div v-else>
                <div class="muted xs">上传图片</div>
            </div>
        </el-upload>
    </div>
</template>

<script>
import config from "~/config/app";
export default {
    components: {},
    props: {
        limit: {
            type: Number,
            default: 1,
        },
        isSlot: {
            type: Boolean,
            default: false,
        },
        autoUpload: {
            type: Boolean,
            default: true,
        },
        onChange: {
            type: Function,
            default: () => {},
        },
    },
    watch: {},
    data() {
        return {
            config: config,
            url: config.config.baseUrl,
        };
    },
    created() {},
    computed: {},
    methods: {
        success(res, file, fileList) {
            if (!this.autoUpload) {
                return;
            }
            this.$message({
                message: "上传成功",
                type: "success",
            });
            this.$emit("success", fileList);
        },
        error(res) {
            this.$message({
                message: "上传失败，请重新上传",
                type: "error",
            });
        },
		
		// 图片上传限制
		beforeAvatarUpload(file) {
		  var testmsg = file.name.substring(file.name.lastIndexOf('.') + 1)
		  console.log('fdsadsf')
		  const extension = testmsg === 'jpg'
		  const extension2 = testmsg === 'png'
		  const extension4 = testmsg === 'jpeg'
		  if (!extension && !extension2) {
		    this.$message({
		      message: '上传文件只能是 jpg, jpeg, png格式!',
		      type: 'warning'
		    })
		    return false
		  }
		  return extension || extension2 || extension4
		},
    },
};
</script>

<style lang="scss">
.v-upload {
    .el-upload--picture-card {
        width: 76px;
        height: 76px;
        line-height: 76px;
    }
    .el-upload-list--picture-card {
        .el-upload-list__item {
            width: 76px;
            height: 76px;
        }
    }
}
</style>