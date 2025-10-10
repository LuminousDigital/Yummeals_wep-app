import axios from "axios";

const defaultFilters = {
  search: '',
  sort_by: 'referrals_count',
  sort_order: 'desc',
  page: 1
};

const state = {
  leaderboard: [],
  topThree: [],
  pagination: {
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: 0,
    to: 0
  },
  loading: false,
  filters: { ...defaultFilters }
};

const mutations = {
  SET_LEADERBOARD(state, data) {
    state.leaderboard = data.data || [];
    state.pagination = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      per_page: data.per_page || 10,
      total: data.total || 0,
      from: data.from || 0,
      to: data.to || 0
    };
  },
  SET_TOP_THREE(state, data) {
    state.topThree = data.slice(0, 3);
  },
  SET_LOADING(state, loading) {
    state.loading = loading;
  },
  SET_FILTERS(state, filters) {
    state.filters = { ...state.filters, ...filters };
  },
  RESET_FILTERS(state) {
    state.filters = { ...defaultFilters };
  }
};

const actions = {
  async fetchLeaderboard({ commit, state }) {
    commit('SET_LOADING', true);
    try {
      const params = new URLSearchParams(state.filters);
      const response = await axios.get(`admin/referrals/leaderboard?${params}`);
      commit('SET_LEADERBOARD', response.data);
      commit('SET_TOP_THREE', response.data.data || []);
    } catch (error) {
      console.error('Error fetching leaderboard:', error);
    } finally {
      commit('SET_LOADING', false);
    }
  },
  setFilters({ commit, dispatch }, filters) {
    commit('SET_FILTERS', filters);
    dispatch('fetchLeaderboard');
  },
  resetFilters({ commit, dispatch }) {
    commit('RESET_FILTERS');
    dispatch('fetchLeaderboard');
  }
};

const getters = {
  getLeaderboard: state => state.leaderboard,
  getTopThree: state => state.topThree,
  getPagination: state => state.pagination,
  isLoading: state => state.loading,
  getFilters: state => state.filters,
  page: state => state.filters.page
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
};
