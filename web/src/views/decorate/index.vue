<template>
    <div class="decorate-index flex">
        <decorate-widget />
        <decorate-phone />
        <decorate-attr />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import DecorateWidget from '@/components/decorate/decorate-widget.vue'
import DecoratePhone from '@/components/decorate/decorate-phone.vue'
import DecorateAttr from '@/components/decorate/decorate-attr.vue'

@Component({
    components: {
        DecorateWidget,
        DecoratePhone,
        DecorateAttr
    }
})
export default class DecorateIndex extends Vue {
    /** S data **/
    id!: any
    /** E data **/

    /** S computed **/

    /** E computed **/

    /** S methods **/

    async getPages() {
        if (this.id) {
            this.$store.dispatch('getPages', { id: this.id })
        }
    }
    /** E methods **/
    /** S life cycle **/
    created() {
        this.id = this.$route.query.id
        this.getPages()
    }
    /** E life cycle **/
}
</script>
<style lang="scss" scoped>
.decorate-index {
    height: calc(100vh - #{$--header-height});
}
</style>
