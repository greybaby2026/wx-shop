<template>
    <div class="shop">
        <div class="link-list flex flex-wrap">
            <div
                class="link-item"
                :class="{ active: link.path == item.path }"
                v-for="(item, index) in linkList"
                :key="index"
                @click="link = item"
            >
                {{ item.name }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { apiLinkList, apiPcLinkList } from '@/api/app'
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component({
    components: {}
})
export default class Shop extends Vue {
    @Prop() value!: any
    @Prop() type!: string
    @Prop({ default: 'mobile' }) client!: string
    linkList = []

    get link() {
        return this.value
    }
    set link(val) {
        this.$emit('input', val)
    }

    getLists() {
        const link =
            this.client == 'mobile'
                ? apiLinkList({
                      type: this.type
                  })
                : apiPcLinkList({
                      type: this.type
                  })
        link.then(res => {
            this.linkList = res
        })
    }
    created() {
        this.getLists()
    }
}
</script>
<style lang="scss" scoped>
.shop {
    .link-list {
        .link-item {
            border: $--border-base;
            padding: 6px 15px;
            margin-right: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 4px;
            &.active {
                border-color: $--color-primary;
            }
        }
    }
}
</style>
