<!-- 异常日志 -->
<template>
    <div class="error-journal">异常日志</div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator'
// import {

// } from "@/api/setting/system_maintain/system_maintain.d.ts"
// import {

// } from "@/api/setting/system_maintain/system_maintain"
import { RequestPaging } from '@/utils/util'
import LsPagination from '@/components/ls-pagination.vue'
@Component({
    components: {
        LsPagination
    }
})
export default class ErrorJournal extends Vue {
    /** S Data **/

    pager: RequestPaging = new RequestPaging()
    /** E Data **/

    /** S Methods **/
    /** E Methods **/

    /** S Life Cycle **/
    created() {}
    /** E Life Cycle **/
}
</script>

<style lang="scss" scoped></style>
