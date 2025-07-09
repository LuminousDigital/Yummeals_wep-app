<template>
  <transition name="slide-fade">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur">
      <div class="bg-white w-full max-w-md rounded-2xl p-6 mx-4 sm:mx-0 animate-slide-up relative">
        <button @click="close" class="absolute top-3 right-3 text-gray-400 hover:text-black text-xl">&times;</button>

        <h2 class="text-orange-600 text-lg font-semibold mb-4">Delivery Address</h2>
        <img :src="imageSrc" alt="Delivery" class="w-40 mx-auto mb-4" />

        <p class="text-gray-600 text-sm mb-2 text-center">Enter Delivery Address</p>
        <div @click="selectAddress" class="flex items-center gap-2 p-3 rounded-full bg-gray-100 cursor-pointer">
          <img src="/images/delivery/Vector.png" alt="Location Icon" class="w-5 h-5 object-contain" />
          <span class="text-sm text-gray-700">Delivery Address</span>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "DeliveryAddressModal",
  props: {
    isOpen: Boolean,
  },
  emits: ['close'],
  data() {
    return {
      imageSrc: "/images/delivery/delivery.png"
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    selectAddress() {
      alert("Location selection will go here."); // Later: hook this to maps / address picker
    }
  }
}
</script>

<style scoped>
@keyframes slide-up {
  0% {
    transform: translateY(100%);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}
</style>
