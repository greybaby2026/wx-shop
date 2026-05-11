<template>
    <div class="category flex">
        <div class="m-r-20">选择分类</div>
        <el-cascader
            ref="cascader"
            :value="params.id"
            :options="lists"
            :props="{
                checkStrictly: true,
                label: 'name',
                value: 'id',
                children: 'sons',
                emitPath: false
            }"
            clearable
            filterable
            size="small"
            @change="onSelect"
        ></el-cascader>
    </div>
</template>

<script lang="ts">
import { apiCategoryLists } from '@/api/goods'
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component({
    components: {}
})
export default class Shop extends Vue {
    $refs!: { cascader: any }
    @Prop() value!: any

    lists = []
    get params() {
        return this.value
    }
    getLists() {
        apiCategoryLists({ pager_type: 1 }).then(res => {
            this.lists = res?.lists
        })
    }
    onSelect() {
        const node = this.$refs.cascader.getCheckedNodes()
        this.$emit('input', node[0].data)
    }
    created() {
        this.getLists()
    }
}
</script>
<style lang="scss" scoped></style>
