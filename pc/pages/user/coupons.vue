<template>
    <div class="user-coupons">
        <div class="coupons-header lg">
            我的优惠券
        </div>

        <div class="tabs">
            <el-button v-for="(value,key,index) in extend" @click="changeTabs(index)" :key="key" class="button m-l-18"
                :class="index==active?'active':''" type="primary">
                {{coupons[index].title}}({{value}})</el-button>

            <div v-for="(item2, index2) in coupons" :key="index2">
                <div class="m-t-20" v-if="index2==active">
                    <coupons-list v-if="item2.hasData" :list="item2.list" :type="active" />
                    <div class="data-null" v-else>
                        <img style="width: 150px;height: 150px;" src="@/assets/images/coupon_null.png" />
                        <div class="muted xs">暂无优惠券~</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import couponsList from "@/components/couponsList.vue";
import headerMixins from "@/mixins/header"
export default {
    mixins: [headerMixins],
    layout: "user-layout",
    components: {
        couponsList,
    },
    data() {
        return {
            active: 0,
            extend: {
                normal: 0,
                use: 0,
                invalid: 0,
            },
            coupons: [
                {
                    title: "可使用",
                    type: 0,
                    list: [],
                    hasData: true,
                },
                {
                    title: "已使用",
                    type: 1,
                    list: [],
                    hasData: true,
                },
                {
                    title: "已过期",
                    type: 2,
                    list: [],
                    hasData: true,
                },
            ],
        };
    },
    mounted() {
        this.getMyCoupons();
    },
    methods: {
        changeTabs(index) {
            this.active = index;
            this.getMyCoupons();
        },
        async getMyCoupons() {
            let { data, code } = await this.$get("coupon/my", {
                params: {
                    status: this.coupons[this.active].type + "",
                    page_size: 100,
                },
            });
            console.log(data, code);
            if (code == 1) {
                for (const key in this.extend) {
                    this.$set(this.extend, key, data.extend[key]);
                }
                this.changeData(data);
            }
        },
        changeData(data) {
            this.coupons.some((item, index) => {
                console.log(data, index);
                if (index == this.active) {
                    Object.assign(item, {
                        list: data.lists,
                        hasData: data.lists.length,
                    });
                    return true;
                }
            });
        },
    },
};
</script>

<style lang="scss">
.user-coupons {
    width: 980px;
    padding: 10px;
    .coupons-header {
        padding: 10px 5px;
        border-bottom: 1px solid #e5e5e5;
    }

    .tabs {
        padding: 15px 0;
        .button {
            width: 104px;
            height: 30px;
            line-height: 0px;
            display: inline-block;
            background: #fff;
            color: #666;
            border: 1px solid #e5e5e5;
        }
        .active {
            color: #fff;
            border: 0;
            background: $--color-primary;
        }

        .data-null {
            text-align: center;
            padding-top: 100px;
        }
    }
}
</style>