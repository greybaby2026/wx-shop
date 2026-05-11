<template>
    <widget-root :styles="styles">
        <div class="rubik" :style="{ margin: `-${styles.margin / 2}px` }">
            <div class="rubik-item1" v-if="content.style == 1">
                <div
                    class="item-image"
                    :style="{ margin: `${styles.margin / 2}px` }"
                    v-if="imgLists[0] && imgLists[0].url"
                >
                    <el-image fit="cover" :src="$getImageUri(imgLists[0].url)"></el-image>
                </div>
                <el-image v-else style="height: 375px; width: 100%">
                    <div slot="error" class="image-error muted flex row-center">
                        <i class="el-icon-picture font-size-40"></i>
                    </div>
                </el-image>
            </div>
            <div class="rubik-item2" v-if="content.style == 2">
                <div
                    class="item-image"
                    v-for="(item, index) in imgLists"
                    :key="index"
                    :style="{ margin: `${styles.margin / 2}px` }"
                >
                    <el-image fit="cover" :src="$getImageUri(item.url)" v-if="item && item.url"></el-image>
                    <el-image v-else style="height: 188px; width: 100%">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
            </div>
            <div class="rubik-item3" v-if="content.style == 3">
                <div
                    class="item-image"
                    v-for="(item, index) in imgLists"
                    :key="index"
                    :style="{ margin: `${styles.margin / 2}px` }"
                >
                    <el-image fit="cover" :src="$getImageUri(item.url)" v-if="item && item.url"></el-image>
                    <el-image v-else style="height: 125px; width: 100%">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
            </div>
            <div class="rubik-item4" v-if="content.style == 4">
                <div
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '188px',
                        top: 0,
                        left: 0
                    }"
                >
                    <el-image fit="cover" :src="imgLists[0] && $getImageUri(imgLists[0].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
                <div
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '94px',
                        top: 0,
                        left: '50%'
                    }"
                >
                    <el-image fit="cover" :src="imgLists[1] && $getImageUri(imgLists[1].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
                <div
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '94px',
                        top: '94px',
                        left: '50%'
                    }"
                >
                    <el-image fit="cover" :src="imgLists[2] && $getImageUri(imgLists[2].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
            </div>
            <div class="rubik-item5" v-if="content.style == 5">
                <div class="item-image" v-for="(item, index) in imgLists" :key="index">
                    <el-image fit="cover" :src="item && $getImageUri(item.url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
            </div>
            <div class="rubik-item6" v-if="content.style == 6">
                <div
                    class="item-image"
                    :style="{
                        width: '100%',
                        height: '94px',
                        top: 0,
                        left: 0
                    }"
                >
                    <el-image fit="cover" :src="imgLists[0] && $getImageUri(imgLists[0].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
                <div
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '94px',
                        top: '94px',
                        left: 0
                    }"
                >
                    <el-image fit="cover" :src="imgLists[1] && $getImageUri(imgLists[1].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
                <div
                    class="item-image"
                    :style="{
                        width: '50%',
                        height: '94px',
                        top: '94px',
                        left: '50%'
                    }"
                >
                    <el-image fit="cover" :src="imgLists[2] && $getImageUri(imgLists[2].url)">
                        <div slot="error" class="image-error muted flex row-center">
                            <i class="el-icon-picture font-size-40"></i>
                        </div>
                    </el-image>
                </div>
            </div>
        </div>
    </widget-root>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import WidgetRoot from '@/components/decorate/widget-root.vue'
@Component({
    components: {
        WidgetRoot
    }
})
export default class Contents extends Vue {
    @Prop() content!: any
    @Prop() styles!: any

    get imgLists() {
        return this.content.data
    }
}
</script>

<style lang="scss" scoped>
.rubik {
    .el-image {
        display: block;
    }
    .rubik-item2 {
        display: flex;
        .item-image {
            flex: 1 1 auto;
            width: 50%;
        }
    }
    .rubik-item3 {
        display: flex;
        .item-image {
            flex: 1 1 auto;
            width: 33.3%;
        }
    }
    .rubik-item4,
    .rubik-item6 {
        position: relative;
        width: 100%;
        height: 180px;
        .item-image {
            position: absolute;
            .el-image {
                height: 100%;
            }
        }
    }
    .rubik-item5 {
        display: flex;
        flex-wrap: wrap;
        .item-image {
            flex: 1 1 auto;
            width: 50%;
            height: 94px;
            .el-image {
                height: 100%;
            }
        }
    }
}
</style>
