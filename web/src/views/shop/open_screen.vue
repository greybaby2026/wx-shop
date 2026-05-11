<template>
    <div class="open-screen flex">
        <div class="screen-preview flex-1">
            <el-scrollbar class="ls-scrollbar" style="height: 100%">
                <div class="phone">
                    <w-openad :content="screen.content" :styles="screen.styles" />
                    <i class="el-icon-circle-close closer"></i>
                </div>
            </el-scrollbar>
        </div>
        <div class="screen-setting">
            <el-scrollbar class="ls-scrollbar" style="height: 100%">
                <a-openad :content="screen.content" :styles="screen.styles" />
            </el-scrollbar>
        </div>
        <div class="screen-footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="onSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import openad from '@/components/decorate/widgets/openad'
import { apiThemeConfig, apiThemeConfigSet } from '@/api/shop'
@Component({
    components: {
        WOpenad: openad.widget,
        AOpenad: openad.attribute
    }
})
export default class OpenScreen extends Vue {
    screen = {
        content: {
            enable: '1',
            image: '',
            link: {},
            show_config: '1',
            show_config_number: '3'
        },
        styles: {
            root_bg_color: '',
            icon_color: '',
            padding_top: 0,
            padding_horizontal: 0,
            padding_bottom: 0
        }
    }

    /** S methods **/
    getThemeConfig() {
        apiThemeConfig({ type: 3 }).then(res => {
            this.screen = res.screen
        })
    }

    onSave() {
        if (this.screen.content.show_config_number == '') {
            return this.$message.error('请填写显示次数')
        }
        apiThemeConfigSet({
            type: 3,
            content: {
                screen: this.screen
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
.open-screen {
    height: calc(100vh - #{$--header-height} - 90px);
    .screen-preview,
    .screen-setting {
        height: 100%;
    }
    .screen-setting {
        width: 400px;
        background: #fff;
    }
    .screen-preview {
        .phone {
            position: relative;
            margin: 50px auto 0;
            width: 375px;
            height: 667px;
            background: #f5f5f5;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.06);
        }
    }
    .closer {
        width: 100%;
        text-align: center;
        margin-top: 20px;
        font-size: 28px;
    }
}
</style>
