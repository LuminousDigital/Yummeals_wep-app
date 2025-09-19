<template>
    <LoadingComponent :props="loading" />
    <section class="mt-6 ms:mt-10 mb-16">
        <div class="container">
            <div
                class="grid grid-cols-1 gap-6 pt-4 mb-12 md:grid-cols-2 md:gap-10 lg:gap-12"
            >
                <div class="space-y-4 sm:space-y-8">
                    <div>
                        <h1
                            class="text-[19px] md:text-2xl lg:text-3xl font-bold text-[rgb(242,91,10)] mb-4"
                        >
                            Refer Your Friends and Win
                        </h1>
                        <p
                            class="text-[14px] md:text-[15px] lg:text-[17px] text-gray-700 font-medium leading-snug"
                        >
                            Refer your friends and get amazing rewards <br />
                            when they join us using your referral code.
                        </p>
                    </div>
                    <p
                        class="text-[15px] md:text-sm lg:text-base text-gray-700 font-medium"
                    >
                        Referral Balance:
                        <span class="font-semibold text-green-600">{{
                            referralBalance
                        }}</span>
                    </p>
                    <div>
                        <div class="flex items-center gap-3 pb-5">
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
                        </div>

                        <h2
                            class="text-[14px] md:text-[15px] lg:text-[17px] text-black font-medium pb-2"
                        >
                            Referral Link
                        </h2>
                        <div class="p-3 md:p-4">
                            <p
                                class="text-[15px] md:text-base text-[rgb(242,91,10)] font-medium break-all"
                            >
                                {{ referralLink }}
                            </p>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-center w-full gap-5 mt-10 mb-12 transition-all md:gap-6 lg:gap-8"
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
                        <button
                            @click="copyLink"
                            class="w-full bg-[rgb(242,91,10)] hover:bg-orange-600 text-white font-medium py-2 md:py-3 px-4 md:px-6 rounded-3xl flex items-center justify-center space-x-2 transition-all"
                        >
                            <span>{{ copied ? "Copied!" : "Copy Link" }}</span>
                        </button>
                    </div>
                </div>
                <div class="px-4 bg-white rounded-lg shadow-sm sm:px-6">
                    <h2
                        class="mb-4 text-sm font-semibold text-gray-900 md:text-lg lg:text-xl md:mb-6"
                    >
                        Referral History ({{ referralHistory.length }})
                    </h2>
                    <div
                        class="bg-white rounded-lg shadow-sm px-2 sm:px-4 md:px-6 h-auto sm:max-h-[350px] lg:max-h-[450px] overflow-y-auto hide-scrollbar relative"
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
                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
                        >
                            <div>
                                <h3
                                    class="text-sm font-medium text-orange-500 md:text-base lg:text-lg"
                                >
                                    {{ referral.name }}
                                </h3>
                                <p class="text-xs text-gray-500 md:text-sm">
                                    {{ referral.date }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    {{ referral.reward }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-full h-6 pointer-events-none bg-gradient-to-t from-white to-transparent"
                        ></div>
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
