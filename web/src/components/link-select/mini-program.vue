<template>
    <div class="">
        <div class="title">跳转小程序</div>
        <div class="m-t-8">
            <el-form size="small" label-width="100px">
                <el-form-item label="小程序原始ID">
                    <el-input
                        style="width: 280px"
                        v-model="params.originalId"
                        placeholder="请输入小程序原始ID"
                    ></el-input>
                </el-form-item>
                <el-form-item label="小程序APPID">
                    <el-input style="width: 280px" v-model="params.appId" placeholder="请输入小程序APPID"></el-input>
                </el-form-item>
                <el-form-item label="小程序路径">
                    <el-input style="width: 280px" v-model="params.path" placeholder="请输入小程序路径"></el-input>
                </el-form-item>
                <el-form-item label="传递参数">
                    <el-input
                        style="width: 280px"
                        v-model="params.query"
                        placeholder="请输入小程序跳转参数（选填）"
                    ></el-input>
                    <div style="line-height: 1.3" class="muted m-t-8">
                        示例：id=2&ustm=jiny&name=234
                        <br />
                        <span style="color: red">
                            注意：不允许输入中文、特殊字符等。如果出现对不起，当前页面无法访问，大概率是跳转参数的问题！！
                        </span>
                    </div>
                </el-form-item>
                <el-form-item label="小程序版本">
                    <el-radio-group class="m-r-16" v-model="params.envVersion">
                        <el-radio label="release">正式版</el-radio>
                        <el-radio label="trial">体验版</el-radio>
                        <el-radio label="develop">开发版</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="">
                    <div>1. 小程序路径链接地址请填写小程序的页面路径，如：pages/index/index</div>
                    <div>
                        <span>2. 如果是H5(浏览器)中需要跳转到小程序，则需要以下配置---></span>
                        <a href="https://mp.weixin.qq.com/" class="text-primary" target="_blank" rel="nofollow">
                            小程序管理后台 -&gt; 设置 -&gt; 隐私与安全 -&gt; 明文 scheme 拉起此小程序 （点击跳转去配置）
                        </a>
                    </div>
                    <div>
                        <span>3. App跳转小程序时，需要填写小程序的原始ID</span>
                    </div>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'

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
    created() {}
}
</script>
<style lang="scss" scoped>
.title {
    font-size: 16px;
    font-weight: 500;
}
a {
    text-decoration: none;
}
</style>
