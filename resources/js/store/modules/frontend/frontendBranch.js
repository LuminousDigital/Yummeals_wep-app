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

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const originLat = position.coords.latitude;
                        const originLng = position.coords.longitude;

                        const requestPayload = {
                            origin: "",
                            destinations: [
                                `${payload.latitude},${payload.longitude}`,
                            ],
                        };

                        try {
                            const response = await fetch(
                                "https://api.yummealsapp.com/api/v1/calculate-distance",
                                {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-API-KEY":
                                            "z8p53xn6-n2f5-29w7-7193-s500c15553h171620",
                                    },
                                    body: JSON.stringify(requestPayload),
                                }
                            );

                            const result = await response.json();

                            if (!response.ok || !result?.data) {
                                return reject(
                                    new Error(
                                        result?.message ||
                                            "API returned an error"
                                    )
                                );
                            }

                            const isLocationCovered =
                                result.data.distances?.[0]?.isLocationCovered;

                            if (!isLocationCovered) {
                                return reject(
                                    new Error("Location not covered")
                                );
                            }

                            resolve(result.data);
                        } catch (err) {
                            reject(err);
                        }
                    },
                    (error) => {
                        reject(
                            new Error(
                                "Location permission denied or unavailable"
                            )
                        );
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    }
                );
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
