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
    class="flex flex-col items-center text-center c-h-30
           rounded-2xl border shadow-md transition-all duration-300
           hover:bg-[#f8e5db] bg-[#F7F7FC] hover:shadow-lg relative overflow-hidden group"
  >
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent rounded-2xl z-10"></div>
    <img class="h-full w-full drop-shadow-category rounded-2xl object-cover group-hover:scale-105 transition-transform duration-300" :src="category.thumb" alt="category" />
    <h3 class="text-sm font-normal text-white relative z-20 px-2 -mt-8 drop-shadow-lg">{{ category.name }}</h3>
  </router-link>

  <router-link
    v-else-if="design === categoryDesignEnum.SECOND"
    :to="{ name: 'frontend.menu', query: { s: category.slug } }"
    :class="[
      checkIsQueryAndRouteQuerySame(category.slug) 
        ? 'menu-category-active bg-primary shadow-lg scale-105' 
        : 'border-transparent',
      'flex flex-col items-center text-center gap-4 c-h-30 rounded-2xl border-b-[3px] transition-all duration-300 bg-[#F7F7FC] hover:bg-[#f8e5db] shadow-sm hover:shadow-md relative overflow-hidden group'
    ]"
  >
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent rounded-2xl z-10"></div>
    <img class="h-full w-full drop-shadow-category rounded-2xl object-cover group-hover:scale-105 transition-transform duration-300" :src="category.thumb" alt="category" />
    <h3 class="text-sm font-normal text-white relative z-20 px-2 -mt-12 drop-shadow-lg">{{ category.name }}</h3>
  </router-link>
  
  <!-- <router-link
    :class="[
      checkIsQueryAndRouteQuerySame(category.slug) 
        ? 'menu-category-active bg-primary shadow-lg scale-105' 
        : 'border-transparent',
      'flex flex-col items-center text-center gap-4 c-h-30 rounded-2xl border-b-2 transition-all duration-300 bg-[#F7F7FC] hover:bg-[#f8e5db] shadow-sm hover:shadow-md relative overflow-hidden group'
    ]"
    v-else-if="design === categoryDesignEnum.SECOND"
    :to="{ name: 'frontend.menu', query: { s: category.slug } }"
  >

    <div 
      :class="[
        checkIsQueryAndRouteQuerySame(category.slug) 
          ? '' 
          : '',
        'absolute inset-0 rounded-2xl z-[9999] transition-all duration-300 bg-gradient-to-t from-black/60 via-black/20 to-transparent group-hover:bg-black/40'
      ]"
    ></div>
    <img 
      class="drop-shadow-category relative z-20 group-hover:scale-110 h-full w-full rounded-2xl object-cover transition-transform duration-300" 
      :src="category.thumb" 
      alt="category" 
    />
    <h3 
      :class="[
        checkIsQueryAndRouteQuerySame(category.slug) 
          ? 'text-white font-normal' 
          : 'text-white font-normal',
        'ttext-sm font-normal text-white relative z-20 px-2  -mt-12 drop-shadow-lg'
      ]"
    >
      {{ category.name }}
    </h3>
  </router-link> -->
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
