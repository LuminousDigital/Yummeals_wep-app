<template>
  <LoadingComponent :props="loading" />
  <section class="pt-8 pb-16">
    <div class="container max-w-[550px] py-6 p-4 sm:px-6 shadow-xs rounded-2xl bg-white">
      <h2 class="capitalize mb-6 text-center text-[22px] font-semibold leading-[34px] text-heading">
        {{ $t('label.create_account') }}
      </h2>
      <form @submit.prevent="save">
        <div class="row">
          <div class="col-12 sm:col-6">
            <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.first_name') }}</label>
            <input v-model="form.first_name" type="text" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.first_name">{{ errors.first_name[0] }}</small>
          </div>
          <div class="col-12 sm:col-6">
            <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.last_name') }}</label>
            <input v-model="form.last_name" type="text" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.last_name">{{ errors.last_name[0] }}</small>
          </div>
          <div class="col-12 sm:col-6">
            <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.email') }}</label>
            <input v-model="form.email" type="email" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
          </div>
          <div class="col-12 sm:col-6">
            <label class="text-sm capitalize mb-1 text-heading">{{ $t('label.password') }}</label>
            <input v-model="form.password" type="password" class="w-full h-12 rounded-lg border px-4 border-[#D9DBE9]">
            <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
          </div>
          <div class="col-12">
            <button type="submit" class="w-full h-12 text-center capitalize font-medium rounded-3xl text-white bg-primary">
              {{ $t('button.sign_up') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";

export default {
  name: "SignupRegisterComponent",
  components: { LoadingComponent },
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
      errors: {}
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
    } else {
      const storedRef = this.$store.getters['frontendSignup/referralCode'];
      if (storedRef) this.form.referral_code = storedRef;
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
</script>
