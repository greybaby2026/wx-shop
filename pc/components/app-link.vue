<template>
    <a v-if="isExternalLink" v-bind="$attrs" :href="linkUrl" target="_blank">
        <slot />
    </a>
    <nuxt-link v-else v-bind="$props">
        <slot />
    </nuxt-link>
</template>
<script>
import { isExternalLink, isObject, isString } from "@/utils/validate";
export default {
    name: "AppLink",
    props: {
        to: {
            type:  [String, Object],
            required: true
        },
        tag: {
            type: String,
            default: 'a'
        },
        custom: Boolean,
        exact: Boolean,
        exactPath: Boolean,
        append: Boolean,
        replace: Boolean,
        activeClass: String,
        exactActiveClass: String,
        ariaCurrentValue: {
            type: String,
            default: 'page'
        }
    },
    created() {
        console.log(this.$props);
    },
    computed: {
        isExternalLink() {
            const url = this.linkUrl
            return isString(url) && isExternalLink(url);
        },
        linkUrl() {
            return isObject(this.to) ? this.to.path : this.to
        }
    },
};
</script>
