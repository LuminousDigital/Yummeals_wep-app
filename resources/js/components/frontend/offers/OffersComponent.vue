<template>
    <section class="mb-16 mt-12">
        <div class="container">
            <!-- <h2
                class="capitalize text-[26px] leading-[40px] font-semibold mb-6 text-primary"
            >
                {{ $t("label.all_referrals") }}
            </h2> -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 lg:gap-12 mb-12 pt-4"
            >
                <div class="space-y-8">
                    <div>
                        <h1
                            class="text-[18px] md:text-2xl lg:text-3xl font-bold text-[rgb(242,91,10)] mb-4"
                        >
                            Refer Your Friends and Win
                        </h1>
                        <p
                            class="text-[12px] md:text-[15px] lg:text-[17px] text-black font-medium leading-snug"
                        >
                            Refer your friends and get amazing rewards <br />
                            when they join us using your referral code.
                        </p>
                    </div>

                    <div>
                        <h2
                            class="text-[12px] md:text-[15px] lg:text-[17px] text-black font-medium pb-4"
                        >
                            Referral Link
                        </h2>
                        <div class="p-3 md:p-4">
                            <p
                                class="text-[14px] md:text-base text-[rgb(242,91,10)] font-medium break-all"
                            >
                                {{ referralLink }}
                            </p>
                        </div>

                        <!-- Social Icons -->
                        <div
                            class="flex flex-wrap justify-center items-center gap-5 md:gap-6 lg:gap-8 mb-12 mt-10 w-full transition-all"
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
                                    class="w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 lg:w-12 lg:h-12 object-contain"
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

                <!-- Right Column -->
                <div class="bg-white rounded-lg shadow-sm px-4 sm:px-6">
                    <h2
                        class="text-sm md:text-lg lg:text-xl font-semibold text-gray-900 mb-4 md:mb-6"
                    >
                        Referral History ({{ referralHistory.length }})
                    </h2>
                    <div
                        class="space-y-4 bg-white rounded-lg shadow-sm px-2 sm:px-4 md:px-6 h-auto sm:max-h-[350px] lg:max-h-[450px] overflow-y-auto hide-scrollbar relative"
                    >
                        <div
                            v-for="referral in referralHistory"
                            :key="referral.id"
                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
                        >
                            <div>
                                <h3
                                    class="text-sm md:text-base lg:text-lg font-medium text-orange-500"
                                >
                                    {{ referral.name }}
                                </h3>
                                <p class="text-xs md:text-sm text-gray-500">
                                    {{ referral.date }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    {{ referral.reward }}
                                </p>
                            </div>
                        </div>

                        <!-- Optional fade-out gradient -->
                        <div
                            class="absolute bottom-0 left-0 w-full h-6 bg-gradient-to-t from-white to-transparent pointer-events-none"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Offer Component -->
            <OfferComponent :limit="limit" />
        </div>
    </section>
</template>

<script>
import OfferComponent from "../components/OfferComponent";

export default {
    name: "OffersComponent",
    components: {
        OfferComponent,
    },
    data() {
        return {
            limit: null,
            referralLink: "www.sholaenterprises.Yummeals.com",
            copied: false,
            referralHistory: [
                {
                    id: 1,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 2,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "50% Discount",
                },
                {
                    id: 3,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 4,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 5,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 6,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 7,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
                {
                    id: 8,
                    name: "James Ade",
                    date: "20/05/25",
                    reward: "NGN 500",
                },
            ],
            socialIcons: [
                {
                    src: "/images/social-icon/whatsapp.png",
                    alt: "whatsapp",
                    link: "https://wa.me/234XXXXXXXXXX",
                },
                {
                    src: "/images/social-icon/instagram.png",
                    alt: "instagram",
                    link: "https://instagram.com",
                },
                {
                    src: "/images/social-icon/twitter.png",
                    alt: "X",
                    link: "https://x.com",
                },
                {
                    src: "/images/social-icon/facebook.png",
                    alt: "facebook",
                    link: "https://facebook.com",
                },
                {
                    src: "/images/social-icon/snapchat.png",
                    alt: "snapchat",
                    link: "https://snapchat.com",
                },
            ],
        };
    },
    methods: {
        copyLink() {
            navigator.clipboard.writeText(this.referralLink);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        },
    },
};
</script>
