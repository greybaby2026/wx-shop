<template>
    <div style="padding: 10px 10px 60px 10px;">
        <div class="header lg m-b-50">
            填写快递单号
        </div>

        <el-form ref="inputForm" label-width="100px" :model="form" :rules="rules">
            <el-form-item label="物流公司：" prop="business">
                <el-input size="small" style="width:248px" v-model="form.business" placeholder="请输入物流公司名称" />
            </el-form-item>
            <el-form-item label="快递单号：" prop="number">
                <el-input size="small" style="width:248px" v-model="form.number" placeholder="请输入快递单号" />
            </el-form-item>
            <el-form-item label="备注说明：" prop="desc">
                <el-input style="width: 280px" type="textarea" v-model="form.desc" placeholder="请输入详细内容，选填"
                    resize="none" rows="5" />
            </el-form-item>
            <el-form-item label="上传凭证：" prop="upload">
                <div class="xs muted">请上传快递单号凭证，选填</div>
                <upload isSlot :file-list="fileList" @success="uploadSuccess" :limit="3">
                    <div class="column-center" style="height: 100%">
                        <i class="el-icon-camera xs" style="font-size: 24px" />
                    </div>
                </upload>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" style="width: 140px" @click="submitForm">提交</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
import upload from "@/components/public/upload";
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    components: {
        upload,
    },
    data() {
        return {
            aid: 0,
            form: {
                // 物流公司
                business: "",
                // 快递单号
                number: "",
                // 详细内容
                desc: "",
            },
            rules: {
                business: [{ required: true, message: "请输入物流公司" }],
                number: [{ required: true, message: "请输入快递单号" }],
            },
            fileList: [],
        };
    },
    layout: "user-layout",
    mounted() {
        this.aid = this.$route.query.id
    },
    methods: {
        submitForm() {
            console.log(this.$refs);
            this.$refs["inputForm"].validate(async (valid) => {
                if (valid) {
                    let fileList = [];
                    this.fileList.forEach((item) => {
                        fileList.push(item.response.data.uri);
                    });
                    let data = {
                        id: this.aid,
                        express_name: this.form.business,
                        invoice_no: this.form.number,
                        express_remark: this.form.desc,
                        express_image: fileList,
                    };
                    console.log(data)
                    let res = await this.$post("after_sale/returnGoods", data);
                    if (res.code == 1) {
                        this.$message({
                            message: "提交成功",
                            type: "success",
                        });
                        this.showDialog = false;
                        setTimeout(() => {
                            this.$router.go(-1)
                        },1000)
                    }
                } else {
                    return false;
                }
            });
        },
        uploadSuccess(e) {
            let fileList = Object.assign([], e);
            this.fileList = fileList;
        },
    },
};
</script>

<style lang="scss">
.header {
    padding: 10px 5px;
    border-bottom: 1px solid #e5e5e5;
}
</style>