export default {
    init: function (key, refer) {
        const TMap_URL = `https://apis.map.qq.com/tools/geolocation/min?key=${key}&referer=${refer}&callback=onLocationCallback`;
        return new Promise((resolve, reject) => {
            // 如果已加载直接返回
            if (typeof window.qq !== "undefined") {
                resolve(new window.qq.maps.Geolocation(key, refer));
                return true;
            }
            // 地图异步加载回调处理
            window.onLocationCallback = function () {
                resolve(new window.qq.maps.Geolocation(key, refer));
            };
            // 插入script脚本
            let scriptNode = document.createElement("script");
            scriptNode.setAttribute("type", "text/javascript");
            scriptNode.setAttribute("src", TMap_URL);
            document.body.appendChild(scriptNode);
        });
    },
};
