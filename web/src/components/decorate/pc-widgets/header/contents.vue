<template>
    <div class="header bg-white">
        <div class="header-top flex row-between">
            <div class="notice">xxxx</div>
            <ul class="user flex flex-none">
                <li>
                    <span>登录</span>
                    |
                    <span>注册</span>
                </li>
                <li>
                    <span>我的订单</span>
                </li>
                <li>
                    <span>我的收藏</span>
                </li>
                <li>
                    <span>手机端</span>
                    <i class="el-icon-arrow-down m-l-2"></i>
                </li>
            </ul>
        </div>
        <div class="header-main">
            <div class="search-wrap flex row-between">
                <img :src="logo" class="logo" alt="logo" />
                <div class="flex">
                    <div class="search flex">
                        <input type="text" placeholder="请输入要搜索的商品名称" />
                        <el-button class="search-btn" type="primary"> 搜索 </el-button>
                    </div>
                    <el-button type="primary" plain class="cart">
                        <i class="el-icon-shopping-cart-2 xxl"></i>
                        我的购物车(0)
                    </el-button>
                </div>
            </div>
        </div>
        <div class="header-nav bg-white">
            <div class="nav-wrap flex">
                <category />
                <ul class="nav flex">
                    <li class="item" v-for="(item, index) in content.data" :key="index">
                        <a :class="{ active: index == 0 }">{{ item.name }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
import Category from './category.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot,
        Category
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
    get logo() {
        return this.$store.getters.config.logo
    }
}
</script>

<style lang="scss" scoped>
.header {
    .header-top {
        background-color: #101010;
        height: 40px;
        color: #f2f2f2;
        font-size: 12px;
        .user {
            li {
                margin-left: 20px;
            }
        }
    }
    .header-main {
        .search-wrap {
            height: 80px;
            .logo {
                height: 48px;
                width: auto;
            }
            .search {
                width: 460px;
                height: 42px;
                overflow: hidden;
                input {
                    flex: 1;
                    height: 100%;
                    border-radius: 4px 0 0 4px;
                    border: 1px solid #ff2c3c;
                    border-right-width: 0;
                    padding: 0 10px;
                }
                .search-btn {
                    width: 82px;
                    height: 42px;
                    cursor: pointer;
                    border-radius: 0 4px 4px 0;
                    background-color: #ff2c3c;
                    border-color: #ff2c3c;
                }
            }
            .cart {
                cursor: pointer;
                border-radius: 4px;
                width: 142px;
                padding: 0;
                height: 42px;
                margin-left: 16px;
                background-color: #ffebed;
                border-color: #ff2c3c;
                color: #ff2c3c;
            }
        }
    }
    .header-nav {
        height: 46px;
        .nav-wrap {
            width: 1180px;
            margin: 0 auto;
            height: 100%;
        }
        .nav {
            overflow-x: auto;
            overflow-y: hidden;
            .item {
                a {
                    padding: 12px 15px;
                    margin: 0 10px;
                    color: #101010;
                    font-size: 16px;
                    white-space: nowrap;
                    &:hover {
                        color: #ff2c3c;
                    }
                    &.active {
                        color: #ff2c3c;
                    }
                }
            }
        }
    }
}
</style>
