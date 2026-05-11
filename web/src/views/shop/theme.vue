<template>
    <div class="shop-theme">
        <div class="ls-card">
            <div class="nr weight-500">商城风格</div>
            <div class="theme-list flex">
                <div
                    class="theme-item flex m-r-20"
                    :class="{ active: item.name == theme }"
                    v-for="(item, index) in themeList"
                    :key="index"
                    @click="theme = item.name"
                >
                    <div :class="['theme-item__name', `theme-item__name--${item.name}`]"></div>
                    <div class="theme-item__title m-l-14">{{ item.title }}</div>
                </div>
            </div>
            <div class="theme-preview">
                <div v-for="(item, index) in themeList" :key="index">
                    <div v-show="theme == item.name">
                        <img class="preview-image" :src="require(`@/assets/images/${item.name}_goods.png`)" />
                        <img class="preview-image" :src="require(`@/assets/images/${item.name}_order.png`)" />
                        <img class="preview-image" :src="require(`@/assets/images/${item.name}_user.png`)" />
                    </div>
                </div>
            </div>
        </div>
        <div class="shop-theme__footer bg-white ls-fixed-footer">
            <div class="btns row-center flex" style="height: 100%">
                <el-button size="small" type="primary" @click="handleSave">保存</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiThemeConfig, apiThemeConfigSet } from '@/api/shop'
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component({
    components: {}
})
export default class ShopTheme extends Vue {
    /** S data **/
    theme = 'red_theme'
    themeList = [
        {
            name: 'red_theme',
            title: '经典红'
        },
        {
            name: 'orange_theme',
            title: '活力橙'
        },
        {
            name: 'pink_theme',
            title: '美妆色'
        },
        {
            name: 'gold_theme',
            title: '高级金'
        },
        {
            name: 'blue_theme',
            title: '科技蓝'
        },
        {
            name: 'green_theme',
            title: '生鲜绿'
        }
    ]
    /** E data **/

    /** S methods **/
    getThemeConfig() {
        apiThemeConfig({ type: 1 }).then(res => {
            if (res.theme) {
                this.theme = res.theme
            }
        })
    }
    handleSave() {
        apiThemeConfigSet({
            type: 1,
            content: {
                theme: this.theme
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
.shop-theme {
    padding-bottom: 80px;
    .theme-list {
        padding: 24px 30px;
        .theme-item {
            padding: 18px 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid transparent;
            cursor: pointer;
            &.active {
                border-color: $--color-primary;
            }
            &__name {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                &--red_theme {
                    background: linear-gradient(#f95f2f 0%, #ff2c3c 100%);
                }
                &--orange_theme {
                    background: linear-gradient(#f7971e 0%, #ffd200 100%);
                }
                &--pink_theme {
                    background: linear-gradient(#fa444d 0%, #fd498f 100%);
                }
                &--gold_theme {
                    background: linear-gradient(#e0a356 0%, #ebc389 100%);
                }
                &--blue_theme {
                    background: linear-gradient(#2f80ed 0%, #56ccf2 100%);
                }
                &--green_theme {
                    background: linear-gradient(#2ec840 0%, #3de650 100%);
                }
            }
        }
    }
    .theme-preview {
        padding: 24px 30px;
        .preview-image {
            width: 280px;
            height: 498px;
            margin-right: 50px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.05);
        }
    }
}
</style>
