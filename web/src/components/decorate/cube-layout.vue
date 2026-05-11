<template>
    <div class="cube-layout">
        <div class="layout-item1" v-if="facade == 1">
            <div class="item-image" :class="{ active: current == 0 }">
                <el-image
                    v-if="imgLists[0] && imgLists[0].url"
                    fit="cover"
                    :src="$getImageUri(imgLists[0].url)"
                ></el-image>
                <div class="item-tips muted" v-else>
                    <span>750x不限高度</span>
                </div>
            </div>
        </div>
        <div class="layout-item2" v-if="facade == 2">
            <div
                class="item-image"
                v-for="(item, index) in imgLists"
                :class="{ active: current == index }"
                :key="index"
                @click="changeCurrent(index)"
            >
                <el-image fit="cover" :src="$getImageUri(item.url)" v-if="item && item.url"></el-image>
                <div class="item-tips muted" v-else>
                    <span>375x不限高度</span>
                </div>
            </div>
        </div>
        <div class="layout-item3" v-if="facade == 3">
            <div
                class="item-image"
                v-for="(item, index) in imgLists"
                :class="{ active: current == index }"
                :key="index"
                @click="changeCurrent(index)"
            >
                <el-image fit="cover" :src="$getImageUri(item.url)" v-if="item && item.url"></el-image>
                <div class="item-tips muted" v-else>
                    <span>250x不限高度</span>
                </div>
            </div>
        </div>
        <div class="layout-item4" v-if="facade == 4">
            <div
                class="item-image"
                :class="{ active: current == 0 }"
                @click="changeCurrent(0)"
                :style="{
                    width: '180px',
                    height: '180px',
                    top: 0,
                    left: 0
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[0].url)"
                    v-if="imgLists[0] && imgLists[0].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>375x375</span></div>
            </div>
            <div
                class="item-image"
                :class="{ active: current == 1 }"
                @click="changeCurrent(1)"
                :style="{
                    width: '180px',
                    height: '90px',
                    top: 0,
                    left: '180px'
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[1].url)"
                    v-if="imgLists[1] && imgLists[1].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>375x188</span></div>
            </div>
            <div
                class="item-image"
                :class="{ active: current == 2 }"
                @click="changeCurrent(2)"
                :style="{
                    width: '180px',
                    height: '90px',
                    top: '90px',
                    left: '180px'
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[2].url)"
                    v-if="imgLists[2] && imgLists[2].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>375x188</span></div>
            </div>
        </div>
        <div class="layout-item5" v-if="facade == 5">
            <div
                class="item-image"
                v-for="(item, index) in imgLists"
                :class="{ active: current == index }"
                :key="index"
                @click="changeCurrent(index)"
            >
                <el-image fit="cover" :src="$getImageUri(item.url)" v-if="item && item.url"></el-image>
                <div class="item-tips muted" v-else><span>188x188</span></div>
            </div>
        </div>
        <div class="layout-item6" v-if="facade == 6">
            <div
                class="item-image"
                :class="{ active: current == 0 }"
                @click="changeCurrent(0)"
                :style="{
                    width: '360px',
                    height: '90px',
                    top: 0,
                    left: 0
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[0].url)"
                    v-if="imgLists[0] && imgLists[0].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>750x188</span></div>
            </div>
            <div
                class="item-image"
                :class="{ active: current == 1 }"
                @click="changeCurrent(1)"
                :style="{
                    width: '180px',
                    height: '90px',
                    top: '90px',
                    left: 0
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[1].url)"
                    v-if="imgLists[1] && imgLists[1].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>375x188</span></div>
            </div>
            <div
                class="item-image"
                :class="{ active: current == 2 }"
                @click="changeCurrent(2)"
                :style="{
                    width: '180px',
                    height: '90px',
                    top: '90px',
                    left: '180px'
                }"
            >
                <el-image
                    fit="cover"
                    :src="$getImageUri(imgLists[2].url)"
                    v-if="imgLists[2] && imgLists[2].url"
                ></el-image>
                <div class="item-tips muted" v-else><span>375x188</span></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
@Component({})
export default class DecoratePhone extends Vue {
    @Prop() value!: any
    @Prop() facade!: number
    /** S data **/
    current = 0
    /** E data **/
    /** S computed **/
    get imgLists() {
        return this.value
    }
    set imgLists(val) {
        this.$emit('input', val)
    }
    /** E computed **/
    changeCurrent(val: number) {
        this.current = val
        this.$emit('change', val)
    }
}
</script>

<style lang="scss" scoped>
.cube-layout {
    .item-tips {
        background: #fff;
        border: $--border-base;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .item-image {
        cursor: pointer;
        &.active {
            .el-image {
                position: relative;
                z-index: 10;
                border: 1px solid $--color-primary;
            }
            .item-tips {
                position: relative;
                z-index: 10;
                border: 1px solid $--color-primary;
            }
        }
        .el-image {
            width: 100%;
            box-sizing: border-box;
            height: auto;
        }
    }
    .layout-item1 {
        text-align: center;
        font-weight: 400;
        width: 360px;

        .item-tips {
            width: 100%;
            height: 158px;
            line-height: 158px;
        }
    }
    .layout-item2 {
        width: 360px;
        display: flex;
        .item-image {
            flex: 1 1 auto;
            width: 180px;
            .item-tips {
                width: 180px;
                height: 158px;
            }
        }
    }

    .layout-item3 {
        display: flex;
        width: 360px;
        .item-image {
            flex: 1 1 auto;
            width: 120px;
            .item-tips {
                width: 120px;
                height: 120px;
            }
        }
    }
    .layout-item4 {
        position: relative;
        width: 360px;
        height: 180px;
        .item-image {
            position: absolute;
            .el-image {
                height: 100%;
            }

            .item-tips {
                width: 180px;
                height: 100%;
            }
        }
    }
    .layout-item5 {
        width: 360px;
        height: 180px;
        display: flex;
        flex-wrap: wrap;
        .item-image {
            flex: 1 1 auto;
            width: 180px;
            height: 90px;
            .el-image {
                height: 100%;
            }
            .item-tips {
                width: 180px;
                height: 90px;
            }
        }
    }

    .layout-item6 {
        position: relative;
        width: 360px;
        height: 180px;
        .item-image {
            position: absolute;
            .el-image {
                height: 100%;
            }

            &:first-of-type {
                .item-tips {
                    width: 360px;
                }
            }
            .item-tips {
                width: 180px;
                height: 100%;
            }
        }
    }
}
</style>
