<template>
    <div class="marketing-center">
        <div class="ls-marketing ls-card m-b-16" v-for="(item, index) in menuList" :key="index">
            <div class="title flex">
                {{ item.name }}
                <div class="m-l-10 xs weight-400">{{ item.introduce }}</div>
            </div>
            <div class="flex menu flex-wrap">
                <router-link
                    v-for="(menu, mindex) in item.list"
                    tag="div"
                    :to="menu.page_path"
                    class="flex menu-item"
                    :key="mindex"
                >
                    <el-image :src="menu.image" class="m-r-18 menu-icon" />
                    <div class="menu-info">
                        <div class="lg">
                            {{ menu.name }}
                            <span class="is-open xs" v-if="!menu.is_open">即将开放</span>
                        </div>
                        <div class="muted m-t-4 line-2">
                            {{ menu.introduce }}
                        </div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiMarketingModule } from '@/api/marketing'
import { Component, Vue } from 'vue-property-decorator'
import config from '@/config'
@Component
export default class MarketingCenter extends Vue {
    menuList = []

    getModule() {
        apiMarketingModule().then(res => {
            res.map((item: any) => {
                return item.list.map((el: any) => {
                    el.image = config.baseURL + el.image
                    return el
                })
            })
            this.menuList = res
        })
    }

    created() {
        this.getModule()
    }
}
</script>

<style lang="scss" scoped>
.marketing-center {
    .ls-marketing {
        .title {
            font-weight: 500;
            font-size: 14px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f2f2f2;
        }
        .menu {
            .menu-item:hover {
                .lg {
                    color: $--color-primary;
                }
                background: #f5f8ff;
                border: 1px solid $--color-primary;
            }
            .menu-item {
                margin-top: 24px;
                margin-right: 20px;
                padding: 0 24px;
                border-radius: 8px;
                background: #f9f9f9;
                width: 315px;
                border: 1px solid #f9f9f9;
                cursor: pointer;
                height: 102px;
                .menu-icon {
                    width: 44px;
                    height: 44px;
                }
            }
            .is-open {
                color: #ff8e00;
                // margin-left: 15px;
                padding: 0 4px;
                border-radius: 2px;
                background: #fff3e5;
                border: 1px solid #ff8e00;
            }
        }
    }
}
</style>
