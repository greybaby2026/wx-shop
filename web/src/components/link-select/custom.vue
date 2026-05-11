<template>
    <div class="custom">
        <el-form size="small" label-width="100px">
            <el-form-item label="跳转链接" required>
                <el-input style="width: 280px" v-model="params" placeholder="请输入跳转链接"></el-input>
            </el-form-item>
            <el-form-item label="">
                <div style="line-height: 1.3" class="muted">
                    请填写完整的带有“https://”或“http://”的链接地址
                    <br />
                    小程序中跳转，链接的域名必须在微信公众平台设置业务域名
                </div>
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'

@Component({
    components: {}
})
export default class Shop extends Vue {
    @Prop() value!: any

    get params() {
        return this.value
    }
    set params(val) {
        this.$emit('input', val)
    }
}
</script>
<style lang="scss" scoped></style>
