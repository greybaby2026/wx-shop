<template>
    <div class="style-list flex row-center">
        <img v-if="content.style == 1" src="@/assets/images/sort_one.jpg" style="width: 375px" />
        <img v-if="content.style == 2" src="@/assets/images/sort_one2.jpg" style="width: 375px" />
        <img v-if="content.style == 3" src="@/assets/images/sort_one3.jpg" style="width: 375px" />
        <img v-if="content.style == 4" src="@/assets/images/sort_one4.jpg" style="width: 375px" />
        <img v-if="content.style == 5" src="@/assets/images/sort_two.jpg" style="width: 375px" />
        <img v-if="content.style == 6" src="@/assets/images/sort_two2.jpg" style="width: 375px" />
        <img v-if="content.style == 7" src="@/assets/images/sort_three2.jpg" style="width: 375px" />
        <img v-if="content.style == 8" src="@/assets/images/sort_three.jpg" style="width: 375px" />
        <img v-if="content.style == 9" src="@/assets/images/sort_two3.jpg" style="width: 375px" />
        <img v-if="content.style == 10" src="@/assets/images/sort_three3.jpg" style="width: 375px" />
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
@Component
export default class StyleList extends Vue {
    @Prop() content!: any
}
</script>

<style lang="scss" scoped>
.style-list {
    height: 100%;
    img {
        height: 100%;
    }
}
</style>
