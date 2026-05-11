<template>
    <div class="distribution-center">
        <div class="ls-distribution ls-card">
            <div class="title xxl flex">分销</div>
            <div class="m-t-20 flex menu flex-wrap">
                <router-link
                    v-for="(item, index) in menuList"
                    tag="div"
                    :to="item.path"
                    class="flex menu-item"
                    :key="index"
                >
                    <el-image class="m-r-18 menu-icon" />
                    <div class="menu-info">
                        <div class="lg">{{ item.name }}</div>
                        <div class="muted m-t-4">{{ item.desc }}</div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'

@Component
export default class DistributionCenter extends Vue {
    menuList = [
        {
            name: '分销',
            desc: '裂变分销，智能锁粉',
            path: '/distribution/survey'
        }
    ]
}
</script>

<style lang="scss" scoped>
.distribution-center {
    .ls-distribution,
    .ls-application {
        .title::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 18px;
            border-radius: 1.5px;
            background: linear-gradient(#fcbc2e 0%, #f86056 100%);
            margin-right: 8px;
        }
        .menu {
            .menu-item {
                margin-top: 24px;
                margin-right: 24px;
                padding: 28px 24px;
                border-radius: 8px;
                border: 1px solid $--border-color-base;
                width: 320px;
                box-sizing: border-box;
                cursor: pointer;
                .menu-icon {
                    width: 44px;
                    height: 44px;
                }
            }
        }
    }
    .ls-application {
        .title::before {
            background: linear-gradient(#29a0ff 0%, #2690ff 100%);
        }
    }
}
</style>
