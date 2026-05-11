export default {
    head() {
        // const { clarity_app_id } = this.$store.getters.getSiteStatistics;
        // if (clarity_app_id) {
        //     (function (c, l, a, r, i, t, y) {
        //         c[a] =
        //             c[a] ||
        //             function () {
        //                 (c[a].q = c[a].q || []).push(arguments);
        //             };
        //         t = l.createElement(r);
        //         t.async = 1;
        //         t.src = "https://www.clarity.ms/tag/" + i;
        //         y = l.getElementsByTagName(r)[0];
        //         y.parentNode.insertBefore(t, y);
        //     })(window, document, "clarity", "script", clarity_app_id);
        // }
        const headerMeta = this.$store.getters.headerMeta;
        return {
            title: headerMeta.title,
            link: [
                {
                    rel: "icon",
                    type: "image/x-icon",
                    href: headerMeta.ico,
                },
            ],
            meta: [
                {
                    hid: "description",
                    name: "description",
                    content: headerMeta.description,
                },
                {
                    hid: "keywords",
                    name: "keywords",
                    content: headerMeta.keywords,
                },
            ],
        };
    },
};
