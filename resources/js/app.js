/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import { createApp } from "vue";
import DefaultComponent from "./components/DefaultComponent";
import router from "./router";
import store from "./store";
import axios from "axios";
import i18n from "./i18n";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import VueSimpleAlert from "vue3-simple-alert";
import VueNextSelect from "vue-next-select";
import "vue-next-select/dist/index.css";
import VueApexCharts from "vue3-apexcharts";
import ENV from "./config/env";
import VariationButton from "../js/components/frontend/components/button/VariationButton.vue";

/* Start tooltip alert code */
const options = {
    timeout: 2000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
};
/* End tooltip alert code */

/* Start axios code*/
const API_URL = ENV.API_URL;
const API_KEY = ENV.API_KEY;

axios.defaults.baseURL = API_URL + "/api";
axios.interceptors.request.use(
    (config) => {
        config.headers["x-api-key"] = API_KEY;
        if (localStorage.getItem("vuex")) {
            const vuex = JSON.parse(localStorage.getItem("vuex"));
            const token = vuex.auth.authToken;
            const language = vuex.globalState.lists.language_code;
            config.headers["Authorization"] = token ? `Bearer ${token}` : "";
            config.headers["x-localization"] = language;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

/* End axios code */

// /* Start axios code*/
// const API_URL = ENV.API_URL;
// const API_KEY = ENV.API_KEY;

// axios.defaults.baseURL = API_URL + "/api";
// axios.interceptors.request.use(
//     (config) => {
//         console.log("Axios Interceptor - Original URL:", config.url);
//         console.log("Axios Interceptor - isWeb property:", config.isWeb);

//         if (config.isWeb === true) {
//             console.log(
//                 "Axios Interceptor - Web route detected, removing /api prefix"
//             );
//             config.baseURL = API_URL;

//             delete config.isWeb;
//         } else {
//             console.log("Axios Interceptor - API route, keeping /api prefix");
//         }

//         config.headers["x-api-key"] = API_KEY;
//         if (localStorage.getItem("vuex")) {
//             const vuex = JSON.parse(localStorage.getItem("vuex"));
//             const token = vuex.auth.authToken;
//             const language = vuex.globalState.lists.language_code;
//             config.headers["Authorization"] = token ? `Bearer ${token}` : "";
//             config.headers["x-localization"] = language;
//         }

//         console.log("Axios Interceptor - Final baseURL:", config.baseURL);
//         console.log(
//             "Axios Interceptor - Final URL will be:",
//             config.baseURL + config.url
//         );

//         return config;
//     },
//     (error) => {
//         console.error("Axios Interceptor - Request error:", error);
//         return Promise.reject(error);
//     }
// );
// /* End axios code */

(function handleSocialAuthHash() {
    try {
        if (location.hash && location.hash.indexOf("#social=") === 0) {
            const b64 = decodeURIComponent(location.hash.substring(8));
            const json = JSON.parse(atob(b64));
            if (json && json.token) {
                // Mark that we came from social to finish profile if currently on edit-profile
                try {
                    if ((location.pathname || "").endsWith("/edit-profile")) {
                        store.commit('setSocialEntry', true);
                    }
                } catch (e) {}

                store.commit("authLogin", json);

                history.replaceState(
                    {},
                    document.title,
                    location.pathname + location.search
                );
                try {
                    if ((location.pathname || "").endsWith("/edit-profile")) {
                        location.replace(location.pathname);
                    }
                } catch (e) {}
                const carts = store.getters["frontendCart/lists"] || [];
                if (Array.isArray(carts) && carts.length > 0) {
                    router.replace({ name: "frontend.checkout" });
                } else {
                    router.replace({ name: "frontend.home" });
                }
            }
        }
    } catch (e) {
        console.warn("Social auth hash parse failed", e);
    }
})();

const app = createApp({});
app.component("default-component", DefaultComponent);
app.component("vue-select", VueNextSelect);
app.component("VariationButton", VariationButton);
app.use(router);
app.use(store);
app.use(VueSimpleAlert);
app.use(VueApexCharts);
app.use(Toast, options);
app.use(i18n);
app.mount("#app");
