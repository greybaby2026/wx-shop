<template>
    <div class="shop-tabbar flex">
        <div class="tabbar-preview flex-1">
            <el-scrollbar class="ls-scrollbar" style="height: 100%">
                <div class="phone">
                    <w-tabbar :content="tabbar.content" :styles="tabbar.styles" />
                </div>
            </el-scrollbar>
        </div>
        <div class="tabbar-setting">
            <el-scrollbar class="ls-scrollbar" style="height: 100%">
                <a-tabbar :content="tabbar.content" :styles="tabbar.styles" />
            </el-scrollbar>
        </div>
        <div class="tabbar-footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import tabbar from '@/components/decorate/widgets/tabbar'
import { apiThemeConfig, apiThemeConfigSet } from '@/api/shop'
@Component({
    components: {
        WTabbar: tabbar.widget,
        ATabbar: tabbar.attribute
    }
})
export default class ShopTabbar extends Vue {
    tabbar = {
        content: {
            style: '1',
            data: []
        },
        styles: {
            bg_color: '#FFFFFF',
            color: '#666666',
            text_select_color: '#FF2C3C'
        }
    }

    /** S methods **/
    getThemeConfig() {
        apiThemeConfig({ type: 2 }).then(res => {
            if (res.tabbar) {
                this.tabbar = res.tabbar
            }
        })
    }

    handleSave() {
        apiThemeConfigSet({
            type: 2,
            content: {
                tabbar: this.tabbar
            }
        })
    }
    /** E methods **/

    /** S life cycle **/
    created() {
        this.getThemeConfig()
    }
    /** E life cycle **/
}
</script>
<style lang="scss" scoped>
.shop-tabbar {
    height: calc(100vh - #{$--header-height} - 90px);
    .tabbar-preview,
    .tabbar-setting {
        height: 100%;
    }
    .tabbar-setting {
        width: 400px;
        background: #fff;
    }
    .tabbar-preview {
        .phone {
            position: relative;
            margin: 50px auto 0;
            width: 375px;
            height: 667px;
            background: #f5f5f5;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.06);
        }
    }
}
</style>
