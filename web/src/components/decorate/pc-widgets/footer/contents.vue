<template>
    <div class="footer">
        <div class="footer-tags flex">
            <div class="tags-item flex flex-1 row-center" v-for="(item, index) in content.data" :key="index">
                <img class="tags-img" :src="$getImageUri(item.url)" alt="" />
                <div class="m-l-10 xxl">{{ item.name }}</div>
            </div>
        </div>
        <div class="copy-right xs">Copyright © 2019-2021 xxxx xxxx</div>
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
.footer {
    background-color: #101010;
    color: #ccc;
    .footer-tags {
        padding: 35px 0;
        border-bottom: 1px solid #ccc;
        .tags-img {
            width: 50px;
            height: 50px;
        }
    }
    .copy-right {
        text-align: center;
        padding: 20px 0;
        height: 58px;
    }
}
</style>
