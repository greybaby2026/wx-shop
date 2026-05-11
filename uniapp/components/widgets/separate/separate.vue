<template>
    <widget-root :styles="styles">
        <view class="separate">
            <view class="line" :style="{'border-color': styles.line_color, 'border-style': content.separate}"></view>
        </view>
    </widget-root>
</template>

<script type="text/javascript">
    export default {
        props: {
            content: {
                type: [Object, Array]
            },
            styles: {
                type: [Object, Array]
            },
        },
        data() {
            return {

            }
        },
        methods: {

        },
        computed: {

        }
    };
</script>

<style lang="scss" scoped>
    .separate {
        width: 100%;
        .line {
            border-width: 1px;
        }
    }
</style>
