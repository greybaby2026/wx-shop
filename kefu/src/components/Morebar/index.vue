<template>
    <div class="morebar">
        <!-- Normal -->
        <div v-show="LoadMap['NORMAL'] === status" @click="$emit('onmore')">
            <slot name="normal">
                <i class="status--normal">_加载更多_</i>
            </slot>
        </div>
        <!-- Loading -->
        <div v-show="LoadMap['LOAD'] === status" @click="$emit('onload')">
            <slot name="load">
                <i class="el-icon-loading status--load"></i>
            </slot>
        </div>
        <!-- Empty -->
        <div v-show="LoadMap['EMPTY'] === status" @click="$emit('onempty')">
            <slot name="empty">
                <i class="status--empty">没有更多了~</i>
            </slot>
        </div>
        <!-- Error -->
        <div v-show="LoadMap['ERROR'] === status" @click="$emit('onerror')">
            <slot name="error"> 
                <i class="status--error">发生了一点错误，请重新加载!</i>
            </slot>
        </div>
    </div>
</template>


<script>
/**
 * 加载更多组件
 * @prop {String} status 状态 E_Load
 * @event on-more 点击加载更多 (无参数) 
 */

import {E_Load} from '@/utils/enum'

export default {
    name: 'Morebar',

    props: {
        status: {
            type: [String, Number],
            require: true,
            // validator: value => E_Load[value] !== undefined
            validator: value => {
                let valid = false
                Object.keys(E_Load).forEach(key => {
                    if (E_Load[key] === value) valid = true
                })
                return valid
            }
        }
    },

    data() {
        return {
            LoadMap: Object.freeze({...E_Load})
        }
    },
}
</script>


<style lang="scss" scoped>
.morebar {
    display: flex;
    justify-content: center;
    padding: 10px 0;
    font-size: 12px;

    .status--normal {
        color: $--color-primary;
        cursor: pointer;
    }

    .status--load {
        color: $--color-primary;
        font-size: 16px;
    }

    .status--empty {
        color: $--color-text-regular;
    }

    .status--error {
        color: $--color-danger;
    }

}
</style>