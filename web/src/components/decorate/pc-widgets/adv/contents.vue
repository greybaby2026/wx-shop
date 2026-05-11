<template>
    <div class="adv">
        <div class="adv-content flex">
            <div class="adv-image" v-for="(item, index) in content.data" :key="index">
                <el-image style="width: 100%; height: 100%" :src="$getImageUri(item.url)" fit="cover">
                    <div slot="error" class="image-error muted flex row-center">
                        <i class="el-icon-picture font-size-40"></i>
                    </div>
                </el-image>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import Indicator from '@/components/decorate/indicator.vue'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        Indicator,
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any
}
</script>

<style lang="scss" scoped>
.adv {
    margin-top: 16px;
    .adv-content {
        .adv-image {
            flex: 1;
            height: 180px;
            & ~ .adv-image {
                margin-left: 10px;
            }
        }
    }
}
</style>
