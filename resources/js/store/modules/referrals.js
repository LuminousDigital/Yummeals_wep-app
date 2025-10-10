import axios from 'axios';

const state = {
  administratorReferrals: [],
  customerReferrals: [],
  loading: false,
  error: null,
};

const getters = {
  administratorReferrals: (state) => state.administratorReferrals,
  customerReferrals: (state) => state.customerReferrals,
  loading: (state) => state.loading,
  error: (state) => state.error,
};

const actions = {
      fetchAdministratorReferrals({ commit }, administratorId) {
        commit('setLoading', true);
        return axios.get(`admin/administrators/${administratorId}/referrals`)
          .then(response => {
            commit('setAdministratorReferrals', response.data.data);
            commit('setLoading', false);
            return response;
          })
          .catch(error => {
            commit('setError', error);
            commit('setLoading', false);
            throw error;
          });
      },
      fetchCustomerReferrals({ commit }, customerId) {
        commit('setLoading', true);
        return axios.get(`admin/customers/${customerId}/referrals`)
          .then(response => {
            commit('setCustomerReferrals', response.data.data);
            commit('setLoading', false);
            return response;
          })
          .catch(error => {
            commit('setError', error);
            commit('setLoading', false);
            throw error;
          });
      },
};

const mutations = {
  setAdministratorReferrals(state, referrals) {
    state.administratorReferrals = referrals;
  },
  setCustomerReferrals(state, referrals) {
    state.customerReferrals = referrals;
  },
  setLoading(state, loading) {
    state.loading = loading;
  },
  setError(state, error) {
    state.error = error;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
