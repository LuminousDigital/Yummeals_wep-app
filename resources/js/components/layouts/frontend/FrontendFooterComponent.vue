<template>
    <LoadingComponent :props="loading" />
    <!-- <footer class="pt-12 footer-part mb-14 lg:mb-0"> -->
    <footer class="footer-part pt-12 mb-14 lg:mb-0 bg-[#1a0c05]">
        <div class="container">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-3 md:gap-6">
                <div>
                    <router-link :to="{ name: 'frontend.home' }">
                        <img class="mb-8 w-36" :src="setting.theme_footer_logo" alt="logo">
                    </router-link>
                    <p class="mb-3 text-xs text-white">{{ $t('label.subscribe_short_text') }}</p>
                    <form @submit.prevent="saveSubscription"
                        class="flex items-center w-full h-12 p-2 mb-8 bg-white rounded-lg sm:max-w-xs">
                        <input type="email" :placeholder="$t('label.your_email_address')"
                            v-model="subscriptionProps.post.email" class="w-full h-full ltr:pl-2 rtl:pr-2">
                        <button type="submit"
                            class="capitalize text-xs font-medium rounded-md flex-shrink-0 p-2.5 text-white bg-primary">
                            {{ $t('button.subscribe') }}
                        </button>
                    </form>
                    <h3 v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube"
                        class="mb-4 text-xs text-white capitalize">{{ $t('label.follow_us_on') }}</h3>
                    <nav v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube"
                        class="flex items-center gap-5">
                        <a v-if="setting.social_media_facebook" target="_blank" :href="setting.social_media_facebook"
                            class="inline-block text-sm leading-7 text-center bg-white rounded-full shadow-lg fa-brands fa-facebook-f w-7 h-7 text-primary"></a>
                        <a v-if="setting.social_media_twitter" target="_blank" :href="setting.social_media_twitter"
                            class="inline-block text-sm leading-7 text-center bg-white rounded-full shadow-lg fa-brands fa-x-twitter w-7 h-7 text-primary"></a>
                        <a v-if="setting.social_media_instagram" target="_blank" :href="setting.social_media_instagram"
                            class="inline-block text-sm leading-7 text-center bg-white rounded-full shadow-lg fa-brands fa-instagram w-7 h-7 text-primary"></a>
                        <a v-if="setting.social_media_youtube" target="_blank" :href="setting.social_media_youtube"
                            class="inline-block text-sm leading-7 text-center bg-white rounded-full shadow-lg fa-brands fa-youtube w-7 h-7 text-primary"></a>
                    </nav>
                </div>
                <div>
                    <div class="sm:w-fit sm:mx-auto">
                        <h3 class="mb-6 text-lg font-semibold text-white capitalize">{{ $t('label.useful_links') }}</h3>
                        <nav v-if="pages.length > 0" class="flex flex-col items-start gap-3">
                            <router-link v-for="page in pages" class="text-white capitalize hover:underline"
                                :to="{ name: 'frontend.page', params: { slug: page.slug } }">
                                {{ page.title }}
                            </router-link>
                        </nav>
                    </div>
                </div>
                <div>
                    <h3 v-if="setting.site_android_app_link || setting.site_ios_app_link"
                        class="mb-3 text-lg font-semibold text-white capitalize">
                        {{ $t('label.download_our_apps') }}
                    </h3>
                    <nav class="flex items-center gap-3 mb-7 w-full max-w-[265px]">
                        <a target="_blank" v-if="setting.site_android_app_link" :href="setting.site_android_app_link">
                            <img class="w-full rounded-lg" :src="setting.image_play_store" alt="app">
                        </a>
                        <a target="_blank" v-if="setting.site_ios_app_link" :href="setting.site_ios_app_link">
                            <img class="w-full rounded-lg" :src="setting.image_app_store" alt="app">
                        </a>
                    </nav>
                    <ul class="flex flex-col gap-5">
                        <li class="flex items-center gap-2.5 text-white">
                            <i class="lab lab-sms-tracking lab-font-size-24"></i>
                            <span class="text-lg">{{ setting.company_email }}</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-white">
                            <i class="lab lab-call-center lab-font-size-24"></i>
                            <span class="text-lg font-medium">{{ setting.company_phone }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="py-8 mt-8 border-t border-[#e5e7eb]">
            <p class="text-sm text-center text-white">{{ setting.site_copyright }}</p>
        </div>
    </footer>
</template>


<script>
import statusEnum from "../../../enums/modules/statusEnum";
import menuSectionEnum from "../../../enums/modules/menuSectionEnum";
import axios from "axios";
import alertService from "../../../services/alertService";
import LoadingComponent from "../../frontend/components/LoadingComponent";

export default {
    name: "FrontendFooterComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            subscriptionProps: {
                post: {
                    email: ""
                }
            }
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        pages: function () {
            return this.$store.getters['frontendPage/lists'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendPage/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            menu_section_id: menuSectionEnum.FOOTER,
            status: statusEnum.ACTIVE
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        saveSubscription: function () {
            const url = '/frontend/subscriber';
            this.loading.isActive = true;
            axios.post(url, this.subscriptionProps.post).then(res => {
                this.loading.isActive = false;
                this.subscriptionProps.post.email = "";
                alertService.success(this.$t("message.subscribe"));
            }).catch((err) => {
                if (typeof err.response.data.errors === 'object') {
                    _.forEach(err.response.data.errors, (error) => {
                        alertService.error(error[0]);
                    });
                }
                this.loading.isActive = false;
            });
        }
    }
}
</script>
