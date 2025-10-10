<template>
    <transition name="fade">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            @click="closeModal"
        >
            <div
                class="bg-white w-full max-w-md rounded-2xl p-6 mx-4 sm:mx-0 relative animate-fade-in"
                @click.stop
            >
                <button
                    class="absolute top-3 right-3 text-gray-400 hover:text-black text-xl"
                    @click="closeModal"
                >
                    &times;
                </button>

                <h2
                    class="text-orange-600 text-sm sm:text-lg font-semibold mb-4"
                >
                    Delivery Address
                </h2>

                <p
                    class="text-center text-black font-semibold text-[13px] sm:text-sm mb-4 leading-relaxed py-[8px] sm:py-[30px]"
                >
                    We Are Not Delivering To Your Location Yet, Join Our
                    Waitlist To Be Notified When We Are Here.
                </p>
                <button
                    @click="goToWaitlistPage"
                    class="w-full py-2 bg-orange-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-colors duration-200 hover:bg-orange-700 mb-4"
                >
                    Join Waitlist
                </button>

                <button
                    @click="goToDeliveryAddressModal"
                    class="w-full py-2 bg-orange-600 text-white border-none rounded-lg text-base font-semibold cursor-pointer transition-colors duration-200 hover:bg-orange-700"
                >
                    Try Different Address
                </button>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: "WaitlistModal",
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["close", "go-to-delivery-modal"],

    methods: {
        closeModal() {
            this.$emit("close");
        },

        goToWaitlistPage() {
            this.closeModal();
            window.open("https://yummealsapp.com/#waitlist-form", "_blank");
        },

        goToDeliveryAddressModal() {
            this.closeModal();
            this.$emit("go-to-delivery-modal");
        },
    },
};
</script>

<style scoped>
@keyframes fade-in {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
