<template>
    <div class="add-details">
        <div class="flex col-stretch">
            <el-form-item label="商品详情" class="form-edit">
                <editor v-model="value.content" width="375" />
            </el-form-item>
            <div class="preview m-l-30 flex-none">
                <div class="flex-1" style="min-height: 0">
                    <el-scrollbar class="ls-scrollbar" style="height: 100%">
                        <div class="p-l-10 p-r-10" v-html="value.content"></div>
                    </el-scrollbar>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Editor from '@/components/editor.vue'
@Component({
    components: {
        Editor
    }
})
export default class AddDetails extends Vue {
    @Prop() value: any
}
</script>

<style scoped lang="scss">
.add-details {
    padding-bottom: 40px;
    .form-edit {
        margin-bottom: 0;
    }
    .preview {
        width: 360px;
        box-sizing: content-box;
        flex: none;
        height: 712px;
        border: 10px solid #f5f8ff;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        ::v-deep img {
            max-width: 100%;
            vertical-align: top;
            display: inline;
        }
    }
}
</style>
