<template>
    <view class="flex-col col-center m-b-32" @tap.stop="handleClick(item.link)">
        <u-image
            class="m-b-14"
            :border-radius="$px2rpx(styles.img_border_radius)"
            v-if="content.style == 1"
            :src="$getImageUri(item.url)"
            width="82rpx"
            height="82rpx"
        />
        <view class="xs" :style="{ color: styles.color }">{{ item.name }}</view>
    </view>
</template>

<script type="text/javascript">
import { navigateTo } from '@/utils/tools'

export default {
    props: {
        content: {
            type: [Object, Array]
        },
        styles: {
            type: [Object, Array]
        },
        item: {
            type: Object
        }
    },
    data() {
        return {}
    },
    methods: {
        navigate() {
            const { path, params } = this.item.link
            if (!path) return
            this.$Router.push({
                path: path,
                query: params
            })
        },
        handleClick(link) {
            navigateTo(link)
        }
    },

    computed: {}
}
</script>
