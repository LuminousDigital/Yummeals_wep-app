<template>
    <VueElementLoading 
        spinner="bar-fade-scale" 
        color="#f25b0a" 
        :active="props.isActive" 
        :is-full-screen="props.isFullScreen !== false"
        :style="props.style || {}"
        :class="props.class || ''"
    />
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>