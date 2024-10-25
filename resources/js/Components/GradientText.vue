<script setup>
import { ref } from 'vue';

const props = defineProps({
    fontSize: Number
})
const id = ref((Math.random() * 100000).toFixed(0));

</script>
<template>
    <h1 class="text-center font-mono w-fit mx-auto text-transparent bg-mbti bg-clip-text uppercase font-black">
        <slot />
    </h1>
</template>
