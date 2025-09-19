<template>
  <Transition name="modal" appear>
    <div
      v-if="visible"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @click.self="$emit('close')"
    >
      <div class="absolute inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm"></div>

      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300">
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-t-2xl px-6 py-4" style="background: linear-gradient(to right, rgb(242, 91, 10), rgb(234, 88, 12))">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <h3 class="text-white font-bold text-xl">{{ $t("label.referrals") }}</h3>
            </div>
            <button
              @click="$emit('close')"
              class="text-white/80 hover:text-white hover:bg-white/10 rounded-full p-2 transition-all duration-200 transform hover:scale-110"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <div v-if="loading" class="text-center py-12">
            <LoadingContentComponent :props="{ isActive: loading }" />
          </div>

          <div v-else-if="referrals && referrals.length > 0" class="space-y-4">
            <div class="flex items-center justify-between mb-4">
              <p class="text-sm text-gray-500">{{ referrals.length }} {{ referrals.length === 1 ? 'referral' : 'referrals' }} found</p>
            </div>

            <div class="max-h-80 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
              <TransitionGroup name="list" tag="div">
                <div
                  v-for="(referral, index) in referrals"
                  :key="referral.id"
                  class="group bg-gradient-to-r from-gray-50 to-gray-100 hover:from-orange-50 hover:to-amber-50 border border-gray-200 hover:border-orange-300 rounded-xl p-4 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-md"
                  :style="{
                    '--hover-from': 'rgba(242, 91, 10, 0.05)',
                    '--hover-to': 'rgba(251, 191, 36, 0.05)',
                    'animationDelay': `${index * 0.1}s`
                  }"
                >
                  <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold shadow-md" style="background: linear-gradient(to bottom right, rgb(242, 91, 10), rgb(234, 88, 12))">
                      {{ referral.name.charAt(0).toUpperCase() }}
                    </div>

                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-gray-900 truncate transition-colors" @mouseover="$event.target.style.color = 'rgb(180, 65, 8)'" @mouseleave="$event.target.style.color = ''">
                        {{ referral.name }}
                      </h4>

                      <div class="mt-2 space-y-1">
                        <div class="flex items-center text-sm text-gray-600">
                          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                          <span class="truncate">{{ referral.email }}</span>
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                          </svg>
                          <span>{{ referral.phone }}</span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                          <span class="text-xs font-medium text-gray-500">{{ $t("label.user_referral_code") }}:</span>
                          <div class="flex items-center space-x-2">
                            <code class="px-2 py-1 rounded-md text-sm font-mono font-semibold" style="background-color: rgba(242, 91, 10, 0.1); color: rgb(180, 65, 8)">
                              {{ referral.referral_code }}
                            </code>
                            <button
                              @click="copyToClipboard(referral.referral_code)"
                              class="text-gray-400 hover:transition-colors p-1 rounded"
                              @mouseover="$event.target.style.color = 'rgb(242, 91, 10)'; $event.target.style.backgroundColor = 'rgba(242, 91, 10, 0.1)'"
                              @mouseleave="$event.target.style.color = ''; $event.target.style.backgroundColor = ''"
                              title="Copy code"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </TransitionGroup>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h4 class="text-sm font-semibold text-gray-900 mb-2">No referrals yet</h4>
            <!-- <p class="text-gray-500">{{ $t("message.no_data_available") }}</p> -->
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script>
import LoadingContentComponent from "../LoadingContentComponent.vue";

export default {
  name: "SmReferralsModalComponent",
  components: {
    LoadingContentComponent, // Register the component
  },
  props: {
    visible: {
      type: Boolean,
      required: true,
    },
    referrals: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text);
        console.log('Copied to clipboard:', text);
      } catch (err) {
        console.error('Failed to copy:', err);
      }
    },
  },
};
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(-20px);
}

.modal-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(-20px);
}

.list-enter-active {
  transition: all 0.6s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-20px);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
  transition: background-color 0.2s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.group {
  animation: slideInUp 0.6s ease forwards;
}

.group:hover .text-gray-400 {
  color: rgb(242, 91, 10);
}

button:focus {
  outline: 2px solid rgb(242, 91, 10);
  outline-offset: 2px;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>