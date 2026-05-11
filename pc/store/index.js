import cookieparser from "cookieparser";
import Cookies from "js-cookie";
export const state = () => ({
    token: null,
    category: [],
    publicData: {
        shop: {
            pc_status: 1,
        },
        decoration: {},
        my: {},
        notices: [],
        pc: {},
    },
    config: {},
});

export const mutations = {
    setToken(state, token) {
        state.token = token;
    },
    logout(state) {
        state.token = null;
        Cookies.remove("token");
    },

    setCategory(state, data) {
        state.category = data;
    },
    setConfig(state, data) {
        state.config = data;
    },
    setPublic(state, data) {
        state.publicData = data;
    },
};

export const getters = {
    isLogin: (state) => !!state.token,
    headerMeta: (state) => state.publicData.pc,
    ossDomain: (state) => state.publicData.shop.oss_domain || "",
    getSiteStatistics: (state) => state.publicData.site_statistic,
};

export const actions = {
    async nuxtServerInit({ dispatch, commit }, { req, app }) {
        let cookie = req.headers.cookie;
        if (cookie) {
            const parsed = cookieparser.parse(cookie);
            commit("setToken", parsed.token || "");
        }
        await dispatch("getPublicData");
        await dispatch("getCategory");
    },

    // // 获取分类
    async getCategory({ commit }) {
        const { data, code } = await this.$get("goodsCategory/lists");
        if (code == 1) {
            commit("setCategory", data.lists);
        }
    },

    // 获取配置
    async getConfig({ commit }) {
        const { data, code } = await this.$get("config/getconfig");
        if (code == 1) {
            console.log("getConfig", data);
            commit("setConfig", data);
        }
    },

    async getPublicData({ commit }) {
        const { data, code } = await this.$get("pc/commonData");
        if (code == 1) {
            console.log("getPublicData", data);
            commit("setPublic", data);
        }
    },
};
