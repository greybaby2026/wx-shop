<template>
    <div class="user-wallet-table">
        <el-table :data="list" style="width: 100%">
            <el-table-column prop="type_desc" label="类型" />
            <el-table-column prop="change_amount_desc" label="金额">
                <div slot-scope="scope" :class="{'primary': scope.row.action != 1}">
                    {{scope.row.change_amount_desc}}
                </div>
            </el-table-column>
            <el-table-column prop="create_time" label="时间" />
        </el-table>
    </div>
</template>

<script>
export default {
    props: {
        list: {
            type: Array,
            default: () => [],
        },
        type: {
            type: String,
            default: "all",
        },
    },
    methods: {},
};
</script>

<style lang="scss">
.user-wallet-table {
    .el-table {
        color: #222;
    }
    .el-table .el-button--text {
        color: #222;
        font-weight: 400;
    }
    .el-table th {
        background-color: #f2f2f2;
    }
    .el-table thead {
        color: #555555;
        font-weight: 400;
    }
}
</style>