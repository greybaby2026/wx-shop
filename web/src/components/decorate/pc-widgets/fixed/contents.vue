<template>
    <div class="fixed">
        <div class="float-nav bg-white">
            <div class="nav-list">
                <div class="item flex-col col-center lighter" v-for="(item, index) in content.data" :key="index">
                    <img :src="$getImageUri(item.icon)" class="icon-img" />
                    <div class="xs m-t-5" v-if="content.style == 1">
                        {{ item.name }}
                    </div>
                </div>
            </div>
            <div v-if="!content.data.length">悬浮菜单</div>
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
}
</script>

<style lang="scss" scoped>
.float-nav {
    width: 70px;
    .nav-list {
        bottom: 120px;
        .item {
            padding: 10px 0;
            margin: 0 10px;
            text-align: center;
            cursor: pointer;
            &:hover {
                color: $--color-primary;
            }
            .icon-img {
                width: 28px;
                height: 28px;
            }
        }
    }
}
</style>
