<template>
    <div class="loading-more" infinite-scroll-distance="50" v-infinite-scroll="load" :infinite-scroll-disabled="disabled">
        <slot></slot>
        <morebar :status="status">
        </morebar>
    </div>
</template>


<script>
/**
 * 分页组件
 * @prop {String} value 状态 E_Load
 * @event on-more 点击加载更多 (无参数) 
 */

import {E_Load} from '@/utils/enum'
import Morebar from '@/components/Morebar'
export default {

    props: {
        value: {
            type: [String, Number]
        }
    },
    components: {
        Morebar
    },
    data() {
        return {
            
        }
    },
    computed: {
        disabled() {
            return this.status == E_Load['LOAD'] || this.status == E_Load['EMPTY']
        },
        status: {
            get() {
                return this.value
            },
            set(val) {
                this.$emit('input', val)
            }
        }
    },
    methods: {
        load() {
            this.status = E_Load['LOAD']
            this.$emit('load')
        }
    }
}
</script>


<style lang="scss" scoped>

</style>