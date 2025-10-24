import axios from "axios";

export const frontendSignup = {
  namespaced: true,

  state: {
    phone: {},
    referralCode: null,
  },

  getters: {
    phone(state) {
      return state.phone;
    },
    referralCode(state) {
      return (
        state.referralCode ||
        localStorage.getItem("referral_code") ||
        sessionStorage.getItem("referral_code")
      );
    },
  },

  actions: {
    setReferralCode({ commit }, code) {
      commit("setReferralCode", code);
      if (code) {
        localStorage.setItem("referral_code", code);
        sessionStorage.setItem("referral_code", code);
      }
    },

    otp({ commit }, payload) {
      return axios
        .post("auth/signup/otp", payload)
        .then((res) => {
          commit("setPhone", payload);
          return res;
        });
    },

    verify({ commit }, payload) {
      return axios
        .post("auth/signup/verify", payload)
        .then((res) => {
          commit("setVerified", true);
          return res;
        });
    },

    signup({ commit }, payload) {
      const url = "auth/signup/register";


      return axios
        .post(url, payload)
        .then((res) => {
          commit("resetState");
          return res;
        });
    },

    reset({ commit }) {
      commit("resetState");
    },
  },

  mutations: {
    setPhone(state, payload) {
      state.phone.otp = payload;
    },
    setVerified(state, status) {
      state.phone.status = status;
    },
    setReferralCode(state, code) {
      state.referralCode = code;
    },
    resetState(state) {
      state.phone = {};
      state.referralCode = null;
    },
  },
    // namespaced: true,
    // state: {
    //     phone: {},
    // },
    // getters: {
    //     phone: function (state) {
    //         return state.phone;
    //     },
    // },
    // actions: {
    //     otp: function (context, payload) {
    //         return new Promise((resolve, reject) => {
    //             let url = "auth/signup/otp";
    //             axios.post(url,payload).then((res) => {
    //                 context.commit("phone", payload);
    //                 resolve(res);
    //             }).catch((err) => {
    //                 reject(err);
    //             });
    //         });
    //     },
    //     verify: function (context, payload) {
    //         return new Promise((resolve, reject) => {
    //             let url = "auth/signup/verify";
    //             axios.post(url,payload).then((res) => {
    //                 context.commit("verify",true)
    //                 resolve(res);
    //             }).catch((err) => {
    //                 reject(err);
    //             });
    //         });
    //     },
    //     signup: function (context, payload) {
    //         return new Promise((resolve, reject) => {
    //             let url = "auth/signup/register";
    //             axios.post(url,payload).then((res) => {
    //                 context.commit('reset');
    //                 resolve(res);
    //             }).catch((err) => {
    //                 reject(err);
    //             });
    //         });
    //     },
    //     reset: function (context) {
    //         context.commit('reset');
    //     },
    // },
    // mutations: {
    //     phone: function (state, payload) {
    //         state.phone.otp = payload;
    //     },
    //     verify: function (state, payload) {
    //         state.phone.status = payload;
    //     },
    //     reset: function(state) {
    //         state.phone = {};
    //     }
    // },
};
