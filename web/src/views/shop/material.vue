<template>
    <div class="ls-material flex-col">
        <div class="ls-material__content ls-card flex-col">
            <div class="ls-content__tabs flex-col">
                <el-tabs class="flex-col" v-model="activeName" v-loading="pager.loading" @tab-click="getList">
                    <el-tab-pane
                        v-for="(item, index) in paneLists"
                        :key="index"
                        :label="item.label"
                        :name="item.name"
                        lazy
                    >
                        <material-item :type="item.type" mode="pages" size="160" />
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { RequestPaging } from '@/utils/util'
import { Component, Prop, Vue } from 'vue-property-decorator'
import MaterialItem from '@/components/material-select/material.vue'
@Component({
    components: {
        MaterialItem
    }
})
export default class Material extends Vue {
    activeName = 'image'
    paneLists = [
        {
            label: '图片',
            name: 'image',
            type: 'image'
        },
        {
            label: '视频',
            name: 'video',
            type: 'video'
        }
    ]
    pager = new RequestPaging()

    getList() {}
}
</script>

<style lang="scss" scoped>
.ls-material {
    min-height: calc(100vh - #{$--header-height} - 50px);
    .ls-content__tabs {
        flex: 1;
        box-sizing: border-box;
        padding: 0 24px;
        ::v-deep.el-tabs {
            flex: 1;
            &__header {
                margin-bottom: 0 !important;
            }
            &__content,
            .el-tab-pane {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
        }
    }

    &__top {
        flex: none;
    }
    &__content {
        padding: 0;
    }
}
</style>
