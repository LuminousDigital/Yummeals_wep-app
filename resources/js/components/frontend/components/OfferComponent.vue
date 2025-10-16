<template>
  <section class="mb-12" v-if="offers.length > 0">
    <div class="container">
      <div class="swiper" ref="swiperContainer">
        <div class="swiper-wrapper">
          <div class="swiper-slide" v-for="offer in offers" :key="offer">
            <router-link :to="{ name: 'frontend.offers.item', params: { slug: offer.slug } }">
              <img class="w-full rounded-2xl" :src="offer.image" alt="banner" />
            </router-link>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>
</template>
<script>
import statusEnum from "../../../enums/modules/statusEnum";
import Swiper from 'swiper/bundle';
import 'swiper/css';

export default {
  name: "OfferComponent",
  components: {},
  props: {
    limit: Number,
  },
  data() {
    return {
      loading: {
        isActive: false,
      },
      swiperInstance: null,
    };
  },
  mounted() {
    try {
      this.loading.isActive = true;
      this.$store.dispatch("frontendOffer/lists", {
        order_column: "id",
        order_type: "desc",
        limit: this.limit,
        status: statusEnum.ACTIVE,
      });
    } catch (err) {
      this.loading.isActive = false;
    }
  },
  computed: {
    offers: function () {
      return this.$store.getters["frontendOffer/lists"];
    },
  },
  watch: {
    offers: {
      handler() {
        if (this.offers.length > 0) {
          this.$nextTick(() => {
            this.initSwiper();
          });
        }
      },
      immediate: true,
    },
  },
  beforeUnmount() {
    if (this.swiperInstance) {
      this.swiperInstance.destroy();
    }
  },
  methods: {
    initSwiper() {
      if (this.swiperInstance) {
        this.swiperInstance.destroy();
      }
      if (this.$refs.swiperContainer) {
        this.swiperInstance = new Swiper(this.$refs.swiperContainer, {
          autoplay: {
            delay: 4000,
          },
          pagination: {
            el: '.swiper-pagination',
          },
          navigation: true,
          loop: true,
          slidesPerView: 1,
          spaceBetween: 16,
        });
      }
    },
  },
};
</script>
