<template>
    <button
        @click.prevent="handleClick"
        class="product-card-grid-cart-btn add-btn"
    >
        <i
            class="lab lab-bag-2 font-fill-primary transition lab-font-size-14"
        ></i>
        <span class="text-xs text-primary transition">
            {{ $t("button.add") }}
        </span>
    </button>
</template>

<script>
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
    },
};
</script>
