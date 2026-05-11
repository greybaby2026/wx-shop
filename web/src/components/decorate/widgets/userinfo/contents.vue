<template>
    <div class="user-info flex-col" :style="[infoStyle]">
        <div class="user-top flex-1 col-top">
            <div class="flex">
                <img
                    class="icon-avatar"
                    :src="
                        content.avatar_type == 2 && content.avatar
                            ? $getImageUri(content.avatar)
                            : require('@/assets/images/icon_portrait.png')
                    "
                    mode=""
                />
                <div class="m-l-15 flex-1">
                    <div class="xxl white">会员昵称</div>
                    <div class="flex user-id m-t-4" v-if="content.show_user_sn">
                        <div class="xs m-r-6 white">会员ID: 00000000</div>
                        <div class="xs copy-btn">复制</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="assets-nav flex">
            <div class="assets-item flex-col col-center" v-if="showAssets(1)">
                <div class="number">0</div>
                <div>余额</div>
            </div>
            <div class="assets-item flex-col col-center" v-if="showAssets(2)">
                <div class="number">0</div>
                <div>积分</div>
            </div>
            <div class="assets-item flex-col col-center" v-if="showAssets(3)">
                <div class="number">0</div>
                <div>优惠券</div>
            </div>
            <div class="assets-item flex-col col-center" v-if="showAssets(4)">
                <div class="number">0</div>
                <div>收藏</div>
            </div>
        </div>
        <div class="user-grade flex row-between" v-if="content.show_member">
            <div class="sm"><span class="lg m-r-6 weight-500" style="font-style: italic">V</span>登录查看会员权益</div>
            <div class="xs">了解会员 <i class="el-icon-arrow-right"></i></div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get infoStyle() {
        const { background_image, background_type } = this.content
        if (!background_image || background_type == 1) {
            return {}
        }
        return {
            'background-image': `url(${this.$getImageUri(this.content.background_image)})`
        }
    }

    get showAssets() {
        return (type: number) => {
            return this.content.assets.includes(type)
        }
    }
}
</script>

<style lang="scss" scoped>
.user-info {
    background: url('../../../../assets/images/bg_user.png'), linear-gradient(to right, #f95f2f 0%, #ff2c3c 100%);
    background-size: 100% auto;
    background-repeat: no-repeat;
    padding: 20px 0 0;
    box-sizing: border-box;
    .user-top {
        padding: 0 15px;
        .icon-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 1px solid #f8f8f8;
        }
        .user-id {
            display: inline-flex;
            border: 1px solid #fff;
            border-radius: 50px;
            padding-left: 10px;
            .copy-btn {
                padding: 1.5px 10px;
                border-radius: 10px;
                background: #fff;
            }
        }
    }
    .assets-nav {
        padding: 15px 0 10px;
        color: #fff;
        .assets-item {
            flex: 1;
            width: 50%;
        }
    }
    .user-grade {
        color: #ffe0a1;
        height: 40px;
        background: url('../../../../assets/images/bg_user_grade.png');
        background-size: 100%;
        padding: 0 10px;
        margin: 0 10px;
    }
}
</style>
