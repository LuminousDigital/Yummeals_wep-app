<template>
  <Swiper
    dir="ltr"
    :speed="800"
    :spaceBetween="20"
    :breakpoints="{
      0: { slidesPerView: 2, spaceBetween: 16 },
      410: { slidesPerView: 3, spaceBetween: 16 },
      500: { slidesPerView: 4, spaceBetween: 20 },
      1024: { slidesPerView: 6, spaceBetween: 28 }
    }"
    class="menu-slides px-4"
    @swiper="onSwiper"
  >
    <SwiperSlide
      v-for="category in categories"
      :key="category"
      class="!w-[155.25px] !mr-[20px]"
    >
      <router-link
        v-if="design === categoryDesignEnum.FIRST"
        :to="{ name: 'frontend.menu', query: { s: category.slug } }"
        class="flex flex-col items-center text-center gap-4 p-4 c-h-30
               rounded-2xl border-b-2 border-transparent transition
               hover:bg-[#f8e5db] bg-[#F7F7FC] shadow-sm"
      >
        <img class="h-12 drop-shadow-category" :src="category.thumb" alt="category" />
        <h3 class="text-xs font-medium">{{ category.name }}</h3>
      </router-link>
      <router-link
        :class="checkIsQueryAndRouteQuerySame(category.slug) ? 'menu-category-active' : ''"
        v-else-if="design === categoryDesignEnum.SECOND"
        :to="{ name: 'frontend.menu', query: { s: category.slug } }"
        class="flex flex-col items-center text-center gap-4 p-4 c-h-25
               rounded-2xl border-b-2 border-transparent transition
               hover:bg-[#f8e5db] shadow-sm"
      >
        <img class="h-9 drop-shadow-category" :src="category.thumb" alt="category" />
        <h3 class="text-xs font-medium">{{ category.name }}</h3>
      </router-link>
    </SwiperSlide>
  </Swiper>
</template>

<script>
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";

export default {
  name: "CategoryComponent",
  props: {
    categories: Object,
    design: Number,
  },
  components: {
    Swiper,
    SwiperSlide,
  },
  data() {
    return {
      currentCategory: "",
      categoryDesignEnum,
      swiperInstance: null,
      resizeHandler: null,
    };
  },
  mounted() {
    if (this.$route.query.s !== "undefined") {
      this.currentCategory = this.$route.query.s;
    }
    this.resizeHandler = () => {
      if (this.swiperInstance && !this.swiperInstance.destroyed) {
        this.swiperInstance.update();
      }
    };
    window.addEventListener('resize', this.resizeHandler);
  },
  beforeUnmount() {
    if (this.resizeHandler) {
      window.removeEventListener('resize', this.resizeHandler);
    }
  },
  methods: {
    onSwiper(swiper) {
      this.swiperInstance = swiper;
      this.swiperInstance.update();
      setTimeout(() => {
        if (!swiper.destroyed) {
          swiper.slideTo(1, 800);
          setTimeout(() => {
            if (!swiper.destroyed) {
              swiper.slideTo(0, 800);
            }
          }, 1500);
        }
      }, 1200);
    },
    checkIsQueryAndRouteQuerySame(query) {
      return this.currentCategory === query;
    },
  },
  watch: {
    $route(to) {
      if (to.query.s !== "undefined") {
        this.currentCategory = to.query.s;
      }
    },
  },
};
</script>
