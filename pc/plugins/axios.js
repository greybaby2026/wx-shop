import { Message } from "element-ui";
import { config, version } from "@/config/app";
export default function ({ $axios, redirect, store }, inject) {
    $axios.setBaseURL(`${config.baseUrl}/shopapi/`);
    $axios.onRequest((config) => {
        config.headers.token = store.state.token;
        config.headers.version = version;
    });
    $axios.onResponse((response) => {
        const { code, show, msg } = response.data;
        if (show && msg) {
            code == 1 ? 
            Message({
                message: msg,
                type: "success",
            })
            :Message({
                message: msg,
                type: "error",
            })
        } 
        if (code == -1) {
            store.commit("logout");
            redirect("/account/login");  
        }
    });
    $axios.onError((error) => {
        const code = parseInt(error.response && error.response.status);
        if (code === 400) {
            redirect("/404");
        } else if (code === 500) {
            redirect("/500");
        }
    });
    inject("get", $axios.$get);
    inject("post", $axios.$post);
}
