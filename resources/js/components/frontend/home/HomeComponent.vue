<template>
    <LoadingComponent :props="loading" />

    <WaitlistModal
        :key="waitlistKey"
        :isOpen="showWaitlistModal"
        @close="showWaitlistModal = false"
        @go-to-delivery-modal="handleGoToDeliveryModal"
    />

    <DeliveryAddressModal
        :isOpen="showDeliveryModal"
        @close="showDeliveryModal = false"
        @location-covered="handleLocationCovered"
        @location-not-covered="handleNotCovered"
    />

    <VariationModal
        v-if="showVariationModal"
        :item="selectedItem"
        @close="() => (showVariationModal = false)"
        @cart-animation-complete="handleCartItemAdded"
    />

    <!--======== TRACK PART START ===========-->
    <TrackOrderComponent />
    <!--======== TRACK PART END =============-->

    <!--======== BANNER PART START ==========-->
    <SliderComponent />
    <!--======== BANNER PART END ============-->

    <!--======== CATEGORY PART START ========-->
    <section v-if="categories.length > 0" class="mb-12">
        <div class="container">
            <div class="flex items-center justify-between gap-2 mb-6 mt-4">
                <h2 class="text-2xl font-semibold capitalize">
                    {{ $t("label.our_menu") }}
                </h2>
                <router-link
                    :to="{
                        name: 'frontend.menu',
                        query: { s: categoryProps.slug },
                    }"
                    class="rounded-3xl capitalize text-sm leading-6 font-medium py-1 px-3 transition text-white bg-primary hover:text-primary hover:bg-white"
                >
                    {{ $t("button.view_all") }}
                </router-link>
            </div>

            <div class="swiper menu-swiper">
                <CategoryComponent
                    :categories="categories"
                    :design="categoryProps.design"
                />
            </div>
        </div>
    </section>
    <!--======== CATEGORY PART END =========-->

    <!--======== FEATURED ITEM PART =========-->
    <FeaturedItemComponent />
    <!--======== OFFER PART ================-->
    <OfferComponent :limit="limit" />
    <!--======== POPULAR ITEM PART =========-->
    <PopularItemComponent />
</template>

<script>
import SliderComponent from "../../frontend/home/SliderComponent";
import CategoryComponent from "../components/CategoryComponent";
import FeaturedItemComponent from "../home/FeaturedItemComponent";
import PopularItemComponent from "../home/PopularItemComponent";
import OfferComponent from "../components/OfferComponent";
import categoryDesignEnum from "../../../enums/modules/categoryDesignEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";
import TrackOrderComponent from "./TrackOrderComponent";
import DeliveryAddressModal from "../components/DeliveryAddressModal.vue";
import WaitlistModal from "../components/WaitlistModal.vue";
import VariationModal from "../components/button/VariationButton.vue";

export default {
    name: "HomeComponent",
    components: {
        TrackOrderComponent,
        CategoryComponent,
        SliderComponent,
        FeaturedItemComponent,
        PopularItemComponent,
        OfferComponent,
        LoadingComponent,
        DeliveryAddressModal,
        WaitlistModal,
        VariationModal,
    },
    emits: ['cartItemAdded'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            categoryProps: {
                design: categoryDesignEnum.FIRST,
                slug: "",
            },
            showDeliveryModal: false,
            showWaitlistModal: false,
            waitlistKey: 0,
            showVariationModal: false,
            selectedItem: null,
            limit: 10,
        };
    },
    computed: {
        categories() {
            return this.$store.getters["frontendItemCategory/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;

        this.checkDeliveryModalStatus();

        this.$store
            .dispatch("frontendItemCategory/lists", {
                paginate: 0,
                order_column: "sort",
                order_type: "asc",
                status: statusEnum.ACTIVE,
            })
            .finally(() => {
                this.loading.isActive = false;
            });
    },

    methods: {
        checkDeliveryModalStatus() {
            const isLocationCovered = localStorage.getItem("isLocationCovered");

            // Temporarily disabled delivery address modal
            // if (isLocationCovered === null || isLocationCovered === undefined) {
            //     this.showDeliveryModal = true;
            // }
        },

        closeDeliveryModal() {
            this.showDeliveryModal = false;
        },

        handleGoToDeliveryModal() {
            this.showWaitlistModal = false;
            // Temporarily disabled delivery address modal
            // this.showDeliveryModal = true;
        },

        handleLocationCovered() {
            this.showDeliveryModal = false;
            this.showWaitlistModal = false;
        },

        handleNotCovered() {
            this.showDeliveryModal = false;
            this.showWaitlistModal = false;

            setTimeout(() => {
                this.waitlistKey += 1;
                this.showWaitlistModal = true;
            }, 100);
        },

        handleCloseWaitlist() {
            this.showWaitlistModal = false;
        },
        handleCartItemAdded() {
        },
    },

    watch: {
        categories: {
            deep: true,
            handler(category) {
                if (category.length > 0 && category[0].slug !== "undefined") {
                    this.categoryProps.slug = category[0].slug;
                }
            },
        },
    },
};
</script>
