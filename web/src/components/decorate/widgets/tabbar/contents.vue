<template>
    <div class="tabbar flex" :style="{ 'background-color': styles.bg_color }">
        <div
            class="tabbar-item flex-1 flex-col row-center col-center"
            v-for="(item, index) in content.data"
            :key="index"
        >
            <div v-if="content.style == 1 || content.style == 2" class="tabbar-icon">
                <img :src="$getImageUri(item.icon)" />
            </div>
            <div v-if="content.style == 1 || content.style == 3" class="tabbar-text" :style="{ color: styles.color }">
                {{ item.name }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
@Component
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: object | any
}
</script>

<style lang="scss" scoped>
.tabbar {
    height: 50px;
    position: absolute;
    bottom: 0;
    width: 100%;
    background: #fff;
    border: 1px solid $--color-primary;
    box-shadow: 0px 0px 10px rgba(64, 115, 250, 0.2);
    box-sizing: border-box;
    .tabbar-icon {
        height: 25px;
        img {
            width: 22px;
            height: 22px;
        }
    }
    .tabbar-text {
        color: #999999;
        line-height: 1.2;
    }
}
</style>
