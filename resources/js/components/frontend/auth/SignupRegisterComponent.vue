<template>
  <LoadingComponent :props="loading" />
  <section class="pt-28 pb-16">
        <div class="container max-w-[600px] py-6 p-4 mb-6 sm:mt-12 sm:pb-12  sm:pt-8 sm:px-12 md:shadow-md rounded-2xl bg-white md:border">
      <h2 class="capitalize sm:my-8 mb-6 mt-6 sm:mt-0 text-center text-[22px] md:text-2xl font-normal leading-[34px] text-heading-light tracking-[1px]">
        {{ $t('label.create_account') }}
      </h2>
      <form @submit.prevent="save">
        <div class="row">
         <div class="col-12">
            <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.email') }}</label>
            <input v-model="form.email" type="email" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
          </div>
          <div class="col-12 sm:col-6">
            <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.first_name') }}</label>
            <input v-model="form.first_name" type="text" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.first_name">{{ errors.first_name[0] }}</small>
          </div>
          <div class="col-12 sm:col-6">
            <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.last_name') }}</label>
            <input v-model="form.last_name" type="text" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
          </div>
          <div class="col-12">
            <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.password') }}</label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="w-full h-12 rounded-lg border px-4 pr-12 border-[#D9DBE9]">
              <button type="button" @click="showPassword = !showPassword" class="absolute right-6 top-3 translate-y-[2px] text-gray-300">
                <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
            <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
          </div>
          <div class="col-12">
            <button type="submit" class="w-full h-12 text-center text-white capitalize rounded-lg bg-primary">
              {{ $t('button.sign_up') }}
            </button>
          </div>
        </div>
      </form>
      <SocialAuthButtons class="mt-12"/>
    </div>
  </section>
</template>

<script>
//     <LoadingComponent :props="loading" />
//     <section class="pt-28 pb-16">
//         <div class="container max-w-[550px] py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
//             <h2 class="capitalize sm:my-8 mb-6 mt-6 sm:mt-0 text-center text-[22px] md:text-2xl font-normal leading-[34px] text-heading-light tracking-[1px]">
//                 {{ $t('label.create_account') }}
//             </h2>
//             <form @submit.prevent="save">
//                 <div class="row">
//                     <div class="col-12 sm:col-6">
//                         <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.first_name') }}</label>
//                         <input type="text" v-model="props.form.first_name"
//                             class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
//                         <small class="db-field-alert" v-if="errors.first_name">
//                             {{ errors.first_name[0] }}
//                         </small>
//                     </div>
//                     <div class="col-12 sm:col-6">
//                         <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.last_name') }}</label>
//                         <input type="text" v-model="props.form.last_name"
//                             class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
//                         <small class="db-field-alert" v-if="errors.last_name">
//                             {{ errors.last_name[0] }}
//                         </small>
//                     </div>
//                     <div class="col-12 sm:col-6">
//                         <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.email') }}</label>
//                         <input type="email" v-model="props.form.email"
//                             class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
//                         <small class="db-field-alert" v-if="errors.email">
//                             {{ errors.email[0] }}
//                         </small>
//                     </div>
//                     <div class="col-12 sm:col-6">
//                         <label class="mb-1 text-sm capitalize text-heading-light">{{ $t('label.password') }}</label>
//                         <input type="password" v-model="props.form.password"
//                             class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
//                         <small class="db-field-alert" v-if="errors.password">
//                             {{ errors.password[0] }}
//                         </small>
//                     </div>
//                     <div class="col-12">
//                         <button type="submit"
//                             class="w-full h-12 font-medium text-center text-white capitalize rounded-3xl bg-primary">
//                             {{ $t('button.sign_up') }}
//                         </button>
//                     </div>
//                 </div>
//             </form>
//         </div>
//     </section>
// </template>

// <script>

import LoadingComponent from "../components/LoadingComponent";
import SocialAuthButtons from "../components/button/SocialAuthButtons.vue";
import alertService from "../../../services/alertService";

