<script setup>
import { computed, ref, onMounted, onBeforeMount, watch } from 'vue';
import { toBlob, toJpeg } from 'html-to-image';
import GradientText from "@/Components/GradientText.vue";
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    list: {
        type: Object,
        default: {},
    },
    item: {
        type: Object,
        default: {
            main: {
                width: '720px',
                height: '1280px',
                left: 0,
                top: 0,
                image: null,
            },
            elements: [

            ],
        },
    },
});

const list = computed(() => _.get(props.list, 'data'));
const pagi = computed(() => props.list);

const confirmData = ref({});
const confirm = (id) => {
    _.set(confirmData.value, id, true);
}
const confirmed = (id) => {

    return _.get(confirmData.value, id, false);
}
const confirmReset = () => {
    confirmData.value = {};
}

const newTab = (e, url) => {
    e.preventDefault();
    window.open(url);
}

onMounted(() => {

})

</script>
<template>
    <div class="flex flex-row w-lvw">
        <!-- left side bar -->
        <div class="w-64 shrink-0 grow-0 h-lvh px-4 border-r border-gray-300 flex flex-col">
            <div class="grow w-full flex flex-col py-4">
                <Link :href="route('ads-image.create')"
                    class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">创建
                </Link>
            </div>
        </div>
        <!-- main list -->
        <div class="grow p-4 flex flex-col max-w-5xl">
            <div class="grow">
                <div class="flex flex-row flex-wrap gap-5">
                    <transition-group name='list'>
                        <Link @mouseleave="confirmReset" :href="route('ads-image.show', item.id)"
                            v-for="(item, index) in list" :key="item.id"
                            class="w-32 aspect-square bg-contain bg-no-repeat bg-center border border-gray-100 rounded relative group"
                            :style="`background-image:url(${item.url})`">
                        <div class="absolute right-1 top-1 flex flex-row">
                            <a target="_blank" :href="route('ads-image.down', item.id)"
                                @click="newTab($event, route('ads-image.down', item.id))"
                                class="px-2 py-0.5 text-sm text-white bg-black/80 ring-1 ring-white rounded-sm group-hover:visible invisible">
                                下载
                            </a>
                            <button v-if="!confirmed(item.id)" @click.prevent="confirm(item.id)"
                                class="px-2 py-0.5 text-sm text-white bg-red-500/60 ring-1 ring-white rounded-sm group-hover:visible invisible">删除</button>
                            <Link v-else method="delete" :href="route('ads-image.destroy', item.id)" :only="['list']"
                                as="button" type="button"
                                class="px-2 py-0.5 text-sm text-white bg-red-500/90 ring-1 ring-white rounded-sm group-hover:visible invisible">
                            确认
                            </Link>
                        </div>
                        </Link>
                    </transition-group>
                </div>
            </div>
            <div class="w-full border-t-2 border-gray-300 border-dashed pt-4 grid items-center">
                <Pagination :pagi="pagi" class="mx-auto" />
            </div>
        </div>
    </div>
</template>
<style>
.list-move,
/* 对移动中的元素应用的过渡 */
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: scale(0);
}

/* 确保将离开的元素从布局流中删除
  以便能够正确地计算移动的动画。 */
.list-leave-active {
    position: absolute;
}
</style>