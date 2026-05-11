<template>
    <div class="deliver-search-container">
        <el-dialog
            :visible.sync="showDialog"
            top="10vh"
            width="900px"
            height="500px"
            title="物流查询"
            lock-scroll
        >
            <div class="deliver-box">
                <el-tabs v-model="activeName" @tab-click="handleClick">
                    <el-tab-pane
                        :label="
                            item.send_type == 1
                                ? `包裹${index + 1}(共${deliveryNum(
                                      item.order_goods_info
                                  )}件)`
                                : `无需物流（共${deliveryNum(
                                      item.order_goods_info
                                  )}件）`
                        "
                        :name="String(index)"
                        v-for="(item, index) in deliverOrder.parcel_info"
                        :key="item.id"
                    ></el-tab-pane>
                    <el-tab-pane
                        v-if="deliverOrder.wait_delivery_goods.length != 0"
                        label="待发货"
                        name="waitDelivery"
                    ></el-tab-pane>
                </el-tabs>
                <div class="flex">
                    <div
                        class="recode-img"
                        v-for="i in activeName == 'waitDelivery'
                            ? deliverOrder.wait_delivery_goods
                            : parcelInfo.order_goods_info"
                        :key="i.id"
                    >
                        <el-image
                            style="width: 100%; height: 100%"
                            fit="cover"
                            :src="i.goods_image"
                        />
                        <div class="float-count flex row-center">
                            x{{ i.delivery_num }}
                        </div>
                    </div>
                </div>
                <el-divider></el-divider>
                <div class="m-t-20 m-b-20" v-if="activeName != 'waitDelivery'">
                    <div
                        class="flex col-center row-between"
                        v-if="parcelInfo.send_type == 1"
                    >
                        <div class="flex col-center">
                            <el-image
                                style="height: 30px; width: 30px"
                                fit="cover"
                                :src="$getImageUri(parcelInfo.express_icon)"
                            />
                            <span class="m-l-20">
                                {{ parcelInfo.express_name }}
                            </span>
                            <span class="m-l-10">
                                {{ parcelInfo.invoice_no }}
                            </span>
                        </div>
                        <div
                            class="copy-btn primary flex row-center"
                            @click="onCopy(parcelInfo.invoice_no)"
                        >
                            复制单号
                        </div>
                    </div>
                    <div v-else>无需物流</div>
                </div>
                <el-divider
                    v-if="
                        parcelInfo.send_type == 1 &&
                        activeName != 'waitDelivery'
                    "
                ></el-divider>
                <div
                    class="m-t-30"
                    v-if="
                        parcelInfo.send_type == 1 &&
                        activeName != 'waitDelivery'
                    "
                >
                    <el-timeline
                        v-infinite-scroll="load"
                        style="overflow: auto; height: 300px"
                    >
                        <el-timeline-item
                            v-for="(item, index) in parcelInfo.logistics_info
                                .traces"
                            :key="index"
                            timestamp="暂无物流信息"
                            v-show="item == '暂无物流信息'"
                        >
                            暂无物流信息
                        </el-timeline-item>
                        <el-timeline-item
                            v-for="(item, index) in parcelInfo.logistics_info
                                .traces"
                            :key="index"
                            :timestamp="item[0]"
                            v-show="item != '暂无物流信息'"
                        >
                            {{ item[1] }}
                        </el-timeline-item>
                    </el-timeline>
                </div>
                <el-divider></el-divider>

                <div class="m-t-30 flex">
                    <span class="icon">收</span>
                    <div class="m-l-20">
                        <div>
                            {{ deliverOrder.receipt_address_info.addresss }}
                        </div>
                        <div>
                            <span class="xxs muted">
                                {{ deliverOrder.receipt_address_info.contact }}
                            </span>

                            <span class="xxs muted">
                                {{ deliverOrder.receipt_address_info.mobile }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- <div class="deliver-recode-box flex">

                    <div class="recode-info-container m-l-10">
                        <div class="flex">
                            <div class="recode-label">物流状态：</div>
                            <div class="primary lg" style="font-weight: 500">

                            </div>
                        </div>
                        <div class="flex" style="margin: 6px 0">

                        </div>
                        <div class="flex">
                            <div class="recode-label">快递单号：</div>

                            <div
                                class="copy-btn primary flex row-center"
                                @click="onCopy"
                            >
                                复制
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="deliver-flow-box m-t-16">
                    <el-timeline>
                        <!-- 收货 -->
                        <!-- <el-timeline-item v-if="deliverFinish.tips">
                            <div>
                                <div class="flex lg">
                                    <div class="m-r-8" style="font-weight: 500">
                                        {{ deliverTake.contacts }}
                                    </div>
                                    <div style="font-weight: 500">
                                        {{ deliverTake.mobile }}
                                    </div>
                                </div>
                                <div class="lighter m-t-8">
                                    {{ deliverTake.address }}
                                </div>
                            </div>
                        </el-timeline-item> -->
                        <!-- 交易状态 -->
                        <!-- <el-timeline-item
                            v-if="deliverFinish.tips"
                            :timestamp="deliverFinish.time"
                        >
                            <div class="time-line-title">
                                {{ deliverFinish.title }}
                            </div>
                            <div>{{ deliverFinish.tips }}</div>
                        </el-timeline-item> -->
                        <!-- 跟踪物流 -->
                        <!-- <el-timeline-item
                            v-if="delivery.traces && delivery.traces.length"
                            :timestamp="delivery.time"
                        >
                            <div class="time-line-title m-b-8">
                                {{ delivery.title }}
                            </div>
                            <el-timeline-item
                                v-for="(item, index) in delivery.traces"
                                :key="index"
                                :timestamp="item[0]"
                            >
                                <div class="muted">{{ item[1] }}</div>
                            </el-timeline-item>
                        </el-timeline-item> -->
                        <!-- 完成 -->
                        <!-- <el-timeline-item
                            v-if="deliverShipment.tips"
                            :timestamp="deliverShipment.time"
                        >
                            <div class="time-line-title">
                                {{ deliverShipment.title }}
                            </div>
                            <div>{{ deliverShipment.tips }}</div>
                        </el-timeline-item> -->
                        <!-- 下单 -->
                        <!-- <el-timeline-item
                            v-if="deliverBuy.tips"
                            :timestamp="deliverBuy.time"
                        >
                            <div class="time-line-title">
                                {{ deliverBuy.title }}
                            </div>
                            <div>{{ deliverBuy.tips }}</div>
                        </el-timeline-item> -->
                    </el-timeline>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        aid: {
            type: Number | String,
        },
    },
    data() {
        return {
            showDialog: false,
            deliverBuy: {},
            delivery: {},
            deliverFinish: {},
            deliverOrder: {
                receipt_address_info: {},
                wait_delivery_goods: [],
            },

            deliverShipment: {},
            deliverTake: {},
            timeLineArray: [],
            activeName: 0,
            parcelInfo: {},
        };
    },
    watch: {
        value(val) {
            console.log(val, "val");
            this.showDialog = val;
        },
        showDialog(val) {
            if (val) {
                if (this.aid) {
                    this.timeLineArray = [];
                    this.getDeliverTraces();
                }
            }
            this.$emit("input", val);
        },
    },
    methods: {
        deliveryNum(val) {
            let Num = 0;
            val.map((i) => {
                Num += Number(i.delivery_num);
            });
            return Num;
        },
        handleClick(val) {
            if (val.name == "waitDelivery") {
                this.activeName = "waitDelivery";
            } else {
                this.parcelInfo = this.deliverOrder.parcel_info[val.index];
                this.activeName = val.index;
            }
        },
        async getDeliverTraces() {
            let data = {
                id: this.aid,
            };
            let res = await this.$get("order/orderTraces", { params: data });
            if (res.code == 1) {
                let { parcel_info, wait_delivery_goods } = res.data;
                this.deliverOrder = res.data;
                this.parcelInfo = parcel_info[0];

                // this.deliverBuy = buy;
                // this.delivery = delivery;
                // this.deliverFinish = finish;
                // this.deliverShipment = shipment;
                // this.deliverTake = take;
                // this.timeLineArray.push(this.deliverFinish);
                // this.timeLineArray.push(this.delivery);
                // this.timeLineArray.push(this.deliverShipment);
                // this.timeLineArray.push(this.deliverBuy);
                // console.log(this.timeLineArray);
            }
        },
        onCopy(invoice_no) {
            // this.deliverOrder.invoice_no;
            let oInput = document.createElement("input");
            oInput.value = invoice_no;
            document.body.appendChild(oInput);
            oInput.select();
            document.execCommand("Copy");
            this.$message.success("复制成功");
            oInput.remove();
        },
    },
};
</script>

<style lang="scss" scoped>
.deliver-search-container {
    .deliver-box {
        .deliver-recode-box {
            padding: 10px 20px;
            background-color: #f2f2f2;

            .recode-info-container {
                flex: 1;
                .recode-label {
                    width: 70px;
                }
            }
        }
        .deliver-flow-box {
            padding-left: 15px;
        }
        .time-line-title {
            font-weight: 500px;
            font-size: 16px;
            margin-bottom: 10px;
        }
    }
}
.recode-img {
    position: relative;

    width: 72px;
    height: 72px;
    margin-right: 5px;
    .float-count {
        position: absolute;
        bottom: 0;
        height: 20px;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        font-size: 12px;
    }
}
.copy-btn {
    cursor: pointer;
}
.icon {
    background-color: #ffeacb;
    border-radius: 50%;
    color: #f45d27;
    padding: 8px 10px;
}
</style>
