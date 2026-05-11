<template>
    <div class="footer-container">
        <footer class="footer-wrap flex row-center">
            <a class="" target="_blank" :href="publicData.shop.record_system_link">{{publicData.shop.copyright}} {{publicData.shop.record_number}}</a>
        </footer>
    </div>
</template>

<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState(["publicData"]),
    },
};
</script>

<style lang="scss" scoped>
.footer-container {
    margin-top: 38px;
    .footer-box {
        height: 120px;
        background-color: #222;
        min-width: 1180px;
        .server-box {
            width: 1180px;
            padding: 36px 40px;
            border-bottom: 1px solid #707070;
            .name {
                color: #e5e5e5;
            }
        }
    }
    .footer-wrap {
        min-width: 1180px;
        // background-color: #222;
        height: 60px;
        color: #ccc;
    }
}
</style>
