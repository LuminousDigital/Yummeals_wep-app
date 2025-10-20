import axios from 'axios'


export const auth = {
    state: {
        authStatus: false,
        authToken: null,
        authBranchId: '',
        authInfo: {},
        authMenu: [],
        resetInfo: {
            email: null
        },
        authPermission: {},
        authDefaultPermission: {},
        authDefaultMenu: {},
        socialEntry: false
    },
    getters: {
        authStatus: function (state) {
            return state.authStatus;
        },
        authToken: function (state) {
            return state.authToken;
        },
        authBranchId: function (state) {
            return state.authBranchId;
        },
        authInfo: function (state) {
            return state.authInfo;
        },
        authMenu: function (state) {
            return state.authMenu;
        },
        authPermission: function (state) {
            return state.authPermission;
        },
        authDefaultPermission: function (state) {
            return state.authDefaultPermission;
        },
        authDefaultMenu: function (state) {
            return state.authDefaultMenu;
        },
        resetInfo: function (state) {
            return state.resetInfo;
        },
        socialEntry: function (state) {
            return state.socialEntry;
        }
    },
    actions: {
        login: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/login', payload).then((res) => {
                    context.commit('authLogin', res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        authcheck: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/authcheck', payload).then((res) => {
                    if (res.data.status === false) {
                        context.commit('authLogout');
                    };
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        logout: function (context) {
            return new Promise((resolve, reject) => {
                axios.post('auth/logout').then((res) => {
                    context.commit('authLogout');

                    // Clear persisted/vuex-managed client state on logout
                    try {
                        context.dispatch('frontendCart/resetCart', null, { root: true });
                        context.dispatch('posCart/resetCart', null, { root: true });
                        context.dispatch('tableCart/resetCart', null, { root: true });
                        context.dispatch('frontendSignup/reset', null, { root: true });
                        context.dispatch('GuestSignup/reset', null, { root: true });
                        context.dispatch('globalState/reset', null, { root: true });
                        // Remove any auxiliary persisted values
                        window?.localStorage?.removeItem('referral_code');
                        window?.sessionStorage?.removeItem('referral_code');
                    } catch (e) {}

                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        forgetPassword: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password', payload).then((res) => {
                    context.commit('forgetPassword', payload);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        verifyCode: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password/verify-code', payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        resetPassword: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password/reset-password', payload).then((res) => {
                    context.commit('resetPassword', res.data.token);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        updateAuthInfo: function (context, payload) {
            return new Promise((resolve, reject) => {
                if (context.state.authInfo.id === payload.id) {
                    context.commit('authInfo', payload);
                    resolve(payload);
                } else {
                    reject('user data not match');
                }
            });
        },
        GuestLoginVerify: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/guest-signup/verify', payload).then((res) => {
                    context.commit('authLogin', res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        loginDataReset: function (context) {
            context.commit('authLogout');
            try {
                context.dispatch('frontendCart/resetCart', null, { root: true });
                context.dispatch('posCart/resetCart', null, { root: true });
                context.dispatch('tableCart/resetCart', null, { root: true });
                context.dispatch('frontendSignup/reset', null, { root: true });
                context.dispatch('GuestSignup/reset', null, { root: true });
                context.dispatch('globalState/reset', null, { root: true });
                window?.localStorage?.removeItem('referral_code');
                window?.sessionStorage?.removeItem('referral_code');
            } catch (e) {}
        }
    },
    mutations: {
        authLogin: function (state, payload) {
            state.authStatus = true;
            state.authToken = payload.token;
            state.authBranchId = payload.branch_id;
            state.authInfo = payload.user;
            state.authMenu = payload.menu;
            state.authPermission = payload.permission;
            state.authDefaultPermission = payload.defaultPermission;
            state.authDefaultMenu = payload.defaultMenu;
        },
        authLogout: function (state) {
            state.authStatus = false;
            state.authToken = null;
            state.authBranchId = '';
            state.authInfo = {};
            state.authMenu = [];
            state.authPermission = {};
            state.authDefaultPermission = {};
            state.authDefaultMenu = {};
            state.socialEntry = false;
        },
        forgetPassword: function (state, payload) {
            state.resetInfo = {
                email: payload.email
            }
        },
        resetPassword: function (state) {
            state.resetInfo = {
                email: null
            }
        },
        authInfo: function (state, payload) {
            state.authInfo = payload;
        },
        setSocialEntry: function (state, payload) {
            state.socialEntry = !!payload;
        }
    },
}
