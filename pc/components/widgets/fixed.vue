<template>
    <div class="fixed" v-if="fixed.show">
        <div class="float-nav bg-white">
            <div class="nav-list">
                <template v-for="(item, index) in fixed.content.data">
                    <div :key="index">
                        <div
                            class="item flex-col col-center lighter"
                            v-if="item.type == 'nav'"
                            @mouseenter="handleHover(index, true)"
                            @mouseleave="handleHover(index, false)"
                        >
                            <app-link
                                :to="{
                                    path: item.link.path,
                                    query: item.link.params,
                                }"
                            >
                                <img
                                    :src="
                                        $getImageUri(
                                            ishover(index)
                                                ? item.select_icon || item.icon
                                                : item.icon
                                        )
                                    "
                                    class="icon-img"
                                />
                                <div
                                    class="xs m-t-5"
                                    v-if="fixed.content.style == 1"
                                >
                                    {{ item.name }}
                                </div>
                            </app-link>
                        </div>
                        <div
                            v-else
                            @mouseenter="handleHover(index, true)"
                            @mouseleave="handleHover(index, false)"
                        >
                            <el-popover
                                placement="left"
                                trigger="hover"
                                v-if="serviceConfig.way != 4"
                            >
                                <div
                                    style="text-align: center; line-height: 1.5"
                                    class="flex-col row-center p-5 nr"
                                >
                                    <img
                                        style="width: 140px; height: 140px"
                                        :src="serviceConfig.qr_code"
                                        alt
                                    />
                                    <div class="m-t-5">
                                        {{ serviceConfig.name }}
                                    </div>
                                    <div class="m-t-5 m-b-5 xxs muted">
                                        {{ serviceConfig.phone }}
                                    </div>
                                    <div class="xxs muted">
                                        {{ serviceConfig.remarks }}
                                    </div>
                                </div>
                                <div
                                    class="item flex-col col-center row-center lighter"
                                    slot="reference"
                                >
                                    <img
                                        :src="
                                            $getImageUri(
                                                ishover(index)
                                                    ? item.select_icon ||
                                                          item.icon
                                                    : item.icon
                                            )
                                        "
                                        class="icon-img"
                                    />
                                    <div
                                        class="xs m-t-5"
                                        v-if="fixed.content.style == 1"
                                    >
                                        {{ item.name }}
                                    </div>
                                </div>
                            </el-popover>
                            <div
                                class="item flex-col col-center row-center lighter"
                                @click="handleService"
                                v-else
                            >
                                <img
                                    :src="
                                        $getImageUri(
                                            ishover(index)
                                                ? item.select_icon || item.icon
                                                : item.icon
                                        )
                                    "
                                    class="icon-img"
                                />
                                <div
                                    class="xs m-t-5"
                                    v-if="fixed.content.style == 1"
                                >
                                    {{ item.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";
export default {
    data() {
        return {
            server: {},
            hoverLists: [],
            serviceConfig: {},
        };
    },
    created() {
        this.getService();
    },
    methods: {
        handleService() {
            window.open(this.serviceConfig.kefu_link, "_blank");
        },
        async getService() {
            const { data, code } = await this.$get("service/getConfig");
            if (code == 1) {
                this.server = data;
            }

            const res = await this.$get("config/getKefuConfig");
            if (res.code == 1) {
                this.serviceConfig = res.data.pc;
                console.log(res.data.pc);
            }
        },
        handleHover(index, ishover) {
            this.$set(this.hoverLists, index, ishover);
        },
    },
    computed: {
        ...mapState(["publicData"]),
        fixed() {
            return this.publicData.decoration.fixed || {};
        },
        ishover() {
            return (index) => {
                return this.hoverLists[index];
            };
        },
    },
};
</script>

<style lang="scss" scoped>
.float-nav {
    width: 70px;
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 999;
    box-shadow: -3px 1px 2px rgba(0, 0, 0, 0.04);
    .nav-list {
        bottom: 120px;
        .item {
            padding: 10px 0;
            margin: 0 10px;
            text-align: center;
            cursor: pointer;
            &:hover {
                color: $--color-primary;
            }
            .icon-img {
                width: 28px;
                height: 28px;
            }
        }
    }
}
</style>
