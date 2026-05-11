<template>
    <div class="category">
        <style-list :content="content" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import StyleList from './style-list.vue'
@Component({
    components: {
        StyleList
    }
})
export default class Contents extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
}
</script>

<style lang="scss" scoped>
.category {
    width: 100%;
    ::v-deepimg {
        width: 100%;
    }
}
</style>
