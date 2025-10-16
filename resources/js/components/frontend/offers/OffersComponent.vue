<template>
    <LoadingComponent :props="loading" />
    <section class="mt-6 ms:mt-10 mb-16">
        <div class="container">
            <div
                class="grid grid-cols-1 gap-8 pt-4 mb-12 md:grid-cols-2 md:gap-20 lg:gap-24"
            >
                <div class="space-y-4 sm:space-y-8">
                    <div class="pb-4">
                        <h1
                            class="text-[20px] md:text-2xl lg:text-3xl font-bold text-[rgb(242,91,10)] mb-6"
                        >
                            Refer Your Friends and Win
                        </h1>
                        <p
                            class="text-[13px] md:text-[14px] lg:text-[16px] text-black leading-snug"
                        >
                            Invite your friends to order with the Yummeals app
                            <br />
                            and get rewarded for every successful referral.
                        </p>
                    </div>
                    <!-- <p
                        class="text-[15px] md:text-sm lg:text-base text-gray-700 font-medium"
                    >
                        Referral Balance:
                        <span class="font-semibold text-green-600">{{
                            referralBalance
                        }}</span>
                    </p> -->
                    <div>
                        <!-- <div class="flex items-center gap-3 pb-5">
                            <h2
                                class="text-[14px] md:text-[15px] lg:text-[17px] text-black font-medium"
                            >
                                Referral Code:
                            </h2>
                            <p
                                class="text-[15px] md:text-base text-[rgb(242,91,10)] font-medium break-all"
                            >
                                {{ referralCode }}
                            </p>
                        </div> -->
                        <h2 class="text-[16px] text-[#212121] font-medium pb-4">
                            Referral Link
                        </h2>
                        <div
                            class="flex flex-col md:flex-row rounded-xl overflow-hidden shadow-sm"
                        >
                            <div class="p-3 md:p-4 bg-[#F6F6F6] flex-1 min-w-0">
                                <p
                                    class="text-[13px] sm:text] md:text-[15px] text-[#000000] font-medium truncate"
                                >
                                    {{ referralLink }}
                                </p>
                            </div>
                            <button
                                @click="copyLink"
                                class="w-auto md:w-[130px] bg-[#F25B0A] hover:bg-orange-600 text-white font-medium py-2 md:py-3 px-4 md:px-6 flex items-center justify-center transition-all"
                            >
                                <span>{{
                                    copied ? "Copied!" : "Copy Link"
                                }}</span>
                            </button>
                        </div>

                        <div
                            class="flex flex-col items-center w-full mt-10 mb-12"
                        >
                            <div class="flex items-center w-full max-w-xs mb-6">
                                <div
                                    class="flex-grow border-t border-gray-300"
                                ></div>
                                <span class="mx-3 text-sm text-gray-500"
                                    >Share via</span
                                >
                                <div
                                    class="flex-grow border-t border-gray-300"
                                ></div>
                            </div>
                            <div
                                class="flex flex-wrap items-center justify-center w-full gap-5 transition-all md:gap-6 lg:gap-5"
                            >
                                <a
                                    v-for="icon in socialIcons"
                                    :key="icon.alt"
                                    :href="icon.link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center justify-center cursor-pointer"
                                >
                                    <img
                                        :src="icon.src"
                                        :alt="icon.alt"
                                        class="object-contain w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 lg:w-12 lg:h-12"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <h2
                            class="mb-4 text-sm font-semibold text-gray-900 md:text-lg lg:text-xl md:mb-6"
                        >
                            Referral History ({{ referralHistory.length }})
                        </h2>
                        <div
                            :class="
                                showAllReferrals
                                    ? 'bg-white rounded-lg shadow-sm h-auto'
                                    : 'bg-white rounded-lg shadow-sm h-auto sm:max-h-[350px] lg:max-h-[450px] overflow-y-auto hide-scrollbar relative'
                            "
                        >
                            <div
                                v-if="referralHistory.length === 0"
                                class="flex flex-col items-center justify-center w-full py-10 text-center"
                            >
                                <img
                                    src="/images/social-icon/empty-referrals.png"
                                    alt="No referrals"
                                    class="object-contain w-40 h-40 mb-4"
                                />
                                <p
                                    class="text-sm font-semibold text-gray-600 md:text-base"
                                >
                                    You have not referred anybody
                                </p>
                            </div>
                            <div
                                v-else
                                v-for="referral in referralHistory"
                                :key="referral.id"
                                class="flex items-center justify-between bg-gray-50 rounded-lg p-4 mb-3"
                            >
                                <h3
                                    class="text-sm font-medium text-orange-500 md:text-base"
                                >
                                    {{ referral.name }}
                                </h3>
                                <p class="font-semibold text-gray-900">
                                    {{ referral.reward }}
                                </p>
                                <p class="text-xs text-gray-500 md:text-sm">
                                    {{ referral.date }}
                                </p>
                            </div>
                            <div
                                v-if="!showAllReferrals"
                                class="absolute bottom-0 left-0 w-full h-6 pointer-events-none bg-gradient-to-t from-white to-transparent"
                            ></div>
                        </div>
                        <div class="mt-3 text-center">
                            <a
                                v-if="totalReferrals > 5 && !showAllReferrals"
                                @click="fetchAllReferrals"
                                class="text-sm font-medium text-gray-700 underline cursor-pointer"
                            >
                                See all
                            </a>
                        </div>
                    </div>
                </div>
                <div class="space-y-4 sm:space-y-8 pt-2">
                    <h2
                        class="flex items-center justify-center gap-3 text-center mb-8"
                    >
                        <span>
                            <img
                                src="/images/LeaderBoard/trophy.png"
                                alt="Trophy"
                                class="object-contain h-7"
                            />
                        </span>
                        <span
                            class="text-[20px] md:text-xl lg:text-xl font-bold text-black"
                        >
                            Leader Board
                        </span>
                        <span>
                            <img
                                src="/images/LeaderBoard/trophy.png"
                                alt="Trophy"
                                class="object-contain h-7"
                            />
                        </span>
                    </h2>

                    <div
                        class="flex justify-center items-end gap-3 sm:gap-5 mb-8"
                    >
                        <div
                            v-for="user in topThree"
                            :key="user.rank"
                            class="flex flex-col items-center rounded-lg p-3 sm:p-4 flex-1 max-w-[120px] sm:max-w-[155px] bg-gradient-to-b from-[#64961A] to-[#FF823F] shadow-lg"
                            :class="
                                user.rank === 1
                                    ? 'h-40 sm:h-52'
                                    : 'h-32 sm:h-44'
                            "
                        >
                            <div
                                class="flex items-center justify-center mb-2 sm:mb-3"
                                :class="
                                    user.rank === 1
                                        ? 'w-10 h-10 sm:w-14 sm:h-14'
                                        : 'w-8 h-8 sm:w-12 sm:h-12'
                                "
                            >
                                <img
                                    :src="user.trophy"
                                    :alt="`${user.rank} Trophy`"
                                    class="object-contain"
                                    :class="
                                        user.rank === 1
                                            ? 'h-10 sm:h-12'
                                            : 'h-8 sm:h-10'
                                    "
                                />
                            </div>
                            <div
                                class="bg-orange-100 rounded-full flex items-center justify-center mb-2 sm:mb-3"
                                :class="
                                    user.rank === 1
                                        ? 'w-12 h-12 sm:w-16 sm:h-16'
                                        : 'w-8 h-8 sm:w-12 sm:h-12'
                                "
                            >
                                <span
                                    class="text-orange-600 font-bold"
                                    :class="
                                        user.rank === 1
                                            ? 'text-lg sm:text-xl'
                                            : 'text-sm sm:text-base'
                                    "
                                >
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <p
                                class="text-sm sm:text-base font-medium text-center text-white leading-tight"
                            >
                                {{ user.name }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex justify-between text-xs text-gray-500 mb-2 px-3"
                    >
                        <span>Highest referrals</span>
                        <div class="flex gap-8 sm:gap-12">
                            <span>Referrals</span>
                            <span>Rewards</span>
                        </div>
                    </div>
                    <template v-if="relativeUsers && relativeUsers.length">
                        <div class="space-y-2">
                            <div
                                v-for="user in relativeUsers"
                                :key="user.rank"
                                :class="[
                                    'flex items-center justify-between rounded-lg py-2 px-3 h-16 transition-all duration-300',
                                    user.is_current_user
                                        ? 'bg-[#64961A] text-white'
                                        : 'bg-[#FFEBE1] text-gray-900',
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 flex items-center justify-center flex-shrink-0 rounded-full"
                                        style="background-image: url('/images/LeaderBoard/posisionBD.png'); background-size: cover; background-position: center;"
                                    >
                                        <span class="text-[#F25B0A] font-bold text-md">{{ user.rank }}</span>
                                    </div>
                                    <div
                                        :class="[
                                            'rounded-full flex items-center justify-center flex-shrink-0 w-8 h-8 transition-all duration-300',
                                            user.is_current_user
                                                ? 'bg-orange-100'
                                                : 'bg-gray-800',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'font-bold text-sm sm:text-base',
                                                user.is_current_user
                                                    ? 'text-orange-600'
                                                    : 'text-white',
                                            ]"
                                        >
                                            {{
                                                user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium truncate">
                                        {{ user.name }}
                                    </p>
                                </div>

                                <div
                                    class="flex gap-8 sm:gap-12 text-sm font-semibold flex-shrink-0"
                                >
                                    <span class="w-8 text-center">{{
                                        user.total_referrals
                                    }}</span>
                                    <span class="w-16 text-right"
                                        >₦{{ user.referral_balance }}</span
                                    >
                                </div>
                            </div>

                            <template
                                v-if="currentUser && currentUser.rank > 9"
                            >
                                <p
                                    class="text-center text-xs text-gray-500 mt-4 mb-2"
                                >
                                    Your Level
                                </p>
                                <div
                                    :class="[
                                        'flex items-center justify-between rounded-lg py-2 px-3 h-16 transition-all duration-300',
                                        currentUser.rank <= 3
                                            ? 'bg-gradient-to-b from-[#64961A] to-[#FF823F] text-white'
                                            : 'bg-[#FFEBE1] text-gray-900',
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="rounded-full w-8 h-8 flex items-center justify-center"
                                        >
                                        <span class="text-[#F25B0A] font-bold text-sm">{{ currentUser.rank }}</span>
                                        </div>
                                        <div
                                            class="bg-orange-100 rounded-full w-8 h-8 flex items-center justify-center"
                                        >
                                            <span
                                                class="text-gray-800 font-bold text-sm"
                                            >
                                                {{
                                                    currentUser.name
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>
                                        <p
                                            :class="[
                                                'text-sm font-medium truncate',
                                                currentUser.rank <= 3
                                                    ? 'text-white'
                                                    : 'text-gray-900',
                                            ]"
                                        >
                                            {{ currentUser.name }}
                                        </p>
                                    </div>

                                    <div
                                        class="flex gap-8 sm:gap-12 text-sm font-semibold"
                                        :class="
                                            currentUser.rank <= 3
                                                ? 'text-white'
                                                : 'text-gray-900'
                                        "
                                    >
                                        <span class="w-8 text-center">{{
                                            currentUser.total_referrals
                                        }}</span>
                                        <span class="w-16 text-right"
                                            >₦{{
                                                currentUser.referral_balance
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template v-else>
                        <div
                            class="flex flex-col items-center justify-center text-center py-10"
                        >
                            <!-- <img
                                src="/images/social-icon/empty-referrals.png"
                                alt="Empty Leaderboard"
                                class="object-contain w-40 h-40 mb-4"
                            /> -->
                            <p
                                class="text-sm font-semibold text-gray-600 md:text-base"
                            >
                                You have to have at least one referral to appear on the leaderboard
                            </p>
                        </div>
                    </template>

                    <div class="block md:hidden">
                        <h2
                            class="mb-4 text-sm font-semibold text-gray-900 md:text-lg lg:text-xl md:mb-6"
                        >
                            Referral History ({{ referralHistory.length }})
                        </h2>
                        <div
                            :class="
                                showAllReferrals
                                    ? 'bg-white rounded-lg shadow-sm h-auto'
                                    : 'bg-white rounded-lg shadow-sm h-auto sm:max-h-[350px] lg:max-h-[450px] overflow-y-auto hide-scrollbar relative'
                            "
                        >
                            <div
                                v-if="referralHistory.length === 0"
                                class="flex flex-col items-center justify-center w-full py-10 text-center"
                            >
                                <img
                                    src="/images/social-icon/empty-referrals.png"
                                    alt="No referrals"
                                    class="object-contain w-40 h-40 mb-4"
                                />
                                <p
                                    class="text-sm font-semibold text-gray-600 md:text-base"
                                >
                                    You have not referred anybody
                                </p>
                            </div>
                            <div
                                v-else
                                v-for="referral in referralHistory"
                                :key="referral.id"
                                class="flex items-center justify-between bg-gray-50 rounded-lg p-4 mb-3"
                            >
                                <h3
                                    class="text-sm font-medium text-orange-500 md:text-base"
                                >
                                    {{ referral.name }}
                                </h3>
                                <p class="font-semibold text-gray-90`0">
                                    {{ referral.reward }}
                                </p>
                                <p class="text-xs text-gray-500 md:text-sm">
                                    {{ referral.date }}
                                </p>
                            </div>
                            <div
                                v-if="!showAllReferrals"
                                class="absolute bottom-0 left-0 w-full h-6 pointer-events-none bg-gradient-to-t from-white to-transparent"
                            ></div>
                        </div>
                        <div class="mt-3 text-center">
                            <a
                                v-if="totalReferrals > 5 && !showAllReferrals"
                                @click="fetchAllReferrals"
                                class="text-sm font-medium text-gray-700 underline cursor-pointer"
                            >
                                See all
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <OfferComponent :limit="limit" />
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import OfferComponent from "../components/OfferComponent";
import axios from "axios";

export default {
    name: "OffersComponent",
    components: { LoadingComponent, OfferComponent },
    data() {
        return {
            loading: false,
            limit: null,
            referralCode: "",
            referralLink: "",
            referralBalance: "₦0.00",
            copied: false,
            referralHistory: [],
            totalReferrals: 0,
            showAllReferrals: false,
            socialIcons: [],
            topThree: [],
            relativeUsers: [],
            currentUser: null,
        };
    },
    mounted() {
        this.fetchReferralData();
    },
    methods: {
        copyLink() {
            navigator.clipboard.writeText(this.referralLink);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        },
        async fetchAllReferrals() {
            try {
                const response = await axios.get("/referral?per_page=100");
                this.referralHistory = response.data.referrals.data.map(
                    (item, index) => ({
                        id: index + 1,
                        name: item.name ?? item.email ?? "Unknown",
                        email: item.email ?? "",
                        date: new Date(item.created_at).toLocaleDateString(
                            "en-NG"
                        ),
                        reward: `₦${parseFloat(
                            item.referral_balance || 0
                        ).toFixed(2)}`,
                    })
                );
                this.showAllReferrals = true;
            } catch (error) {
                console.error("Fetch all referrals error:", error);
            }
        },
        async fetchReferralData() {
            try {
                const response = await axios.get("/referral");
                const data = response.data;
                this.referralCode = data.referral_code || "";
                this.referralLink = `${window.location.origin}/signup?ref=${data.referral_code}`;
                this.referralBalance = `₦${parseFloat(
                    data.referral_balance || 0
                ).toFixed(2)}`;

                this.referralHistory = data.referrals.data.map(
                    (item, index) => ({
                        id: index + 1,
                        name: item.name ?? item.email ?? "Unknown",
                        email: item.email ?? "",
                        date: new Date(item.created_at).toLocaleDateString(
                            "en-NG"
                        ),
                        reward: `₦${parseFloat(
                            item.referral_balance || 0
                        ).toFixed(2)}`,
                    })
                );
                this.totalReferrals = data.referrals.total;

                const referralName = data.user?.name ?? "A friend";
                const message = encodeURIComponent(
                    `Hey! ${referralName} picked you for a special surprise… Win FREE meals worth ₦1.5M for a whole year with YUMMEALS! Download the app and Sign Up Now to get started: ${this.referralLink}`
                );

                const topThree = data.leaderboard?.top_three ?? [];
                const reorderedTopThree = [
                    topThree[1],
                    topThree[0],
                    topThree[2],
                ]
                    .filter(Boolean)
                    .map((user, i, arr) => {
                        const order =
                            arr.length === 3
                                ? [2, 1, 3]
                                : arr.length === 2
                                ? [1, 2]
                                : [1];
                        const rank = order[i];
                        return {
                            id: user?.id ?? i + 1,
                            rank,
                            name: user?.name ?? "Unknown",
                            trophy: `/images/LeaderBoard/${
                                rank === 1 ? "1st" : rank === 2 ? "2nd" : "3rd"
                            }.png`,
                            referrals: user?.total_referrals ?? 0,
                            reward: `₦${user?.referral_balance ?? 0}`,
                        };
                    });

                this.topThree = reorderedTopThree;

                const relativeUsersArray = Object.values(
                    data.leaderboard.relative_leaderboard.users
                );
                this.currentUser = relativeUsersArray.find(
                    (user) => user.is_current_user
                );
                this.relativeUsers = relativeUsersArray;

                this.socialIcons = [
                    {
                        src: "/images/social-icon/whatsapp.png",
                        alt: "whatsapp",
                        link: `https://wa.me/?text=${message}`,
                    },
                    {
                        src: "/images/social-icon/instagram.png",
                        alt: "instagram",
                        link: `https://www.instagram.com/?url=${this.referralLink}`,
                    },
                    {
                        src: "/images/social-icon/twitter.png",
                        alt: "twitter",
                        link: `https://twitter.com/intent/tweet?text=${message}`,
                    },
                    {
                        src: "/images/social-icon/facebook.png",
                        alt: "facebook",
                        link: `https://www.facebook.com/sharer/sharer.php?u=${this.referralLink}&quote=${message}`,
                    },
                    {
                        src: "/images/social-icon/snapchat.png",
                        alt: "snapchat",
                        link: `${this.referralLink}`,
                    },
                ];
            } catch (error) {
                console.error("Referral fetch error:", error);
                this.$toast?.error("Unable to load referral data.");
            }
        },
    },
};
</script>
