<template>
    <div class="chat-message">
        <!-- My message -->
        <div class="message-contain message-contain--my" v-if="$slots['my']">
            <slot name="my"></slot>
        </div>

        <!-- Avatar -->
        <el-avatar class="message-avatar" :size="40" :src="avatar"></el-avatar>

        <!-- His message -->
        <div class="message-contain message-contain--his" v-if="$slots['his']">
            <slot name="his"></slot>
        </div>
    </div>
</template>


<script>
export default {
    name: 'ChatMessage',

    props: {
        avatar: {
            type: String,
            default: ''
        }
    }
}
</script>

<style lang="scss" scoped>
.chat-message {
    display: inline-flex;

    .message-avatar {
        min-width: 40px;
    }

    .message-contain {
        padding: 10px 12px;
        border-radius: 8px;
        max-width: 1em * 32;
    }

    .message-contain--my {
        margin-right: 10px;
        background-color: $--color-primary;
        color: #FFFFFF;
    }

    .message-contain--his {
        margin-left: 10px;
        border: 1px solid $--border-color-base;
    }
}
</style>
