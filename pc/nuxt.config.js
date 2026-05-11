require("dotenv").config();
export default {
    // Global page headers: https://go.nuxtjs.dev/config-head
    head: {
        title: "",
        htmlAttrs: {
            lang: "zh",
        },
        meta: [
            {
                charset: "utf-8",
            },
            {
                name: "viewport",
                content: "width=device-width, initial-scale=1",
            },
            {
                hid: "description",
                name: "description",
                content: "",
            },
            {
                name: "format-detection",
                content: "telephone=no",
            },
        ],
        link: [
            {
                rel: "icon",
                type: "image/x-icon",
                href: "/",
            },
        ],
        script: [
            {
                src: "https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js",
                type: "text/javascript",
                mode: "client",
            },
        ],
    },

    // Global CSS: https://go.nuxtjs.dev/config-css
    css: [
        "@/assets/css/element-variables.scss",
        "@/assets/css/common.scss",
        "@/assets/css/reset.scss",
        "@/assets/fonts/iconfont.css",
        "swiper/css/swiper.css",
    ],

    // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
    plugins: [
        "@/plugins/element-ui",
        "@/plugins/axios",
        "@/plugins/init",
        "@/plugins/mixins",
        {
            src: "@/plugins/vue-awesome-swiper",
            mode: "client",
        },
        {
            src: "@/plugins/vueqr.js",
            mode: "client",
        },
        {
            src: "@/plugins/tool-code.js",
            mode: "client",
        },
    ],

    // Auto import components: https://go.nuxtjs.dev/config-components
    components: true,

    // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
    buildModules: [],
    styleResources: {
        scss: "./assets/css/variables.scss",
    },
    // Modules: https://go.nuxtjs.dev/config-modules
    modules: [
        // https://go.nuxtjs.dev/axios
        "@nuxtjs/axios",
        "@nuxtjs/style-resources",
    ],

    // Axios module configuration: https://go.nuxtjs.dev/config-axios
    axios: {},

    // Build Configuration: https://go.nuxtjs.dev/config-build
    build: {
        transpile: [/^element-ui/],
    },
    ssr: false,
    router: {
        base: "/pc/",
        // middleware: ['route']
    },
    env: {
        API_URL: process.env.APP_BASE_API,
    },
};
