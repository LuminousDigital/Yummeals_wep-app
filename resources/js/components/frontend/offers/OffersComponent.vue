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
                                    class="text-[13px] sm:text-[14px] md:text-[15px] text-[#000000] font-medium truncate"
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
                    <div>
                        <h2
                            class="mb-4 text-sm font-semibold text-gray-900 md:text-lg lg:text-xl md:mb-6"
                        >
                            Referral History ({{ referralHistory.length }})
                        </h2>
                        <div
                            class="bg-white rounded-lg shadow-sm h-auto sm:max-h-[350px] lg:max-h-[450px] overflow-y-auto hide-scrollbar relative"
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
                                    class="text-sm font-semibold text-orange-600 md:text-base"
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
                                class="absolute bottom-0 left-0 w-full h-6 pointer-events-none bg-gradient-to-t from-white to-transparent"
                            ></div>
                        </div>
                        <div class="mt-3 text-center">
                            <a
                                href="#"
                                class="text-sm font-medium text-gray-700 underline"
                            >
                                See all
                            </a>
                        </div>
                    </div>
                </div>
                <div className="space-y-4 sm:space-y-8 pt-2">
                    <h2
                        class="flex items-center justify-center gap-3 text-center mb-6"
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

                    <div className="flex justify-center items-end gap-[12px] sm:gap-[21px] mb-8 scale-100 sm:scale-100">
                        <div
                            className="flex flex-col items-center rounded-[8px] p-[12px] sm:p-[16px] w-[100px] sm:w-[154.67px] h-[140px] sm:h-[189px] bg-gradient-to-b from-[#64961A] to-[#FF823F] shadow-[0px_7px_18.8px_0px_#4F850040]"
                        >
                            <div
                                className="w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center mb-2 sm:mb-3"
                            >
                                <img
                                    src="/images/LeaderBoard/2nd.png"
                                    alt="2nd Trophy"
                                    className="object-contain h-8 sm:h-12"
                                />
                            </div>
                            <div
                                className="bg-orange-100 rounded-full flex items-center justify-center mb-2 sm:mb-3"
                            >
                                <img
                                    src="/images/LeaderBoard/2ndhead.png"
                                    alt="2nd Avatar"
                                    className="object-contain h-8 sm:h-12"
                                />
                            </div>
                            <p
                                className="text-[14px] sm:text-[18px] font-medium text-center text-white whitespace-nowrap"
                            >
                                Ada Blessing
                            </p>
                        </div>

                        <div
                            className="flex flex-col items-center rounded-[8px] p-[12px] sm:p-[16px] w-[110px] sm:w-[154.67px] h-[160px] sm:h-[215px]
                            bg-gradient-to-b from-[#64961A] to-[#FF823F] shadow-[0px_7px_18.8px_0px_#4F850040]"
                        >
                            <div
                                className="w-9 h-9 sm:w-14 sm:h-14 flex items-center justify-center mb-3 sm:mb-4"
                            >
                                <img
                                    src="/images/LeaderBoard/1st.png"
                                    alt="1st Trophy"
                                    className="object-contain h-9 sm:h-12"
                                />
                            </div>
                            <div
                                className="bg-orange-100 rounded-full flex items-center justify-center mb-3 sm:mb-4"
                            >
                                <img
                                    src="/images/LeaderBoard/1sthead.png"
                                    alt="1st Avatar"
                                    className="object-contain h-12 sm:h-16"
                                />
                            </div>
                            <p
                                className="text-[14px] sm:text-[18px] font-medium text-center text-white whitespace-nowrap"
                            >
                                Ada Blessing
                            </p>
                        </div>

                        <div
                            className="flex flex-col items-center rounded-[8px] p-[12px] sm:p-[16px] w-[100px] sm:w-[154.67px] h-[140px] sm:h-[189px] bg-gradient-to-b from-[#64961A] to-[#FF823F] shadow-[0px_7px_18.8px_0px_#4F850040]"
                        >
                            <div
                                className="w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center mb-2 sm:mb-3"
                            >
                                <img
                                    src="/images/LeaderBoard/3rd.png"
                                    alt="3rd Trophy"
                                    className="object-contain h-8 sm:h-12"
                                />
                            </div>
                            <div
                                className="bg-orange-100 rounded-full flex items-center justify-center mb-2 sm:mb-3"
                            >
                                <img
                                    src="/images/LeaderBoard/3rdhead.png"
                                    alt="3rd Avatar"
                                    className="object-contain h-8 sm:h-12"
                                />
                            </div>
                            <p
                                className="text-[14px] sm:text-[18px] font-medium text-center text-white whitespace-nowrap"
                            >
                                Ada Blessing
                            </p>
                        </div>
                    </div>

                    <div
                        className="flex justify-between text-xs text-gray-500 mb-1 px-2"
                    >
                        <span>Highest referrals</span>
                        <div className="flex gap-12">
                            <span>Referrals</span>
                            <span>Rewards</span>
                        </div>
                    </div>
                    <div className="space-y-2">
                        <div
                            key="{rank}"
                            className="flex items-center justify-between h-[64px] bg-[#FFEBE1] rounded-[8px] py-[8px] px-[12px] opacity-100"
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className="w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/4th.png"
                                        alt="4th Trophy"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <div
                                    className="bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/2ndhead.png"
                                        alt="User Avatar"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <p
                                    className="text-sm font-medium text-gray-900"
                                >
                                    Lotus Bliss
                                </p>
                            </div>

                            <div
                                className="flex gap-12 text-sm font-semibold text-gray-900"
                            >
                                <span>20</span>
                                <span>₦5000</span>
                            </div>
                        </div>

                        <div
                            key="{rank}"
                            className="flex items-center justify-between h-[64px] bg-[#FFEBE1] rounded-[8px] py-[8px] px-[12px] opacity-100"
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className="w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/5th.png"
                                        alt="4th Trophy"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <div
                                    className="bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/2ndhead.png"
                                        alt="User Avatar"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <p
                                    className="text-sm font-medium text-gray-900"
                                >
                                    Lotus Bliss
                                </p>
                            </div>

                            <div
                                className="flex gap-12 text-sm font-semibold text-gray-900"
                            >
                                <span>20</span>
                                <span>₦5000</span>
                            </div>
                        </div>

                        <div
                            key="{rank}"
                            className="flex items-center justify-between h-[64px] bg-[#FFEBE1] rounded-[8px] py-[8px] px-[12px] opacity-100"
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className="w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/6th.png"
                                        alt="4th Trophy"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <div
                                    className="bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/2ndhead.png"
                                        alt="User Avatar"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <p
                                    className="text-sm font-medium text-gray-900"
                                >
                                    Lotus Bliss
                                </p>
                            </div>

                            <div
                                className="flex gap-12 text-sm font-semibold text-gray-900"
                            >
                                <span>20</span>
                                <span>₦5000</span>
                            </div>
                        </div>

                        <div
                            key="{rank}"
                            className="flex items-center justify-between h-[64px] bg-[#FFEBE1] rounded-[8px] py-[8px] px-[12px] opacity-100"
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className="w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/7th.png"
                                        alt="4th Trophy"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <div
                                    className="bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/2ndhead.png"
                                        alt="User Avatar"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <p
                                    className="text-sm font-medium text-gray-900"
                                >
                                    Lotus Bliss
                                </p>
                            </div>

                            <div
                                className="flex gap-12 text-sm font-semibold text-gray-900"
                            >
                                <span>20</span>
                                <span>₦5000</span>
                            </div>
                        </div>
                        <p
                            className="text-center text-xs text-gray-500 mt-4 mb-2"
                        >
                            Your Level
                        </p>
                        <div
                            className="flex items-center justify-between bg-green-600 rounded-lg p-3 h-[64px]"
                        >
                            <div className="flex items-center gap-3">
                                <div
                                    className=" rounded-full w-8 h-8 flex items-center justify-center"
                                >
                                    <img
                                        src="/images/LeaderBoard/9th.png"
                                        alt="9th Trophy"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <div
                                    className="bg-white rounded-full w-8 h-8 flex items-center justify-center text-xl"
                                >
                                    <img
                                        src="/images/LeaderBoard/2ndhead.png"
                                        alt="User Avatar"
                                        className="object-contain h-12"
                                    />
                                </div>
                                <p className="text-sm font-medium text-white">
                                    Lotus Bliss
                                </p>
                            </div>
                            <div
                                className="flex gap-12 text-sm font-semibold text-white"
                            >
                                <span>20</span>
                                <span>₦5000</span>
                            </div>
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
    components: { OfferComponent },
    data() {
        return {
            limit: null,
            referralCode: "",
            referralLink: "",
            referralBalance: "₦0.00",
            copied: false,
            referralHistory: [],
            socialIcons: [],
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
                        date: new Date(item.created_at).toLocaleDateString(
                            "en-NG"
                        ),
                        reward: `₦${parseFloat(
                            item.referral_balance || 0
                        ).toFixed(2)}`,
                    })
                );

                const referralName = data.user?.name ?? "A friend";
                const message = encodeURIComponent(
                    `Hey! ${referralName} picked you for a special surprise… Win FREE meals worth ₦1.5M for a whole year with YUMMEALS! Download the app and Sign Up Now to get started: ${this.referralLink}`
                );

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
