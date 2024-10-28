<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    pagi: Object,
});

</script>
<template>
    <div class='py-2'>
        <div class=" inline-block">
            <Link v-for="(link, index) in pagi.links" :href="link.url ?? '#'" v-html="link.label"
                class="px-2 py-2 border border-gray-200 first:rounded-l-md last:rounded-r-md -m-px first:aspect-auto last:aspect-auto aspect-square"
                :class="{
                    'text-gray-200': link.url === null,
                    'bg-blue-400 text-white font-bold': link.active,
                }">
            </Link>
        </div>
    </div>
</template>