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

    signup({ getters, commit }, payload) {
      const url = "auth/signup/register";

      if (!payload.referral_code) {
        payload.referral_code = getters.referralCode || null;
      }

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
    },
  },
};
