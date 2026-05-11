<template>
    <div class="detail">
        <div class="content flex">
            <div class="left m-r-10">
                <el-scrollbar class="ls-scrollbar" style="height: 100%">
                    <el-collapse class="p-t-20" v-model="activeName">
                        <el-collapse-item
                            :title="item.name"
                            :name="item.key"
                            v-for="(item, index) in lists"
                            :key="index"
                        >
                            <div class="nav-list">
                                <div
                                    class="nav-item"
                                    :class="{ active: linkType == citem.type }"
                                    v-for="(citem, cindex) in item.children"
                                    :key="cindex"
                                    @click="linkType = citem.type"
                                >
                                    <div>{{ citem.name }}</div>
                                </div>
                            </div>
                        </el-collapse-item>
                    </el-collapse>
                </el-scrollbar>
            </div>
            <div class="right flex-1">
                <div class="right-content">
                    <shop
                        :key="linkType"
                        :type="linkType"
                        v-if="linkType == 'shop' || linkType == 'marking'"
                        v-model="link"
                        :client="client"
                    />
                    <goods
                        v-if="linkType == 'goods'"
                        :value="link.params"
                        @input="setLinkParams"
                        :show-virtual-goods="true"
                    />
                    <activity
                        :key="linkType"
                        v-if="
                            linkType == 'seckill' ||
                            linkType == 'team' ||
                            linkType == 'bargain' ||
                            linkType == 'presell'
                        "
                        :single="true"
                        v-model="link.params"
                        :type="linkType"
                        @input="setLinkParams"
                    />

                    <category v-if="linkType == 'category'" :value="link.params" @input="setLinkParams" />
                    <draw v-if="linkType == 'draw'" :value="link.params" @input="setLinkParams" />
                    <page v-if="linkType == 'page'" :value="link.params" @input="setLinkParams" />
                    <Miniprogram
                        v-if="linkType == 'mini_program'"
                        :value="link.params"
                        @input="setLinkParams"
                    ></Miniprogram>
                    <custom v-if="linkType == 'custom' && client == 'mobile'" v-model="link.params.url" />
                    <custom v-if="linkType == 'custom' && client == 'pc'" v-model="link.path" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import Goods from '@/components/goods-select/detail.vue'
import Activity from '@/components/activity-select/detail.vue'
import Shop from './shop.vue'
import Category from './category.vue'
import Page from './page.vue'
import Custom from './custom.vue'
import Draw from './draw.vue'
import Miniprogram from './mini-program.vue'
import { mobileLink, pcLink } from './link'
import { deepClone } from '@/utils/util'
@Component({
    components: {
        Goods,
        Activity,
        Shop,
        Category,
        Page,
        Custom,
        Draw,
        Miniprogram
    }
})
export default class Detail extends Vue {
    @Prop() value!: any
    @Prop({ default: 'mobile' }) client!: string
    activeName: any[] = []
    linkType = 'shop'
    lists = this.client == 'mobile' ? deepClone(mobileLink) : deepClone(pcLink)

    get link(): any {
        let itemLink: any = {}
        this.lists.forEach((item: any) => {
            const citem = item.children.find((citem: any) => citem.type == this.linkType)
            citem && (itemLink = citem)
        })
        return itemLink?.link
    }
    set link(val) {
        this.lists.forEach((item: any) => {
            item.children.forEach((citem: any) => {
                if (citem.type == this.linkType) {
                    val && (citem.link = val)
                    if (!this.activeName.includes(item.key)) {
                        this.activeName.push(item.key)
                    }
                }
            })
        })
    }

    @Watch('value', { immediate: true })
    valueChange(val: any) {
        if (!val) {
            return
        }

        this.linkType = val.type || 'shop'
        this.link = val
    }

    @Watch('link', { deep: true })
    linkChange(val: any) {
        this.$emit('input', val)
    }

    setLinkParams(item: any) {
        if (!item.id) {
            return (this.link.params = {})
        }
        this.link &&
            (this.link = {
                ...this.link,
                params: { id: this.linkType == 'presell' ? item.goods_id : item.id, name: item.name }
            })
    }
}
</script>

<style scoped lang="scss">
.detail {
    .content {
        .left,
        .right {
            width: 160px;
            height: 500px;
            border-radius: 4px;
            border: $--border-base;
            box-sizing: border-box;
        }

        .left {
            ::v-deep .el-collapse {
                border: none;

                &-item__header {
                    height: 40px;
                    line-height: 40px;
                    padding-left: 16px;
                    border-radius: 4px;
                    border: none;
                }
                &-item__wrap {
                    border: none;
                    overflow: unset;
                }
                &-item__content {
                    padding-bottom: 10px;
                }
            }
            .nav-list {
                .nav-item {
                    cursor: pointer;
                    padding: 8px 15px;
                    &.active {
                        color: $--color-primary;
                        background-color: $--color-primary-light-9;
                    }
                }
            }
        }
        .right {
            .right-content {
                padding: 20px 24px;
            }
        }
    }
}
</style>