export default {
  name: "SignupRegisterComponent",
  components: { LoadingComponent, SocialAuthButtons },
  data() {
    return {
      loading: { isActive: false },
      form: {
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        phone: "",
        country_code: "",
        referral_code: ""
      },
      errors: {},
      showPassword: false
    };
  },
  computed: {
    carts() {
      return this.$store.getters['frontendCart/lists'];
    }
  },
  mounted() {
    this.phoneChecking();

    const referral = this.$route.query.ref;
    if (referral) {
      this.form.referral_code = referral;
      this.$store.dispatch('frontendSignup/setReferralCode', referral);
    }
  },
  methods: {
    phoneChecking() {
      const otp = this.$store.getters['frontendSignup/phone'];
      if (otp?.otp?.phone) {
        this.form.phone = otp.otp.phone;
        this.form.country_code = otp.otp.code;
      } else {
        this.$router.push({ name: 'auth.signupPhone' });
      }
    },
    async save() {
      try {
        this.loading.isActive = true;

        const response = await this.$store.dispatch("frontendSignup/signup", this.form);
        this.errors = {};

        const loginRes = await this.$store.dispatch("login", {
          email: this.form.email,
          password: this.form.password,
        });

        this.loading.isActive = false;
        alertService.success(loginRes.data.message);
        this.resetForm();

        if (this.carts.length > 0) {
          this.$router.push({ name: "frontend.checkout" });
        } else {
          this.$router.push({ name: "frontend.home" });
        }

      } catch (err) {
        this.loading.isActive = false;
        if (err.response?.data?.errors) {
          this.errors = err.response.data.errors;
        } else {
          alertService.error("Signup failed. Try again.");
        }
      }
    },
    resetForm() {
      this.form = {
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        phone: "",
        country_code: "",
        referral_code: ""
      };
    }
  }
};
//     name: "SignupRegisterComponent",
//     components: { LoadingComponent },
//     data() {
//         return {
//             loading: {
//                 isActive: false,
//             },
//             props: {
//                 form: {
//                     first_name: "",
//                     last_name: "",
//                     email: "",
//                     password: "",
//                     phone: "",
//                     country_code: "",
//                 },
//             },
//             errors: {},
//         };
//     },
//     computed: {
//         carts: function () {
//             return this.$store.getters['frontendCart/lists'];
//         }
//     },
//     mounted() {
//         this.phoneChecking();
//     },
//     methods: {
//         phoneChecking: function () {
//             const otp = this.$store.getters['frontendSignup/phone'];
//             if (Object.keys(otp).length > 0) {
//                 this.props.form.phone = otp.otp.phone;
//                 this.props.form.country_code = otp.otp.code;
//             } else {
//                 this.$router.push({ name: 'auth.signupPhone' });
//             }
//         },
//         save: function () {
//             try {
//                 this.loading.isActive = true;
//                 this.$store.dispatch("frontendSignup/signup", this.props.form).then((res) => {
//                     this.errors = {};
//                     this.$store.dispatch('login', {
//                         email: this.props.form.email,
//                         password: this.props.form.password
//                     }).then(LoginRes => {
//                         this.loading.isActive = false;
//                         alertService.success(LoginRes.data.message);
//                         this.props.form = {
//                             first_name: "",
//                             last_name: "",
//                             email: "",
//                             password: "",
//                             phone: "",
//                         };
//                         if (this.carts.length > 0) {
//                             this.$router.push({ name: "frontend.checkout" });
//                         } else {
//                             this.$router.push({ name: "frontend.home" });
//                         }
//                     }).catch((err) => {
//                         this.loading.isActive = false;
//                         alertService.success(res.data.message);
//                         this.$router.push({ name: "auth.login" });
//                     });
//                 }).catch((err) => {
//                     this.loading.isActive = false;
//                     this.errors = err.response.data.errors;
//                 });
//             } catch (err) {
//                 this.loading.isActive = false;
//                 alertService.error(err);
//             }
//         },
//     }
// }
</script>
