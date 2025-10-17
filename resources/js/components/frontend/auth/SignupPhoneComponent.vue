<template>
    <LoadingComponent :props="loading" />
    <section class="pt-28 pb-16">
        <div class="container max-w-[600px] py-6 p-4 mb-6 sm:mt-12 sm:pb-12  sm:pt-8 sm:px-12 md:shadow-md rounded-2xl bg-white md:border">
            <h2 class="capitalize sm:my-8 mb-6 mt-6 sm:mt-0 text-center text-[22px] md:text-2xl font-normal leading-[34px] text-heading-light tracking-[1px]">{{
                $t('label.lets_get_started')
                }}
            </h2>
            <form @submit.prevent="save">
                <div class="mb-6">
                    <label for="phone" class="text-sm capitalize mb-1 text-heading-light tracking-[1px]">
                        {{ $t('label.mobile_number') }}
                    </label>
                    <div :class="errors.phone ? 'invalid' : ''"
                        class="w-full h-12 rounded-lg border pl-4 flex items-center border-[#D9DBE9] bg-[#eff0f6] focus:bg-white">
                        <div class="w-fit flex-shrink-0 dropdown-group pr-4">
                            <button type="button" class="flex items-center gap-1">
                                {{ flag }}
                                <span class="whitespace-nowrap flex-shrink-0 text-sm">{{ props.form.code }}</span>
                                <input type="hidden" v-model="props.form.code">
                            </button>
                        </div>
                        <input id="phone" v-model="props.form.phone" v-on:keyup.enter="save"
                            v-on:keypress="phoneNumber($event)" type="text"
                            class="pl-4 text-sm w-full h-full text-heading focus:border focus:border-[#D9DBE9] focus:rounded-tr-lg  focus:rounded-br-lg">
                    </div>
                    <small class="db-field-alert" v-if="errors.phone">
                        {{ errors.phone[0] }}
                    </small>
                </div>
                <button type="submit"
                    class="w-full h-12 text-center capitalize font-medium rounded-lg mb-6 mt-4 sm:mt-6 text-white bg-primary">
                    {{ $t('label.next') }}
                </button>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-sm">{{ $t('label.already_have_an_account') }}</span>
                    <router-link :to="{ name: 'auth.login' }" class="text-sm text-primary">
                        {{ $t('label.login') }}
                    </router-link>
                </div>
            </form>
        </div>
    </section>
</template>

<script>

import appService from "../../../services/appService";
import askEnum from "../../../enums/modules/askEnum"
import alertService from "../../../services/alertService";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "SignupPhoneComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                form: {
                    phone: "",
                    code: "",
                },
            },
            flag: "",
            country_code: "",
            errors: {},
            phone_verification: "",
        };
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.defaultCountryCode = res.data.data.company_country_code;
            this.$store.dispatch('frontendCountryCode/show', this.defaultCountryCode).then(res => {
                this.props.form.code = res.data.data.calling_code;
                this.country_code = res.data.data.calling_code;
                this.flag = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
        const referral = this.$route.query.ref;
        if (referral) {
            this.$store.dispatch('frontendSignup/setReferralCode', referral);
        }
    },
    computed: {
        countryCode: function () {
            return this.$store.getters['frontendCountryCode/show'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendSignup/otp", this.props.form).then((res) => {
                    this.loading.isActive = false;

                    if (this.setting.site_phone_verification === askEnum.NO) {
                        this.$router.push({ name: "auth.signupRegister" });
                    } else {
                        alertService.success(res.data.message);
                        this.$router.push({ name: "auth.signupVerify" });
                    }
                    const referralCode = this.$store.getters['frontendSignup/referralCode'];
                    if (referralCode) {
                        this.$router.push({ name: "auth.signupRegister", query: { ref: referralCode } });
                    } else {
                        this.$router.push({ name: "auth.signupRegister" });
                    }

                    this.props.form = {
                        phone: "",
                        code: this.country_code,
                    };
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
}
</script>
