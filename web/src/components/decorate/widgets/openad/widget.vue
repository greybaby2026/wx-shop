<template>
    <contents :content="content" :styles="styles" />
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Contents from './contents.vue'
@Component({
    components: {
        Contents
    }
})
export default class Widget extends Vue {
    @Prop() content!: object | any[]
    @Prop() styles!: object | any[]
}
</script>
