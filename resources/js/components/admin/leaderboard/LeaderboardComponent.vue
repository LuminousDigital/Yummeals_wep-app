<template>
    <LoadingContentComponent :props="{ isActive: loading }" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">
                    <i class="lab lab-trophy"></i>{{ $t("menu.leaderboard") }}
                </h3>
                <div class="db-card-filter">
                    <TableLimitComponent
                        :method="list"
                        :search="filters"
                        :page="paginationPage"
                    />
                    <FilterComponent
                        @click.prevent="handleSlide('leaderboard-filter')"
                    />
                </div>
            </div>
            <div class="table-filter-div" id="leaderboard-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="applyFilters">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label
                                for="search"
                                class="db-field-title after:hidden"
                                >{{ $t("label.search") }}</label
                            >
                            <input
                                id="search"
                                v-model="filters.search"
                                type="text"
                                class="db-field-control"
                            />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label
                                for="sort_by"
                                class="db-field-title after:hidden"
                                >{{ $t("label.sort_by") }}</label
                            >
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="sort_by"
                                v-model="filters.sort_by"
                                :options="[
                                    {
                                        id: 'referrals_count',
                                        name: $t('label.referral_count'),
                                    },
                                    { id: 'name', name: $t('label.name') },
                                    {
                                        id: 'created_at',
                                        name: $t('label.join_date'),
                                    },
                                ]"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label
                                for="sort_order"
                                class="db-field-title after:hidden"
                                >{{ $t("label.sort_order") }}</label
                            >
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="sort_order"
                                v-model="filters.sort_order"
                                :options="[
                                    { id: 'asc', name: $t('label') },
                                    {
                                        id: 'desc',
                                        name: $t('label.descending'),
                                    },
                                ]"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button
                                    class="db-btn py-2 text-white bg-primary"
                                >
                                    <i
                                        class="lab lab-search-line lab-font-size-16"
                                    ></i>
                                    <span>{{ $t("button") }}</span>
                                </button>
                                <button
                                    class="db-btn py-2 text-white bg-gray-600"
                                    @click="clearFilters"
                                >
                                    <i
                                        class="lab lab-cross-line-2 lab-font-size-22"
                                    ></i>
                                    <span>{{ $t("button.clear") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">
                                {{ $t("label.rank") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.user") }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t("label.referral_code") }}
                            </th>
                            <th class="db-table-head-th">TOTAL REFERRALS</th>
                            <th class="db-table-head-th">REFERRAL BALANCE</th>
                            <th
                                class="db-table-head-th hidden-print"
                                v-if="
                                    permissionChecker('customers_show') ||
                                    permissionChecker('customers_edit')
                                "
                            >
                                {{ $t("label.action") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="leaderboard.length > 0">
                        <tr
                            class="db-table-body-tr"
                            v-for="(user, index) in leaderboard"
                            :key="user.id"
                        >
                            <td class="db-table-body-td">
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-full"
                                    style="
                                        background-image: url('/images/LeaderBoard/posisionBD.png');
                                        background-size: cover;
                                        background-position: center;
                                    "
                                >
                                    <span
                                        class="text-[#F25B0A] font-bold text-sm"
                                        >{{ user.rank }}</span
                                    >
                                </div>
                            </td>
                            <td class="db-table-body-td">
                                <div class="flex items-center">
                                    <!-- <div class="flex-shrink-0 h-10 w-10">
                                        <img
                                            class="h-10 w-10 rounded-full"
                                            :src="user.avatar || '/images/default-avatar.png'"
                                            :alt="user.name"
                                        />
                                    </div> -->
                                    <div>
                                        <div
                                            class="text-sm font-meium text-gray-900"
                                        >
                                            {{ user.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="db-table-body-td">
                                {{ user.referral_code }}
                            </td>
                            <td class="db-table-body-td">
                                {{ user.total_referrals }}
                            </td>
                            <td class="db-table-body-td">
                                ₦{{ user.referral_balance }}
                            </td>
                            <td
                                class="db-table-body-td hidden-print"
                                v-if="
                                    permissionChecker('customers_show') ||
                                    permissionChecker('customers_edit')
                                "
                            >
                                <div
                                    class="flex justify-start items-center gap-1.5"
                                >
                                    <SmIconViewComponent
                                        @click="viewReferrals(user)"
                                        v-if="
                                            permissionChecker('customers_show')
                                        "
                                    />
                                    <SmIconSidebarModalEditComponent
                                        @click="editUser(user)"
                                        v-if="
                                            permissionChecker('customers_edit')
                                        "
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td
                                class="db-table-body-td text-center"
                                colspan="6"
                            >
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img
                                            class="w-full h-full"
                                            :src="
                                                ENV.API_URL +
                                                '/images/default/not-found.png'
                                            "
                                            alt="Not Found"
                                        />
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{
                                        $t("message.no_data_available")
                                    }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"
                v-if="leaderboard.length > 0"
            >
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div
                    class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                >
                    <PaginationTextComponent
                        :props="{ page: paginationPage }"
                    />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
    <SmReferralsModalComponent
        :visible="showReferralsModal"
        :referrals="selectedUserReferrals"
        :loading="referralsLoading"
        @close="closeReferralsModal"
    />
</template>

<script>
import { mapState, mapActions } from "vuex";
import LoadingContentComponent from "../components/LoadingContentComponent.vue";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmReferralsModalComponent from "../components/buttons/SmReferralsModalComponent.vue";
import axios from "axios";
import appService from "../../../services/appService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ENV from "../../../config/env";

export default {
    name: "LeaderboardComponent",
    components: {
        LoadingContentComponent,
        SmIconViewComponent,
        SmIconSidebarModalEditComponent,
        SmReferralsModalComponent,
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        FilterComponent,
    },
    data() {
        return {
            ENV: ENV,
            showReferralsModal: false,
            selectedUserReferrals: [],
            referralsLoading: false,
        };
    },
    computed: {
        ...mapState("leaderboard", [
            "leaderboard",
            "topThree",
            "pagination",
            "loading",
        ]),
        filters: {
            get() {
                return this.$store.state.leaderboard.filters;
            },
            set(value) {
                this.$store.commit("leaderboard/SET_FILTERS", value);
            },
        },
        paginationPage: function () {
            return this.$store.getters["leaderboard/page"];
        },
        visiblePages() {
            const current = this.pagination.current_page;
            const total = this.pagination.last_page;
            const delta = 2;
            const range = [];
            const rangeWithDots = [];

            for (
                let i = Math.max(2, current - delta);
                i <= Math.min(total - 1, current + delta);
                i++
            ) {
                range.push(i);
            }

            if (current - delta > 2) {
                rangeWithDots.push(1, "...");
            } else {
                rangeWithDots.push(1);
            }

            rangeWithDots.push(...range);

            if (current + delta < total - 1) {
                rangeWithDots.push("...", total);
            } else if (total > 1) {
                rangeWithDots.push(total);
            }

            return rangeWithDots
                .filter((item) => item !== "...")
                .map((item) => parseInt(item));
        },
    },
    created() {
        this.debouncedFetch = this.debounce(this.applyFilters, 500);
    },
    mounted() {
        this.fetchLeaderboard();
    },
    methods: {
        ...mapActions("leaderboard", [
            "fetchLeaderboard",
            "setFilters",
            "resetFilters",
        ]),

        applyFilters() {
            this.setFilters(this.filters);
        },

        clearFilters() {
            this.filters = {
                search: "",
                sort_by: "referrals_count",
                sort_order: "desc",
                page: 1,
            };
            this.resetFilters();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.filters.page = page;
                this.applyFilters();
            }
        },

        debounce(func, delay) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        },

        getRankDisplay(rank) {
            return rank;
        },
        async viewReferrals(user) {
            if (!user.id) {
                console.error("User ID is missing for viewing referrals");
                return;
            }
            this.referralsLoading = true;
            this.showReferralsModal = true;
            try {
                const response = await axios.get(
                    `admin/customers/${user.id}/referrals`
                );
                this.selectedUserReferrals = response.data.data || [];
            } catch (error) {
                console.error("Error fetching referrals:", error);
                this.selectedUserReferrals = [];
            } finally {
                this.referralsLoading = false;
            }
        },
        getRowClass(index) {
            switch (index) {
                case 0:
                    return "bg-yellow-50";
                case 1:
                    return "bg-gray-50";
                case 2:
                    return "bg-orange-50";
                default:
                    return "";
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },
        closeReferralsModal() {
            this.showReferralsModal = false;
            this.selectedUserReferrals = [];
            this.referralsLoading = false;
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        list: function (page = 1) {
            this.filters.page = page;
            this.applyFilters();
        },
        editUser(user) {
            if (user.id) {
                this.$router.push({
                    name: "admin.customers.show",
                    params: { id: user.id },
                });
            } else {
                console.error("User ID is missing");
            }
        },
    },
    watch: {
        "filters.search": {
            handler() {
                this.filters.page = 1;
                this.debouncedFetch();
            },
        },
        "filters.sort_by": {
            handler() {
                this.filters.page = 1;
                this.applyFilters();
            },
        },
        "filters.sort_order": {
            handler() {
                this.filters.page = 1;
                this.applyFilters();
            },
        },
    },
};
</script>

<style scoped></style>
