import axios from "axios";
import appService from "../../../services/appService";

export const frontendBranch = {
    namespaced: true,
    state: {
        lists: [],
        show: {},
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        show: function (state) {
            return state.show;
        },
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/branch";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit("lists", res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            if (payload) {
                return new Promise((resolve, reject) => {
                    axios.get(`frontend/branch/show/${payload}`).then((res) => {
                        context.commit("show", res.data.data);
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
                });
            }
        },
        // showByLatLong: function (context, payload) {
        //     return new Promise((resolve, reject) => {
        //         let url = "frontend/branch/lat-long";
        //         if (payload) {
        //             url = url + appService.requestHandler(payload);
        //         }
        //         axios.get(url).then((res) => {
        //             resolve(res);
        //         }).catch((err) => {
        //             reject(err);
        //         });
        //     });
        // },

        showByLatLong: function (context, payload) {
            return new Promise((resolve, reject) => {
                if (!payload?.latitude || !payload?.longitude) {
                    return reject(new Error("Invalid destination coordinates"));
                }

                const url = `/frontend/branch/lat-long?latitude=${payload.latitude}&longitude=${payload.longitude}`;

                axios.get(url).then((res) => {
                    if (res.data.data) {
                        resolve(res.data.data);
                    } else {
                        reject(new Error("Location not covered"));
                    }
                }).catch((err) => {
                    reject(new Error(err.response?.data?.message || "Unable to check location coverage"));
                });
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        show: function (state, payload) {
            state.show = payload;
        }
    },
};
