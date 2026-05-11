<template>
    <div class="template-details" v-loading="pager.loading">
        <div class="template-list flex flex-wrap">
            <div class="template-item template-item--empty" v-if="type == 'popup'">
                <div class="template-item__img flex col-center row-center">
                    <div class="lg lighter">空白模板</div>
                </div>
                <div class="template-item__mask flex-col col-center row-center">
                    <div>
                        <el-button size="small" type="primary" @click="handleCreate('empty')">启用模板</el-button>
                    </div>
                </div>
            </div>
            <div class="template-item" v-for="(item, index) in pager.lists" :key="index">
                <div class="template-item__img">
                    <img :src="item.image" alt="" />
                </div>
                <div class="template-item__mask flex-col col-center row-center">
                    <div>
                        <el-button size="small" type="primary" @click="handleCreate(item.theme_page)"
                            >启用模板</el-button
                        >
                    </div>
                    <div class="m-t-10">
                        <a :href="item.image" target="_blank">
                            <el-button size="small">预览模板</el-button>
                        </a>
                    </div>
                </div>
                <div class="template-item__footer">
                    <div class="weight-500 nr">{{ item.name }}</div>
                </div>
            </div>
        </div>
        <div class="flex row-right m-t-16">
            <ls-pagination
                v-if="type == 'popup'"
                v-model="pager"
                layout="total, prev, pager, next, jumper"
                @change="getList"
            />
        </div>
    </div>
</template>

<script lang="ts">
import { apiSystemThemePage } from '@/api/shop'
import { RequestPaging } from '@/utils/util'
import { Component, Prop, Vue, Watch } from 'vue-property-decorator'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class Details extends Vue {
    @Prop({ default: 'popup' }) type!: string
    @Prop({ default: 2 }) pageSize!: number
    lists = []
    pagesInfo = {
        type: 1,
        name: '默认页面',
        common: {
            title: '微页面',
            background_type: '0',
            bg_color: '#F5F5F5',
            background_image: ''
        },
        content: []
    }
    pager = new RequestPaging({ size: this.pageSize })

    getList(): void {
        this.pager.request({
            callback: apiSystemThemePage
        })
    }
    handleCreate(data: any) {
        data = data == 'empty' ? this.pagesInfo : data
        this.$emit('select', data)
    }
    created() {
        this.getList()
    }
}
</script>

<style scoped lang="scss">
.template-details {
    color: $--color-text-primary;
    padding: 0 30px;
    .template-item {
        position: relative;
        width: 260px;
        height: 520px;
        background: #ffffff;
        border: $--border-base;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.06);
        margin-bottom: 40px;
        margin-right: 40px;
        &--empty {
            .template-item__img,
            .template-item__mask {
                height: 100%;
            }
        }
        &__img {
            width: 100%;
            height: 460px;
            overflow: hidden;
            img {
                width: 100%;
            }
        }
        &__mask {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 460px;
            display: none;
            background: rgba(0, 0, 0, 0.3);
        }
        &:hover .template-item__mask {
            display: flex;
        }
        &__footer {
            padding: 10px 10px 0;
        }
    }
}
</style>
