<template>
    <footer class="footer-container" v-if="footer.show">
        <div class="footer-tags flex">
            <div
                class="tags-item flex flex-1 row-center"
                v-for="(item, index) in footer.content.data"
                :key="index"
            >
                <img class="tags-img" :src="$getImageUri(item.url)" alt="" />
                <div class="m-l-10 xxl">{{ item.name }}</div>
            </div>
        </div>
        <div class="copyright flex row-center">
            <div v-html="shop.copyright2"></div>
            <!-- <a class="white xs" target="_blank" :href="shop.record_system_link"
                >{{ shop.copyright }} {{ shop.record_number }}</a
            > -->
        </div>
        <div class="footer-img">
            <div
                v-for="(item, index) in shop.copyright_auth.list"
                class="flex"
                style="cursor: pointer"
                @click="handleClick(item.link)"
            >
                <img
                    class="tags-img m-r-16"
                    :src="item.image"
                    alt=""
                    v-if="shop.copyright_auth.type == 2"
                />
                <div class="" v-else>
                    <span class="m-l-5 m-r-5 color-ccc" v-if="index != 0"
                        >|</span
                    >

                    {{ item.name }}
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { mapState, mapGetters } from "vuex";
export default {
    computed: {
        ...mapState(["publicData"]),
        shop() {
            return this.publicData.shop || {};
        },
        footer() {
            return this.publicData.decoration.footer || {};
        },
    },
    methods: {
        handleClick(link) {
            window.open(link);
        },
    },
};
</script>

<style lang="scss" scoped>
.footer-container {
    background-color: #101010;
    color: #ccc;
    min-width: 1180px;
    padding-bottom: 30px;
    .footer-tags {
        width: 1180px;
        margin: 0 auto;
        padding: 35px 0;
        border-bottom: 1px solid #ccc;
        .tags-img {
            width: 50px;
            height: 50px;
        }
    }
    .footer-img {
        width: 1180px;
        margin: 0 auto;
        padding: 0;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;

        .tags-img {
            width: 103px;
            height: 32px;
            margin-bottom: 10px;
        }
    }
    .copyright {
        margin: 16px auto;
        width: 1180px;
        color: #ccc;
    }
}
.img {
    display: flex;
}
.color-ccc {
    color: rgb(106, 105, 105) !important;
}
</style>
