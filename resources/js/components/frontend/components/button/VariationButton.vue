<template>
    <button
        ref="addToCartBtn"
        @click.prevent="handleClick"
        class="product-card-grid-cart-btn add-btn rounded-lg bg-primary hover:bg-white text-white hover:border-primary hover:border hover:!text-primary group"
    >
        <i
            class="lab lab-bag-2 font-fill-white group-hover:!font-fill-primary transition lab-font-size-14"
        ></i>
        <span class="text-xs transition text-white group-hover:!text-primary">
            {{ $t("button.add") }}
        </span>
    </button>
</template>

<script>
import { animateToCart } from '../../utils/animateToCart.js';

export default {
    props: {
        item: Object,
    },
    methods: {
        handleClick() {
            const isLocationCovered = localStorage.getItem("isLocationCovered")
            if (isLocationCovered === "true") {
                this.$emit("variation-click", this.item);
            } else {
                this.$emit("show-location-modal", this.item);
            }
        },
        triggerAnimation() {
            const cartIcon = document.querySelector('[data-cart-icon]');
            animateToCart(this.$refs.addToCartBtn, cartIcon, () => {
                this.$emit('cart-animation-complete');
            });
        },
    },
};
</script>