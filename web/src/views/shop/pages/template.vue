<template>
    <div class="pages-template ls-card">
        <div class="nr weight-500 m-b-20">我的模板</div>
        <template-detail type="pages" :page-size="9999" @select="handleSelect" />
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
import TemplateDetail from '@/components/shop/template-select/detail.vue'
import { apiThemePageAdd } from '@/api/shop'

@Component({
    components: {
        TemplateDetail
    }
})
export default class PagesTemplate extends Vue {
    handleSelect(data: any) {
        apiThemePageAdd(data).then(res => {
            this.$router.push({
                path: '/decorate/index',
                query: { id: res.id }
            })
        })
    }
}
</script>
<style lang="scss" scoped></style>
