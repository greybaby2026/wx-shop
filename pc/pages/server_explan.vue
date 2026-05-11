<template>
	<div class="content">
        <div v-html="content"></div>
    </div>
</template>

<script>
    import headerMixins from "@/mixins/header"
	export default {
        mixins: [headerMixins],
        layout: 'void',
        async asyncData({ $get, query }) {
            let content;
            const { data } = await $get("config/getPolicyAgreement", {
                params: {
                    type: query.type
                }
            });
            
            content = data.content
            return { content };
        },
		data() {
			return {
				content: ""
			};
		}
	};
</script>
<style lang="scss">
	.content {
        padding:  30px;
        max-width: 1200px;
        margin: 0 auto;
        background-color: #FFFFFF;
    }
</style>
