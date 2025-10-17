<template>
  <div class="flex flex-col gap-4">
        <div class="relative">
      <div class="absolute inset-0 flex items-center"><span class="w-full border-t"/></div>
      <div class="relative flex justify-center text-xs uppercase">
        <span class="px-2 bg-white text-[#6E7191]">Or continue with</span>
      </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
      <button type="button" @click="goGoogle" class="w-full h-12 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-800 hover:bg-gray-50 transition">
        <svg class="mr-2 h-4 w-4" viewBox="0 0 48 48">
          <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12 s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C33.643,6.053,29.083,4,24,4C12.955,4,4,12.955,4,24 s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
          <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,16.108,18.961,13,24,13c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657 C33.643,6.053,29.083,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
          <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.197l-6.19-5.238C29.173,35.091,26.715,36,24,36 c-5.192,0-9.596-3.317-11.258-7.946l-6.5,5.02C9.551,39.556,16.227,44,24,44z"/>
          <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.094,5.565 c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.97,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
        </svg>
        <span class="font-normal">Google</span>
      </button>
      <button type="button" @click="goFacebook" class="w-full h-12 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-800 hover:bg-gray-50 transition">
        <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24"><path fill="#1877F2" d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.097 4.388 23.093 10.125 24v-8.437H7.078v-3.49h3.047V9.356c0-3.007 1.792-4.668 4.533-4.668 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.49 0-1.953.927-1.953 1.878v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.093 24 18.097 24 12.073z"/></svg>
        <span class="font-normal">Facebook</span>
      </button>
    </div>
  </div>
</template>
<script>
import ENV from '../../../../config/env';
export default {
  name: 'SocialAuthButtons',
  methods: {
    goGoogle(){ window.location.href = `${ENV.API_URL}/auth/social/google/redirect`; },
    goFacebook(){ window.location.href = `${ENV.API_URL}/auth/social/facebook/redirect`; },
  }
}
</script>
<style scoped>
</style>
