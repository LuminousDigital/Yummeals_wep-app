<template>
    <VueElementLoading spinner="bar-fade-scale" color="#f25b0a" :active="props.isActive" :is-full-screen="false"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingContentComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>
